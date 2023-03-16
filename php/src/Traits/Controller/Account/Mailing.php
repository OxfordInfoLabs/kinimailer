<?php

namespace Kinimailer\Traits\Controller\Account;

use Kiniauth\Objects\Account\Account;
use Kiniauth\Objects\Workflow\Task\LongRunning\StoredLongRunningTaskSummary;
use Kiniauth\Services\Workflow\Task\LongRunning\LongRunningTaskService;
use Kinikit\Core\Logging\Logger;
use Kinimailer\Objects\Mailing\MailingSummary;
use Kinimailer\Services\Mailing\MailingProcessorLongRunningTask;
use Kinimailer\Services\Mailing\MailingService;

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
