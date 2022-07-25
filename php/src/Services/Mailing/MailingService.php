<?php

namespace Kinimailer\Services\Mailing;

use Kiniauth\Objects\Account\Account;
use Kinikit\Persistence\ORM\Exception\ObjectNotFoundException;
use Kinimailer\Objects\Mailing\Mailing;
use Kinimailer\Objects\Mailing\MailingSummary;
use Kinimailer\Objects\Template\Template;

class MailingService {


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

}
