<?php

namespace Kinimailer\Services\Mailing;

use Kiniauth\Objects\Account\Account;
use Kiniauth\Services\Communication\Email\EmailService;
use Kiniauth\Services\Workflow\Task\LongRunning\LongRunningTask;
use Kinikit\Core\Communication\Email\Email;
use Kinikit\Core\Util\ObjectArrayUtils;
use Kinikit\Persistence\ORM\Exception\ObjectNotFoundException;
use Kinimailer\Objects\Mailing\Mailing;
use Kinimailer\Objects\Mailing\MailingEmail;
use Kinimailer\Objects\Mailing\MailingLogEntry;
use Kinimailer\Objects\Mailing\MailingLogSet;
use Kinimailer\Objects\Mailing\MailingSummary;
use Kinimailer\Objects\MailingList\MailingListSubscriber;
use Kinimailer\Objects\Template\Template;
use Kinimailer\Services\MailingList\MailingListService;
use Kinimailer\Services\Template\TemplateService;

class MailingService {


    /**
     * @var EmailService
     */
    private $emailService;


    /**
     * @var TemplateService
     */
    private $templateService;

    /**
     * @var MailingListService
     */
    private $mailingListService;

    /**
     * MailingService constructor.
     *
     * @param EmailService $emailService
     * @param TemplateService $templateService
     * @param MailingListService $mailingListService
     */
    public function __construct($emailService, $templateService, $mailingListService) {
        $this->emailService = $emailService;
        $this->templateService = $templateService;
        $this->mailingListService = $mailingListService;
    }


    /**
     * Return a Mailing Summary object by ID
     *
     * @param $id
     * @return MailingSummary
     */
    public function getMailing($id) {
        try {
            /** @var Mailing $mailing */
            $mailing = Mailing::fetch($id);
            return $mailing->returnSummary();
        } catch (ObjectNotFoundException $e) {
            return new MailingSummary();
        }
    }


    /**
     * Filter mailings by string, limit, and offset
     *
     * @param $filterString
     * @param $projectKey
     * @param $offset
     * @param $limit
     * @param $accountId
     * @return MailingSummary[]
     */
    public function filterMailings($filterString = "", $projectKey = null, $offset = 0, $limit = 10, $accountId = Account::LOGGED_IN_ACCOUNT) {
        $params = [];
        if ($accountId === null) {
            $query = "WHERE accountId IS NULL";
        } else {
            $query = "WHERE accountId = ?";
            $params[] = $accountId;
        }

        if ($filterString) {
            $query .= " AND title LIKE ?";
            $params[] = "%$filterString%";
        }

        if ($projectKey) {
            $query .= " AND project_key = ?";
            $params[] = $projectKey;
        }

        $query .= " ORDER BY title LIMIT $limit OFFSET $offset";

        return array_map(function ($instance) {
            /** @var Mailing $instance */
            return $instance->returnSummary();
        }, Mailing::filter($query, $params));
    }


    /**
     * Save a Mailing object
     *
     * @param $mailingSummary
     * @param $projectKey
     * @param $accountId
     * @return int|null
     */
    public function saveMailing($mailingSummary, $projectKey = null, $accountId = Account::LOGGED_IN_ACCOUNT) {
        $mailing = new Mailing($mailingSummary, $projectKey, $accountId);
        $mailing->save();
        return $mailing->getId();
    }


    /**
     * Delete a Mailing object
     *
     * @param $id
     * @return void
     */
    public function removeMailing($id) {
        /** @var Mailing $mailing */
        $mailing = Mailing::fetch($id);
        $mailing->remove();
    }


    /**
     * Process mailing using the defined rules (i.e. if it has a schedule or not)
     *
     * @param integer $mailingId
     * @param boolean $runNow
     * @param LongRunningTask $longRunningTaskka_contact
     */
    public function processMailing($mailingId, $runNow = false, $longRunningTask = null) {

        /**
         * @var Mailing $mailing
         */
        $mailing = Mailing::fetch($mailingId);

        // Quit while we're ahead if either sent or sending
        if ($mailing->getStatus() == Mailing::STATUS_SENT || $mailing->getStatus() == Mailing::STATUS_SENDING)
            return;


        // If adhoc or run now passed, run now
        if (!$mailing->getScheduledTask() || $runNow) {

            // Grab the template and update with mailing values
            $template = $mailing->getTemplate();
            $template->setParameters($mailing->getTemplateParameters());
            $template->setSections($mailing->getTemplateSections());

            // If adhoc email addresses, merge these into the set
            $emailAddresses = [];
            if ($mailing->getEmailAddresses()) {
                $emailAddresses = array_merge($emailAddresses, $mailing->getEmailAddresses());
            }

            // Index all subscribers by email for use later
            $subscribersByEmail = [];

            // If mailing list Ids, merge in subscriber email addresses
            if ($mailing->getMailingListIds()) {
                foreach ($mailing->getMailingListIds() as $mailingListId) {
                    $subscribers = $this->mailingListService->getSubscribersForMailingList($mailingListId);
                    $subscriberEmails = array_map(function ($subscriber) {
                        if ($subscriber->getName()) {
                            return $subscriber->getName() . "<" . $subscriber->getEmailAddress() . ">";
                        } else {
                            return $subscriber->getEmailAddress();
                        }
                    }, $subscribers);
                    $emailAddresses = array_merge($emailAddresses, $subscriberEmails);

                    // Grab subscribers by email
                    $subscribersByEmail = array_merge($subscribersByEmail, ObjectArrayUtils::indexArrayOfObjectsByMember("emailAddress", $subscribers));
                }
            }

            // From and reply addresses
            $fromAddress = $mailing->getMailingProfile()->getFromAddress();
            $replyToAddress = $mailing->getMailingProfile()->getReplyToAddress();

            /**
             * Loop through each email and send accordingly
             */

            // Create the log set
            $logSet = new MailingLogSet($mailingId, [], $mailing->getProjectKey(), $mailing->getAccountId());
            $logSet->save();

            // Add log entries as we go.
            $longRunningData = [];
            foreach ($emailAddresses as $emailAddress) {
                $email = new MailingEmail($fromAddress, $replyToAddress, [$emailAddress], $template, $subscribersByEmail[$emailAddress] ?? null);
                $response = $this->emailService->send($email, $mailing->getAccountId());
                $logEntry = new MailingLogEntry($emailAddress, $response->getStatus(), $response->getErrorMessage(), $response->getEmailId(), $logSet->getId());
                $logEntry->save();

                if ($longRunningTask) {
                    $longRunningData[] = $logEntry;
                    $longRunningTask->updateProgress($longRunningData);
                }
            }

        }

        // Update mailing status
        $mailing->setStatus($mailing->getScheduledTask() ? Mailing::STATUS_SCHEDULED : Mailing::STATUS_SENT);
        $mailing->save();

    }


    /**
     * Possibly temporary method for one time mail shots
     *
     * @param $mailingId
     * @param $mailingListKey
     * @param $subscriberEmailAddress
     *
     * @objectInterceptorDisabled
     */
    public function processSingleMailingListSubscriberMailing($mailingId, $mailingListKey, $subscriberEmailAddress) {

        /**
         * @var Mailing $mailing
         */
        $mailing = Mailing::fetch($mailingId);

        // Get Mailing list by key
        $mailingListId = $this->mailingListService->getMailingListByKey($mailingListKey)->getId();

        // Grab the subscriber
        $subscribers = MailingListSubscriber::filter("WHERE mailingListId = ? AND emailAddress = ?", $mailingListId, $subscriberEmailAddress);

        if (sizeof($subscribers)) {

            $subscriber = $subscribers[0];

            // Grab the template and update with mailing values
            $template = $mailing->getTemplate();
            $template->setParameters($mailing->getTemplateParameters());
            $template->setSections($mailing->getTemplateSections());

            // From and reply addresses
            $fromAddress = $mailing->getMailingProfile()->getFromAddress();
            $replyToAddress = $mailing->getMailingProfile()->getReplyToAddress();

            // Create the log set
            $logSet = new MailingLogSet($mailingId, [], $mailing->getProjectKey(), $mailing->getAccountId());
            $logSet->save();

            // Send the email
            $email = new MailingEmail($fromAddress, $replyToAddress, [$subscriberEmailAddress], $template, $subscriber);
            $response = $this->emailService->send($email, $mailing->getAccountId());

            // Create a log entry
            $logEntry = new MailingLogEntry($subscriberEmailAddress, $response->getStatus(), $response->getErrorMessage(), $response->getEmailId(), $logSet->getId());
            $logEntry->save();
        }


    }

}
