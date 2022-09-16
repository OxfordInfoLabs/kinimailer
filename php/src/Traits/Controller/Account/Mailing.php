<?php

namespace Kinimailer\Traits\Controller\Account;

use Kiniauth\Objects\Account\Account;
use Kinikit\Core\Logging\Logger;
use Kinimailer\Objects\Mailing\MailingSummary;
use Kinimailer\Services\Mailing\MailingService;

trait Mailing {

    /**
     * @var MailingService
     */
    private $mailingService;

    /**
     * @param MailingService $mailingService
     */
    public function __construct($mailingService) {
        $this->mailingService = $mailingService;
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
     * @param MailingSummary $mailingSummary
     * @param $projectKey
     * @param $accountId
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
    public function removeTemplate($id) {
        $this->mailingService->removeMailing($id);
    }
}
