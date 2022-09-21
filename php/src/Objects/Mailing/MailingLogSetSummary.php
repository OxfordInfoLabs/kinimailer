<?php


namespace Kinimailer\Objects\Mailing;


use Kinikit\Persistence\ORM\ActiveRecord;

/**
 * Class MailingLogSetSummary
 * @package Kinimailer\Objects\Mailing
 *
 * @table km_mailing_log_set
 */
class MailingLogSetSummary extends ActiveRecord {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $mailingId;


    /**
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * MailingLogSetSummary constructor.
     *
     * @param int $mailingId
     * @param \DateTime $timestamp
     */
    public function __construct($mailingId, $timestamp = null) {
        $this->mailingId = $mailingId;
        $this->timestamp = $timestamp ?? new \DateTime();
    }


    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMailingId() {
        return $this->mailingId;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp() {
        return $this->timestamp;
    }


}