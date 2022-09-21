<?php

namespace Kinimailer\Objects\Mailing;

use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTask;
use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTaskSummary;
use Kinikit\Persistence\ORM\ActiveRecord;
use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;
use Kinimailer\Objects\Template\TemplateSummary;

class MailingSummary extends ActiveRecord {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var TemplateSection[]
     * @json
     * @sqlType longtext
     */
    protected $templateSections;

    /**
     * @var TemplateParameter[]
     * @json
     * @sqlType longtext
     */
    protected $templateParameters;

    /**
     * @var TemplateSummary
     */
    protected $template;

    /**
     * @var string
     */
    protected $status;


    /**
     * @var mixed
     * @json
     * @sqlType longtext
     */
    protected $mailingListIds;

    /**
     * @var mixed
     * @json
     * @sqlType longtext
     */
    protected $userIds;

    /**
     * @var mixed
     * @json
     * @sqlType longtext
     */
    protected $emailAddresses;

    /**
     * @var MailingProfileSummary
     */
    protected $mailingProfile;

    /**
     * @var ScheduledTaskSummary
     */
    protected $scheduledTask;


    /**
     * @var MailingLogSetSummary[]
     * @oneToMany
     * @childJoinColumns mailing_id
     * @readOnly
     */
    protected $logSets;


    const STATUS_DRAFT = "draft";
    const STATUS_SENDING = "sending";
    const STATUS_SCHEDULED = "scheduled";
    const STATUS_SENT = "sent";


    /**
     * @param int $id
     * @param string $title
     * @param TemplateSection[] $templateSections
     * @param TemplateParameter[] $templateParameters
     * @param TemplateSummary $template
     * @param string $status
     * @param mixed $mailingListIds
     * @param mixed $userIds
     * @param mixed $emailAddresses
     * @param MailingProfileSummary $mailingProfile
     * @param ScheduledTaskSummary $scheduledTask
     */
    public function __construct($title = null, $templateSections = null, $templateParameters = null, $template = null, $status = self::STATUS_DRAFT, $mailingListIds = null, $userIds = null, $emailAddresses = null, $mailingProfile = null, $scheduledTask = null, $id = null) {
        $this->id = $id;
        $this->title = $title;
        $this->templateSections = $templateSections;
        $this->templateParameters = $templateParameters;
        $this->template = $template;
        $this->status = $status;
        $this->mailingListIds = $mailingListIds;
        $this->userIds = $userIds;
        $this->emailAddresses = $emailAddresses;
        $this->mailingProfile = $mailingProfile;
        $this->scheduledTask = $scheduledTask;
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
     * @return TemplateSection[]
     */
    public function getTemplateSections() {
        return $this->templateSections;
    }

    /**
     * @param TemplateSection[] $templateSections
     */
    public function setTemplateSections($templateSections) {
        $this->templateSections = $templateSections;
    }

    /**
     * @return TemplateParameter[]
     */
    public function getTemplateParameters() {
        return $this->templateParameters;
    }

    /**
     * @param TemplateParameter[] $templateParameters
     */
    public function setTemplateParameters($templateParameters) {
        $this->templateParameters = $templateParameters;
    }

    /**
     * @return TemplateSummary
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * @param TemplateSummary $template
     */
    public function setTemplate($template) {
        $this->template = $template;
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
     * @return mixed
     */
    public function getMailingListIds() {
        return $this->mailingListIds;
    }

    /**
     * @param mixed $mailingListIds
     */
    public function setMailingListIds($mailingListIds) {
        $this->mailingListIds = $mailingListIds;
    }

    /**
     * @return mixed
     */
    public function getUserIds() {
        return $this->userIds;
    }

    /**
     * @param mixed $userIds
     */
    public function setUserIds($userIds) {
        $this->userIds = $userIds;
    }

    /**
     * @return mixed
     */
    public function getEmailAddresses() {
        return $this->emailAddresses;
    }

    /**
     * @param mixed $emailAddresses
     */
    public function setEmailAddresses($emailAddresses) {
        $this->emailAddresses = $emailAddresses;
    }

    /**
     * @return MailingProfileSummary
     */
    public function getMailingProfile() {
        return $this->mailingProfile;
    }

    /**
     * @param MailingProfileSummary $mailingProfile
     */
    public function setMailingProfile($mailingProfile) {
        $this->mailingProfile = $mailingProfile;
    }

    /**
     * @return ScheduledTaskSummary
     */
    public function getScheduledTask() {
        return $this->scheduledTask;
    }

    /**
     * @param ScheduledTaskSummary $scheduledTask
     */
    public function setScheduledTask($scheduledTask) {
        $this->scheduledTask = $scheduledTask;
    }

    /**
     * @return MailingLogSetSummary[]
     */
    public function getLogSets() {
        return $this->logSets;
    }

    /**
     * @param MailingLogSetSummary[] $logSets
     */
    public function setLogSets($logSets) {
        $this->logSets = $logSets;
    }


}
