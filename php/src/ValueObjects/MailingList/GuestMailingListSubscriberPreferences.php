<?php


namespace Kinimailer\ValueObjects\MailingList;

/**
 * Class DirectMailingListSubscriberPreferences
 * @package Kinimailer\ValueObjects\MailingList
 */
class GuestMailingListSubscriberPreferences extends MailingListSubscriberPreferences {

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
    private $name;

    /**
     * GuestMailingListSubscriberPreferences constructor.
     *
     * @param string[boolean] $mailingListPreferences
     * @param string $emailAddress
     * @param string $mobileNumber
     * @param string $name
     */
    public function __construct($mailingListPreferences, $emailAddress = null, $mobileNumber = null, $name = null) {
        parent::__construct($mailingListPreferences);
        $this->emailAddress = $emailAddress;
        $this->mobileNumber = $mobileNumber;
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


}