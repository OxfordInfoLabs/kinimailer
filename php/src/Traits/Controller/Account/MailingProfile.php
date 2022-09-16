<?php


namespace Kinimailer\Traits\Controller\Account;


use Kiniauth\Objects\Account\Account;
use Kinikit\Core\Logging\Logger;
use Kinimailer\Objects\Mailing\MailingProfileSummary;
use Kinimailer\Services\Mailing\MailingProfileService;

trait MailingProfile {


    /**
     * @var MailingProfileService
     */
    private $mailingProfileService;

    /**
     * MailingProfile constructor.
     *
     * @param MailingProfileService $mailingProfileService
     */
    public function __construct($mailingProfileService) {
        $this->mailingProfileService = $mailingProfileService;
    }


    /**
     * Filter mailing profiles using search method
     *
     * @http GET /
     *
     * @param string $search
     * @param null $projectKey
     * @param int $offset
     * @param int $limit
     * @param string $accountId
     *
     * @return MailingProfileSummary[]
     */
    public function filterMailingProfiles($search = "", $projectKey = null, $offset = 0, $limit = 10) {
        return $this->mailingProfileService->filterMailingProfiles($search, $projectKey, $offset, $limit);
    }


    /**
     * Save the mailing profile
     *
     * @http POST /
     *
     * @param MailingProfileSummary $mailingProfileSummary
     * @param string $projectKey
     *
     * @return int
     */
    public function saveMailingProfile($mailingProfileSummary, $projectKey = null) {
        return $this->mailingProfileService->saveMailingProfile($mailingProfileSummary, $projectKey);
    }

    /**
     * Remove a mailing profile by id
     *
     * @http DELETE /$mailingProfileId
     *
     * @param int $mailingProfileId
     */
    public function removeMailingProfile($mailingProfileId) {
        $this->mailingProfileService->removeMailingProfile($mailingProfileId);
    }


}
