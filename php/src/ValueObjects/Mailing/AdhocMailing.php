<?php

namespace Kinimailer\ValueObjects\Mailing;

use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;

class AdhocMailing {

    /**
     * @var integer
     */
    private $mailingId;


    /**
     * Name if using a single targeted person
     *
     * @var string
     */
    private $name;

    /**
     * Email address if using a single targeted person
     *
     * @var string
     */
    private $emailAddress;


    /**
     * If this is set, the attached mailing lists will be sent to in addition to
     * the single target if set.
     *
     * @var boolean
     */
    private $sendToMailingLists;


    /**
     * @var TemplateSection[]
     */
    protected $sections;

    /**
     * @var TemplateParameter[]
     */
    protected $parameters;


    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $fromAddress;


    /**
     * @var string
     */
    private $replyToAddress;

    /**
     * @var string
     */
    private $ccAddresses;


    /**
     * @var string
     */
    private $bccAddresses;


    /**
     * @param int $mailingId
     * @param string $name
     * @param string $emailAddress
     * @param $sendToMailingLists
     * @param TemplateSection[] $sections
     * @param TemplateParameter[] $parameters
     * @param string $title
     * @param string $fromAddress
     * @param string $replyToAddress
     * @param string $ccAddresses
     * @param string $bccAddresses
     */
    public function __construct($mailingId, $name, $emailAddress, $sendToMailingLists = false, array $sections = [], array $parameters = [], $title = null, $fromAddress = null, $replyToAddress = null, $ccAddresses = [], $bccAddresses = []) {
        $this->mailingId = $mailingId;
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->sections = $sections;
        $this->parameters = $parameters;
        $this->title = $title;
        $this->fromAddress = $fromAddress;
        $this->replyToAddress = $replyToAddress;
        $this->ccAddresses = $ccAddresses;
        $this->bccAddresses = $bccAddresses;
        $this->sendToMailingLists = $sendToMailingLists;
    }


    /**
     * @return int
     */
    public function getMailingId() {
        return $this->mailingId;
    }

    /**
     * @param int $mailingId
     */
    public function setMailingId($mailingId) {
        $this->mailingId = $mailingId;
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
     * @return bool|mixed
     */
    public function getSendToMailingLists() {
        return $this->sendToMailingLists;
    }

    /**
     * @param bool|mixed $sendToMailingLists
     */
    public function setSendToMailingLists($sendToMailingLists) {
        $this->sendToMailingLists = $sendToMailingLists;
    }


    /**
     * @return string
     */
    public function getFromAddress() {
        return $this->fromAddress;
    }

    /**
     * @param string $fromAddress
     */
    public function setFromAddress($fromAddress) {
        $this->fromAddress = $fromAddress;
    }

    /**
     * @return string
     */
    public function getReplyToAddress() {
        return $this->replyToAddress;
    }

    /**
     * @param string $replyToAddress
     */
    public function setReplyToAddress($replyToAddress) {
        $this->replyToAddress = $replyToAddress;
    }

    /**
     * @return TemplateSection[]
     */
    public function getSections() {
        return $this->sections;
    }

    /**
     * @param TemplateSection[] $sections
     */
    public function setSections($sections) {
        $this->sections = $sections;
    }

    /**
     * @return TemplateParameter[]
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param TemplateParameter[] $parameters
     */
    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getCcAddresses() {
        return $this->ccAddresses;
    }

    /**
     * @param string $ccAddresses
     */
    public function setCcAddresses($ccAddresses) {
        $this->ccAddresses = $ccAddresses;
    }

    /**
     * @return string
     */
    public function getBccAddresses() {
        return $this->bccAddresses;
    }

    /**
     * @param string $bccAddresses
     */
    public function setBccAddresses($bccAddresses) {
        $this->bccAddresses = $bccAddresses;
    }


}