<?php


namespace Kinimailer\ValueObjects\MailingList;


use Kinimailer\Objects\MailingList\MailingListSubscriber;

class MailingListSubscriberSummary {

    /**
     * @var string
     */
    private $name;


    /**
     * @var string
     */
    private $organisation;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $mobileNumber;

    /**
     * @var boolean
     */
    private $isUser;

    /**
     * @var string
     */
    private $unsubscribeKey;


    /**
     * @var string
     */
    private $emailHash;

    /**
     * @var string
     */
    private $mobileHash;

    /**
     * MailingListSubscriberSummary constructor.
     *
     * @param MailingListSubscriber $mailingListSubscriber
     */
    public function __construct($mailingListSubscriber) {
        $this->name = $mailingListSubscriber->getName();
        $this->organisation = $mailingListSubscriber->getOrganisation();
        $this->emailAddress = $mailingListSubscriber->getEmailAddress();
        $this->mobileNumber = $mailingListSubscriber->getMobileNumber();
        $this->isUser = $mailingListSubscriber->getUserId() ? true : false;
        $this->unsubscribeKey = $mailingListSubscriber->getUnsubscribeKey();
        $this->emailHash = $mailingListSubscriber->returnEmailHash();
        $this->mobileHash = $mailingListSubscriber->returnMobileHash();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOrganisation() {
        return $this->organisation;
    }

    /**
     * @return string
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getMobileNumber() {
        return $this->mobileNumber;
    }

    /**
     * @return bool
     */
    public function getIsUser() {
        return $this->isUser;
    }

    /**
     * @return string
     */
    public function getUnsubscribeKey() {
        return $this->unsubscribeKey;
    }

    /**
     * @return string
     */
    public function getEmailHash() {
        return $this->emailHash;
    }

    /**
     * @return string
     */
    public function getMobileHash() {
        return $this->mobileHash;
    }


}