<?php

namespace Kinimailer\Services\MailingList;

use Kiniauth\Objects\Account\Account;
use Kiniauth\Objects\Security\User;
use Kiniauth\Objects\Security\UserSummary;
use Kiniauth\Services\Application\Session;
use Kinimailer\Objects\MailingList\MailingList;
use Kinimailer\Objects\MailingList\MailingListSubscriber;
use Kinimailer\Objects\MailingList\MailingListSummary;
use Kinimailer\ValueObjects\MailingList\GuestMailingListSubscriberPreferences;
use Kinimailer\ValueObjects\MailingList\MailingListSubscriberPreferences;
use Kinimailer\ValueObjects\MailingList\UserMailingListSubscriberPreferences;

/**
 * Service for managing mailing lists
 *
 * Class MailingListService
 */
class MailingListService {

    /**
     * @var Session
     */
    private $session;


    /**
     * MailingListService constructor.
     * @param Session $session
     */
    public function __construct($session) {
        $this->session = $session;
    }


    /**
     * Get a mailing list by id
     *
     * @param $mailingListId
     * @return MailingListSummary
     */
    public function getMailingList($mailingListId) {
        return MailingList::fetch($mailingListId)->returnSummary();
    }

    /**
     * Get a mailing list by key and parent account id
     *
     * @param integer $mailingListKey
     * @param integer $parentAccountId
     *
     * @return MailingListSummary
     *
     * @objectInterceptorDisabled
     */
    public function getMailingListByKey($mailingListKey, $parentAccountId = null) {

        // Grab active parent id if none supplied
        if ($parentAccountId === null)
            $parentAccountId = $this->session->__getActiveParentAccountId();

        $matchingLists = MailingList::filter("WHERE key = ? AND account_id = ?", $mailingListKey, $parentAccountId);
        if (sizeof($matchingLists) > 0) {
            return $matchingLists[0]->returnSummary();
        } else {
            return null;
        }

    }


    /**
     * Return a boolean indicator as to whether the key is available for the supplied mailing list id (or a new one if null supplied).
     *
     * @param string $proposedKey
     * @param integer $proposedMailingListId
     * @param string $accountId
     */
    public function isKeyAvailableForMailingList($proposedKey, $proposedMailingListId = null, $accountId = Account::LOGGED_IN_ACCOUNT) {

        $mailingList = new MailingList(new MailingListSummary($proposedKey, "", "", false, $proposedMailingListId), null, $accountId ?? 0);
        return !sizeof($mailingList->validate());

    }


    /**
     * Save a mailing list
     *
     * @param $mailingListSummary
     * @param string $projectKey
     * @param integer $accountId
     */
    public function saveMailingList($mailingListSummary, $projectKey = null, $accountId = Account::LOGGED_IN_ACCOUNT) {

        // Create a full mailing list object
        $mailingList = new MailingList($mailingListSummary, $projectKey, $accountId ? $accountId : 0);

        // Save it
        $mailingList->save();

        // Return the id of the new / existing item
        return $mailingList->getId();

    }


    /**
     * Remove a mailing list
     *
     * @param $mailingListId
     */
    public function removeMailingList($mailingListId) {
        $mailingList = MailingList::fetch($mailingListId);
        $mailingList->remove();
    }


    /**
     * Update subscription preferences from a passed mailing list preferences object
     *
     * @param MailingListSubscriberPreferences $mailingListSubscriberPreferences
     * @param integer $parentAccountId
     *
     * @objectInterceptorDisabled
     */
    public function updateSubscriptionPreferences($mailingListSubscriberPreferences, $parentAccountId = null) {

        // Resolve whether the subscriber is anonymous or a user.
        $userId = null;
        if ($mailingListSubscriberPreferences instanceof GuestMailingListSubscriberPreferences) {

            // Check for resolving user for email address / mobile number
            $matchingUsers = UserSummary::filter("WHERE parent_account_id = ? AND " . ($mailingListSubscriberPreferences->getEmailAddress() ? "email_address = ?" : "mobile_number = ?"),
                $mailingListSubscriberPreferences->getEmailAddress() ?: $mailingListSubscriberPreferences->getMobileNumber());

            if (sizeof($matchingUsers)) {
                $userId = $matchingUsers[0]->getId();
            }

        } else if ($mailingListSubscriberPreferences instanceof UserMailingListSubscriberPreferences) {
            $userId = $mailingListSubscriberPreferences->getUserId();
        }

        // Grab all mailing lists one at a time by key.
        foreach ($mailingListSubscriberPreferences->getMailingListPreferences() ?? [] as $mailingListKey => $subscribe) {

            // Get the mailing list
            $mailingList = $this->getMailingListByKey($mailingListKey, $parentAccountId);

            // If mailing list is anonymous or the user is not anonymous we can continue.
            if ($mailingList && ($mailingList->isAnonymousSignUp() || $userId)) {

                $mailingListId = $mailingList->getId();

                // Check for an existing sub entry
                $existingSub = null;
                if ($userId) {
                    $existingSubs = MailingListSubscriber::filter("WHERE mailingListId = ? AND userId = ?",
                        $mailingListId, $userId);
                    $existingSub = sizeof($existingSubs) ? $existingSubs[0] : null;
                } else {
                    $existingSubs = MailingListSubscriber::filter("WHERE mailingListId = ? AND " . ($mailingListSubscriberPreferences->getEmailAddress() ? "email_address = ?" : "mobile_number = ?"),
                        $mailingListId, $mailingListSubscriberPreferences->getEmailAddress() ?: $mailingListSubscriberPreferences->getMobileNumber());
                    $existingSub = sizeof($existingSubs) ? $existingSubs[0] : null;
                }

                if ($subscribe && !$existingSub) {

                    $newSubscriber = null;
                    if ($userId) {
                        $newSubscriber = new MailingListSubscriber($mailingListId, $userId);
                    } else {
                        $newSubscriber = new MailingListSubscriber($mailingListId, null,
                            $mailingListSubscriberPreferences->getEmailAddress(), $mailingListSubscriberPreferences->getMobileNumber(),
                            $mailingListSubscriberPreferences->getName());
                    }

                    // Save if we have one
                    if ($newSubscriber)
                        $newSubscriber->save();
                } else if (!$subscribe && $existingSub) {
                    $existingSub->remove();
                }


            }

        }

    }

    /**
     * Unsubscribe to a mailing list by unsubscribe key
     *
     * @param $unsubscribeKey
     */
    public function unsubscribeBykey($unsubscribeKey, $emailHash = null, $mobileHash = null) {
        if (!$emailHash && !$mobileHash)
            return;

        $matches = MailingListSubscriber::filter("WHERE unsubscribe_key = ?", $unsubscribeKey);

        // Compare hash values for data item
        if (sizeof($matches) > 0) {
            if ($matches[0]->returnEmailHash() == $emailHash || $matches[0]->returnMobileHash() == $mobileHash) {
                $matches[0]->remove();
            }
        }

    }


}