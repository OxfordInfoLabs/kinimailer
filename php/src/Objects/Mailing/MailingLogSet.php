<?php


namespace Kinimailer\Objects\Mailing;


use Kiniauth\Traits\Account\AccountProject;

/**
 * @table km_mailing_log_set
 * @generate
 */
class MailingLogSet extends MailingLogSetSummary {

    // Add account project
    use AccountProject;

    /**
     * @var MailingLogEntry[]
     * @oneToMany
     * @childJoinColumns log_set_id
     */
    private $logEntries;

    public function __construct($mailingId, $logEntries = [], $projectKey = null, $accountId = null, $timeStamp = null) {
        parent::__construct($mailingId, $timeStamp);
        $this->logEntries = $logEntries;
        $this->projectKey = $projectKey;
        $this->accountId = $accountId;
    }


    /**
     * @return MailingLogEntry[]
     */
    public function getLogEntries() {
        return $this->logEntries;
    }

    /**
     * @param MailingLogEntry[] $logEntries
     */
    public function setLogEntries($logEntries) {
        $this->logEntries = $logEntries;
    }


}