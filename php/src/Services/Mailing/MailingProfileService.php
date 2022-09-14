<?php


namespace Kinimailer\Services\Mailing;


use Kiniauth\Objects\Account\Account;
use Kinimailer\Objects\Mailing\MailingProfile;
use Kinimailer\Objects\Mailing\MailingProfileSummary;

class MailingProfileService {

    /**
     * Filter mailing profiles using search method
     *
     * @param string $search
     * @param null $projectKey
     * @param int $offset
     * @param int $limit
     * @param string $accountId
     *
     * @return MailingProfileSummary[]
     */
    public function filterMailingProfiles($search = "", $projectKey = null, $offset = 0, $limit = 10, $accountId = Account::LOGGED_IN_ACCOUNT) {

        $query = "WHERE (title LIKE ? OR fromAddress LIKE ? OR replyToAddress LIKE ?)";
        $params = ["%" . $search . "%", "%" . $search . "%", "%" . $search . "%"];

        if ($accountId !== null) {
            $query .= " AND accountId = ?";
            $params[] = $accountId;
        }

        if ($projectKey !== null) {
            $query .= " AND projectKey = ?";
            $params[] = $projectKey;
        }

        $query .= " ORDER BY title LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;


        return array_map(function ($profile) {
            return $profile->returnSummary();
        }, MailingProfile::filter($query, $params));


    }


    /**
     * Save the mailing profile
     *
     * @param MailingProfileSummary mailingProfileSummary
     * @param string $projectKey
     * @param string $accountId
     *
     * @return int
     */
    public function saveMailingProfile($mailingProfileSummary, $projectKey = null, $accountId = Account::LOGGED_IN_ACCOUNT) {
        $profile = new MailingProfile($mailingProfileSummary, $projectKey, $accountId);
        $profile->save();
        return $profile->getId();
    }

    /**
     * Remove a mailing profile by id
     *
     * @param int $mailingProfileId
     */
    public function removeMailingProfile($mailingProfileId) {
        $profile = MailingProfile::fetch($mailingProfileId);
        $profile->remove();
    }

}