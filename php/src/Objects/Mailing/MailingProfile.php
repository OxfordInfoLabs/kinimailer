<?php


namespace Kinimailer\Objects\Mailing;


use Kiniauth\Traits\Account\AccountProject;

/**
 * @table km_mailing_profile
 * @generate
 */
class MailingProfile extends MailingProfileSummary {

    // Add account project standard attributes
    use AccountProject;

    /**
     * MailingProfile constructor.
     *
     * @param MailingProfileSummary $mailingProfileSummary
     * @param string $projectKey
     * @param integer $accountId
     */
    public function __construct($mailingProfileSummary, $projectKey = null, $accountId = null) {
        if ($mailingProfileSummary)
            parent::__construct($mailingProfileSummary->getTitle(), $mailingProfileSummary->getFromAddress(), $mailingProfileSummary->getReplyToAddress(), $mailingProfileSummary->getId());
        $this->projectKey = $projectKey;
        $this->accountId = $accountId;
    }


    /**
     * @return MailingProfileSummary
     */
    public function returnSummary() {
        return new MailingProfileSummary($this->getTitle(), $this->getFromAddress(), $this->getReplyToAddress(), $this->getId());
    }

}