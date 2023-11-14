<?php

namespace Kinimailer\Traits\Controller\Account;

use Kiniauth\Objects\Account\Account;
use Kiniauth\Objects\Workflow\Task\LongRunning\StoredLongRunningTaskSummary;
use Kiniauth\Services\Workflow\Task\LongRunning\LongRunningTaskService;
use Kinikit\Core\Logging\Logger;
use Kinikit\MVC\Request\FileUpload;
use Kinimailer\Objects\Mailing\MailingSummary;
use Kinimailer\Services\Mailing\MailingProcessorLongRunningTask;
use Kinimailer\Services\Mailing\MailingService;
use Kinimailer\ValueObjects\Mailing\AdhocMailing;

trait Mailing {

    /**
     * @var MailingService
     */
    private $mailingService;


    /**
     * @var LongRunningTaskService
     */
    private $longRunningTaskService;

    /**
     * @param MailingService $mailingService
     * @param LongRunningTaskService $longRunningTaskService
     */
    public function __construct($mailingService, $longRunningTaskService) {
        $this->mailingService = $mailingService;
        $this->longRunningTaskService = $longRunningTaskService;
    }

    /**
     * Get a Mailing object by ID
     *
     * @http GET /$id
     *
     *
     * @param $id
     * @return MailingSummary
     */
    public function getMailing($id) {
        return $this->mailingService->getMailing($id);
    }


    /**
     * Filter Mailings
     *
     * @http GET /
     *
     * @param $filterString
     * @param $projectKey
     * @param $offset
     * @param $limit
     * @param $accountId
     * @return MailingSummary[]
     */
    public function filterTemplates($filterString = "", $projectKey = null, $offset = 0, $limit = 10, $accountId = Account::LOGGED_IN_ACCOUNT) {
        return $this->mailingService->filterMailings($filterString, $projectKey, $offset, $limit, $accountId);
    }


    /**
     * Save a mailing object
     *
     * @http POST /
     *
     * @unsanitise mailingSummary
     *
     * @param MailingSummary $mailingSummary
     * @param $projectKey
     * @param $accountId
     *
     * @return int|null
     */
    public function saveMailing($mailingSummary, $projectKey = null, $accountId = Account::LOGGED_IN_ACCOUNT) {
        return $this->mailingService->saveMailing($mailingSummary, $projectKey, $accountId);
    }


    /**
     * @http POST /attachments/$mailingId
     *
     * @param integer $mailingId
     * @param FileUpload[] $fileUploads
     *
     * @return void
     */
    public function uploadMailingAttachments($mailingId, $fileUploads) {
        $this->mailingService->attachUploadedFilesToMailing($mailingId, $fileUploads);
    }

    /**
     * @http DELETE /attachments/$mailingId/$attachmentId
     *
     * @param integer $mailingId
     * @param integer $attachmentId
     *
     * @return void
     */
    public function removeMailingAttachment($mailingId, $attachmentId) {
        $this->mailingService->removeAttachmentFromMailing($mailingId, $attachmentId);
    }


    /**
     * Delete a Mailing
     *
     * @http DELETE /
     *
     * @param $id
     * @return void
     */
    public function removeMailing($id) {
        $this->mailingService->removeMailing($id);
    }


    /**
     * @http POST /send
     *
     * @param $mailingId
     */
    public function sendMailing($mailingId, $trackingKey, $projectKey = null) {
        $mailingTask = new MailingProcessorLongRunningTask($mailingId, $this->mailingService);
        return $this->longRunningTaskService->startTask("Mailing", $mailingTask, $trackingKey, $projectKey);
    }


    /**
     * Send an adhoc mailing
     *
     * @http POST /sendAdhoc
     * @unsanitise adhocMailing
     *
     * @param AdhocMailing $adhocMailing
     */
    public function sendAdhocMailing($adhocMailing) {
        $this->mailingService->processAdhocMailing($adhocMailing);
    }


    /**
     * Retrieve results for the supplied tracking key
     *
     * @http GET /results/$trackingKey
     *
     * @param $trackingKey
     *
     * @return StoredLongRunningTaskSummary
     */
    public function retrieveMailingResults($trackingKey) {
        return $this->longRunningTaskService->getStoredTaskByTaskKey($trackingKey);
    }
}
