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
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $emailAddress;


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
     * @param int $mailingId
     * @param string $name
     * @param string $emailAddress
     * @param TemplateSection[] $sections
     * @param TemplateParameter[] $parameters
     * @param string $title
     * @param string $fromAddress
     * @param string $replyToAddress
     */
    public function __construct($mailingId, $name, $emailAddress, array $sections, array $parameters, $title, $fromAddress, $replyToAddress) {
        $this->mailingId = $mailingId;
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->sections = $sections;
        $this->parameters = $parameters;
        $this->title = $title;
        $this->fromAddress = $fromAddress;
        $this->replyToAddress = $replyToAddress;
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


}