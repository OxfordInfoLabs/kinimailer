<?php


namespace Kinimailer\Objects\Mailing;


use Kinikit\Persistence\ORM\ActiveRecord;

/**
 * Class MailingLogEntry
 * @package Kinimailer\Objects\Mailing
 *
 * @table km_mailing_log_entry
 * @generate
 */
class MailingLogEntry extends ActiveRecord {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $logSetId;

    /**
     * @var string
     * @maxLength 2000
     */
    private $emailAddress;


    /**
     * @var string
     */
    private $mobileNumber;


    /**
     * @var string
     */
    private $status;


    /**
     * @var string
     * @maxLength 2000
     */
    private $failureMessage;

    /**
     * Id of associated item (e.g. stored email)
     *
     * @var int
     */
    private $associatedItemId;


    /**
     * MailingLogEntry constructor.
     *
     * @param string $emailAddress
     * @param string $status
     * @param string $failureMessage
     * @param int $associatedItemId
     */
    public function __construct($emailAddress = null, $status = null, $failureMessage = null, $associatedItemId = null, $logSetId = null) {
        $this->emailAddress = $emailAddress;
        $this->status = $status;
        $this->failureMessage = $failureMessage;
        $this->associatedItemId = $associatedItemId;
        $this->logSetId = $logSetId;
    }


    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getLogSetId() {
        return $this->logSetId;
    }

    /**
     * @param int $logSetId
     */
    public function setLogSetId($logSetId) {
        $this->logSetId = $logSetId;
    }

    /**
     * @return string
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress) {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getMobileNumber() {
        return $this->mobileNumber;
    }

    /**
     * @param string $mobileNumber
     */
    public function setMobileNumber($mobileNumber) {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getFailureMessage() {
        return $this->failureMessage;
    }

    /**
     * @param string $failureMessage
     */
    public function setFailureMessage($failureMessage) {
        $this->failureMessage = $failureMessage;
    }

    /**
     * @return int
     */
    public function getAssociatedItemId() {
        return $this->associatedItemId;
    }

    /**
     * @param int $associatedItemId
     */
    public function setAssociatedItemId($associatedItemId) {
        $this->associatedItemId = $associatedItemId;
    }


}