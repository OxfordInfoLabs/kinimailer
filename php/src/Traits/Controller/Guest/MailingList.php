<?php

namespace Kinimailer\Traits\Controller\Guest;

use Kinimailer\Services\MailingList\MailingListService;
use Kinimailer\ValueObjects\MailingList\GuestMailingListSubscriber;
use Kinimailer\ValueObjects\MailingList\GuestMailingListSubscriberPreferences;

trait MailingList {

    /**
     * @var MailingListService
     */
    private $mailingListService;

    /**
     * MailingList constructor.
     *
     * @param MailingListService $mailingListService
     */
    public function __construct($mailingListService) {
        $this->mailingListService = $mailingListService;
    }


    /**
     * Subscribe to a single mailing list
     *
     * @http POST /subscribe/$mailingListKey
     *
     * @param string $mailingListKey
     * @param GuestMailingListSubscriber $mailingListSubscriber
     */
    public function subscribeToMailingList($mailingListKey, $mailingListSubscriber) {

        // Create preferences object
        $preferences = new GuestMailingListSubscriberPreferences([
            $mailingListKey => 1
        ], $mailingListSubscriber->getEmailAddress(), $mailingListSubscriber->getMobileNumber(),
            $mailingListSubscriber->getName());

        // Update subscription preferences
        $this->mailingListService->updateSubscriptionPreferences($preferences);

    }

    /**
     * One shot unsubscribe email address from mailing list
     *
     * @http GET /unsubscribe/email/$unsubscribeKey/$emailHash
     *
     * @param string $unsubscribeKey
     * @param string $emailHash
     */
    public function unsubscribeEmailFromMailingList($unsubscribeKey, $emailHash = null) {
        $this->mailingListService->unsubscribeBykey($unsubscribeKey, $emailHash);
    }

    /**
     * One shot unsubscribe email address from mailing list
     *
     * @http GET /unsubscribe/mobile/$unsubscribeKey/$mobileHash
     *
     * @param string $unsubscribeKey
     * @param string $mobileHash
     */
    public function unsubscribeMobileFromMailingList($unsubscribeKey, $mobileHash = null) {
        $this->mailingListService->unsubscribeBykey($unsubscribeKey, null, $mobileHash);
    }


}
