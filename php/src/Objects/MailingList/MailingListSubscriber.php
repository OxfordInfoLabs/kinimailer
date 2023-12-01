<?php


namespace Kinimailer\Objects\MailingList;

use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Security\Hash\HashProvider;
use Kinikit\Core\Security\Hash\SHA512HashProvider;
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
     * @var string
     */
    private $unsubscribeKey;

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

        /**
         * @var HashProvider $hashProvider
         */
        $hashProvider = Container::instance()->get(SHA512HashProvider::class);
        $this->unsubscribeKey = $hashProvider->generateHash($this->emailAddress . $this->userId . rand(0, 2000000));
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
     * Get full email address with name prefix
     *
     * @return string|null
     */
    public function getFullEmailAddress() {
        if ($this->getName()) {
            return $this->getName() . "<" . $this->getEmailAddress() . ">";
        } else {
            return $this->getEmailAddress();
        }
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
    public function getUnsubscribeKey() {
        return $this->unsubscribeKey;
    }

    /**
     * Get an email hash
     *
     * @return string
     */
    public function returnEmailHash() {
        $hashProvider = Container::instance()->get(SHA512HashProvider::class);
        return $hashProvider->generateHash($this->getEmailAddress());
    }

    /**
     * Get a mobile hash
     *
     * @return string
     */
    public function returnMobileHash() {
        $hashProvider = Container::instance()->get(SHA512HashProvider::class);
        return $hashProvider->generateHash($this->getMobileNumber());
    }


}