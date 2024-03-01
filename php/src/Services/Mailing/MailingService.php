<?php

namespace Kinimailer\Services\Mailing;

use Kiniauth\Objects\Account\Account;
use Kiniauth\Objects\Attachment\AttachmentSummary;
use Kiniauth\Objects\Attachment\Attachment;
use Kiniauth\Services\Attachment\AttachmentService;
use Kiniauth\Services\Communication\Email\EmailService;
use Kiniauth\Services\Workflow\Task\LongRunning\LongRunningTask;
use Kinikit\Core\Communication\Email\Attachment\StringEmailAttachment;
use Kinikit\Core\Communication\Email\Email;
use Kinikit\Core\Stream\File\ReadOnlyFileStream;
use Kinikit\Core\Util\ObjectArrayUtils;
use Kinikit\MVC\Request\FileUpload;
use Kinikit\Persistence\ORM\Exception\ObjectNotFoundException;
use Kinimailer\Objects\Mailing\Mailing;
use Kinimailer\Objects\Mailing\MailingEmail;
use Kinimailer\Objects\Mailing\MailingLogEntry;
use Kinimailer\Objects\Mailing\MailingLogSet;
use Kinimailer\Objects\Mailing\MailingSummary;
use Kinimailer\Objects\MailingList\MailingListSubscriber;
use Kinimailer\Objects\Template\Template;
use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;
use Kinimailer\Services\MailingList\MailingListService;
use Kinimailer\Services\Template\TemplateService;
use Kinimailer\ValueObjects\Mailing\AdhocMailing;

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
     * @var AttachmentService
     */
    private $attachmentService;

    /**
     * MailingService constructor.
     *
     * @param EmailService $emailService
     * @param TemplateService $templateService
     * @param MailingListService $mailingListService
     * @param AttachmentService $attachmentService
     */
    public function __construct($emailService, $templateService, $mailingListService, $attachmentService) {
        $this->emailService = $emailService;
        $this->templateService = $templateService;
        $this->mailingListService = $mailingListService;
        $this->attachmentService = $attachmentService;
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

        if ($mailing->getId()) {
            $savedMailing = Mailing::fetch($mailing->getId());
            if ($savedMailing->getStatus() != Mailing::STATUS_SENDING)
                $mailing->setStatus($mailing->getScheduledTask() ? Mailing::STATUS_SCHEDULED : Mailing::STATUS_DRAFT);
        } else {
            $mailing->setStatus($mailing->getScheduledTask() ? Mailing::STATUS_SCHEDULED : Mailing::STATUS_DRAFT);
        }

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
     * Upload attachments to a contact
     *
     * @param integer $contactId
     * @param FileUpload[] $uploadedFiles
     *
     * @return void
     */
    public function attachUploadedFilesToMailing($mailingId, $uploadedFiles) {

        // Check access to contact first
        $mailing = Mailing::fetch($mailingId);

        foreach ($uploadedFiles as $fileUpload) {
            $attachmentSummary = new AttachmentSummary($fileUpload->getClientFilename(), $fileUpload->getMimeType(),
                "Mailing", $mailing->getId(), null, $mailing->getProjectKey(), $mailing->getAccountId());
            $this->attachmentService->saveAttachment($attachmentSummary, new ReadOnlyFileStream($fileUpload->getTemporaryFilePath()));
        }
    }


    /**
     * Remove an attachment from a contact
     *
     * @param integer $mailingId
     * @param integer $attachmentId
     * @return void
     */
    public function removeAttachmentFromMailing($mailingId, $attachmentId) {

        // Check access to contact first
        Mailing::fetch($mailingId);

        // Remove attachment
        $this->attachmentService->removeAttachment($attachmentId);
    }


    /**
     * Process mailing using the defined rules (i.e. if it has a schedule or not)
     *
     * @param integer $mailingId
     * @param LongRunningTask $longRunningTask
     */
    public function processMailing($mailingId, $longRunningTask = null) {

        /**
         * @var Mailing $mailing
         */
        $mailing = Mailing::fetch($mailingId);


        // Quit while we're ahead if sending
        if ($mailing->getStatus() == Mailing::STATUS_SENDING)
            return;


        // Set sending status
        $mailing->setStatus(Mailing::STATUS_SENDING);
        $mailing->save();
        $mailing = Mailing::fetch($mailing->getId());


        // Grab the template and update with mailing values
        $template = $mailing->getTemplate();
        $template->setParameters($mailing->getTemplateParameters());
        $template->setSections($mailing->getTemplateSections());
        $template->setTitle($mailing->getTitle());

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
                    return $subscriber->getFullEmailAddress();
                }, $subscribers);
                $emailAddresses = array_merge($emailAddresses, $subscriberEmails);

                // Grab subscribers by email
                $subscribersByEmail = array_merge($subscribersByEmail, ObjectArrayUtils::indexArrayOfObjectsByMember("fullEmailAddress", $subscribers));
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

        $attachments = array_map(function ($attachment) {
            /**
             * @var Attachment $attachment
             */
            $attachment = Attachment::fetch($attachment->getId());
            return new StringEmailAttachment($attachment->getContent(), $attachment->getAttachmentFilename(), $attachment->getMimeType());
        }, $mailing->getAttachments() ?? []);

        // Add log entries as we go.
        $logEntries = [];
        foreach ($emailAddresses as $emailAddress) {

            $email = new MailingEmail($fromAddress, $replyToAddress, [$emailAddress], $template, $subscribersByEmail[$emailAddress] ?? null, $attachments);

            $response = $this->emailService->send($email, $mailing->getAccountId());

            $logEntry = new MailingLogEntry($emailAddress, $response->getStatus(), $response->getErrorMessage(), $response->getEmailId(), $logSet->getId());
            $logEntry->save();

            if ($longRunningTask) {
                $logEntries[] = $logEntry;
                $progressData = ["total" => sizeof($emailAddresses), "completed" => $logEntries];
                $longRunningTask->updateProgress($progressData);
            }
        }


        // Update mailing status
        $mailing->setStatus($mailing->getScheduledTask() ? Mailing::STATUS_SCHEDULED : Mailing::STATUS_SENT);
        $mailing->save();

    }


    /**
     * Process an adhoc mailing to a single recipient.
     *
     * @param AdhocMailing $adhocMailing
     * @return null
     */
    public function processAdhocMailing($adhocMailing) {

        /**
         * @var Mailing $mailing
         */
        $mailing = Mailing::fetch($adhocMailing->getMailingId());

        /**
         * @var MailingListSubscriber $subscriber
         */
        $subscriber = new MailingListSubscriber($adhocMailing->getMailingId(), null, $adhocMailing->getEmailAddress(), null, $adhocMailing->getName());

        $ccAddresses = [];
        if ($adhocMailing->getCcAddresses()) {
            foreach (explode(",", $adhocMailing->getCcAddresses()) as $item) {
                $ccAddresses[] = trim($item);
            }
        }

        $bccAddresses = [];
        if ($adhocMailing->getBccAddresses()) {
            foreach (explode(",", $adhocMailing->getBccAddresses()) as $item) {
                $bccAddresses[] = trim($item);
            }
        }


        // Send the single mailing
        $this->sendSingleMailing($mailing, $subscriber, $adhocMailing->getTitle(),
            $adhocMailing->getSections(), $adhocMailing->getParameters(), $adhocMailing->getFromAddress(), $adhocMailing->getReplyToAddress(),
            $ccAddresses,
            $bccAddresses);

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

            $this->sendSingleMailing($mailing, $subscriber);
        }


    }


    /**
     * @param Mailing $mailing
     * @param MailingListSubscriber $toSubscriber
     * @param $customTitle
     * @param TemplateSection[] $customTemplateSections
     * @param TemplateParameter[] $customTemplateParameters
     * @param string $customFromAddress
     * @param string $customReplyToAddress
     * @param array $ccAddresses
     * @param array $bccAddresses
     * @return void
     */
    private function sendSingleMailing($mailing, $toSubscriber, $customTitle = null, $customTemplateSections = [], $customTemplateParameters = [],
                                       $customFromAddress = null, $customReplyToAddress = null, $ccAddresses = [], $bccAddresses = []) {

        // Create the log set
        $logSet = new MailingLogSet($mailing->getId(), [], $mailing->getProjectKey(), $mailing->getAccountId());
        $logSet->save();

        // Grab template and update details
        $template = $mailing->getTemplate();
        $template->setTitle($customTitle ?? $mailing->getTitle());

        // Add mailing sections and any custom ones passed in.
        $template->mergeData($mailing->getTemplateSections() ?? [], $mailing->getTemplateParameters() ?? []);
        $template->mergeData($customTemplateSections ?? [], $customTemplateParameters ?? []);


        $sendAddress = $toSubscriber->getName() ?: "";
        $sendAddress = $sendAddress . ($toSubscriber->getName() ? " <" : "") . $toSubscriber->getEmailAddress() . ($toSubscriber->getName() ? ">" : "");

        $attachments = array_map(function ($attachment) {
            /**
             * @var Attachment $attachment
             */
            $attachment = Attachment::fetch($attachment->getId());
            return new StringEmailAttachment($attachment->getContent(), $attachment->getAttachmentFilename(), $attachment->getMimeType());
        }, $mailing->getAttachments() ?? []);


        // Derive from and to addresses
        $fromAddress = $customFromAddress ?? ($mailing->getMailingProfile() ? $mailing->getMailingProfile()->getFromAddress() : null) ?? null;
        $replyToAddress = $customReplyToAddress ?? ($mailing->getMailingProfile() ? $mailing->getMailingProfile()->getReplyToAddress() : null) ?? null;


        // Send the email
        $email = new MailingEmail($fromAddress, $replyToAddress, [$sendAddress], $template, $toSubscriber, $attachments, $ccAddresses, $bccAddresses);

        $response = $this->emailService->send($email, $mailing->getAccountId());

        // Create a log entry
        $logEntry = new MailingLogEntry($sendAddress, $response->getStatus(), $response->getErrorMessage(), $response->getEmailId(), $logSet->getId());
        $logEntry->save();
    }


}
