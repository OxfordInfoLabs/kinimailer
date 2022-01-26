<?php


namespace Kinimailer\Objects\MailingList;

use Kinikit\Persistence\ORM\ActiveRecord;

/**
 * @table km_mailing_list_subscriber
 * @generate
 */
class MailingListSubscriber extends ActiveRecord {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     * @required
     */
    private $mailingListId;


    /**
     * @var integer
     */
    private $userId;


    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $emailAddress;


    /**
     * @var string
     */
    private $mobileNumber;

    /**
     * MailingListSubscriber constructor.
     *
     * @param int $mailingListId
     * @param int $userId
     * @param string $emailAddress
     * @param string $mobileNumber
     * @param string $name
     */
    public function __construct($mailingListId, $userId = null, $emailAddress = null, $mobileNumber = null, $name = null) {
        $this->mailingListId = $mailingListId;
        $this->userId = $userId;
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->mobileNumber = $mobileNumber;
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
    public function getMailingListId() {
        return $this->mailingListId;
    }

    /**
     * @param int $mailingListId
     */
    public function setMailingListId($mailingListId) {
        $this->mailingListId = $mailingListId;
    }

    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
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


}