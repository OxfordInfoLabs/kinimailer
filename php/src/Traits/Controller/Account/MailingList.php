<?php

namespace Kinimailer\Traits\Controller\Account;

use Kiniauth\Objects\Account\Account;
use Kinikit\Core\Logging\Logger;
use Kinimailer\Objects\MailingList\MailingListSummary;
use Kinimailer\Services\MailingList\MailingListService;

trait MailingList {

    /**
     * @var MailingListService
     */
    private $mailingListService;

    /**
     * @param MailingListService $mailingListService
     */
    public function __construct($mailingListService) {
        $this->mailingListService = $mailingListService;
    }

    /**
     * Return a mailing list for the supplied id
     *
     * @http GET /$id
     *
     * @param $id
     * @return MailingListSummary
     */
    public function getMailingList($id) {
        return $this->mailingListService->getMailingList($id);
    }

    /**
     * Return a mailing lists for the supplied filters
     *
     * @http GET /
     *
     * @param string $filterString
     * @param $projectKey
     * @param $offset
     * @param $limit
     * @param $accountId
     * @return array|MailingListSummary[]
     */
    public function filterMailingLists($filterString = "", $projectKey = null, $offset = 0, $limit = 10, $accountId = Account::LOGGED_IN_ACCOUNT) {
        return $this->mailingListService->filterMailingLists($filterString, $projectKey, $offset, $limit, $accountId);
    }

    /**
     * Get the subscribers for the supplied mailing list
     *
     * @http GET /subscribers
     *
     * @param $mailingListId
     * @param $projectKey
     * @param $accountId
     * @return mixed
     */
    public function getSubscribersForMailingList($mailingListId, $projectKey = null, $accountId = Account::LOGGED_IN_ACCOUNT) {
        return $this->mailingListService->getSubscribersForMailingList($mailingListId);
    }

    /**
     * Check whether the proposed key is available
     *
     * @http GET /keyAvailable
     *
     * @param $proposedKey
     * @param $proposedMailingListId
     * @param $accountId
     * @return bool
     */
    public function isKeyAvailableForMailingList($proposedKey, $proposedMailingListId = null, $accountId = Account::LOGGED_IN_ACCOUNT) {
        return $this->mailingListService->isKeyAvailableForMailingList($proposedKey, $proposedMailingListId, $accountId);
    }

    /**
     * Create a new mailing list
     *
     * @http POST /
     *
     * @param MailingListSummary $mailingListSummary
     * @param $projectKey
     * @param $accountId
     * @return int|null
     */
    public function saveMailingList($mailingListSummary, $projectKey = null, $accountId = Account::LOGGED_IN_ACCOUNT) {
        return $this->mailingListService->saveMailingList($mailingListSummary, $projectKey, $accountId);
    }


    /**
     * Remove a mailing list using the supplied id
     *
     * @http DELETE /
     *
     * @param $id
     * @return void
     */
    public function removeMailingList($id) {
        $this->mailingListService->removeMailingList($id);
    }

}
