<?php

namespace Kinimailer\Objects\Mailing;

use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTask;
use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTaskSummary;
use Kinikit\Persistence\ORM\ActiveRecord;
use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;

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
     * @var string
     */
    protected $key;

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
     * @var integer
     */
    protected $templateId;

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
     * @var integer
     */
    protected $mailingProfileId;

    /**
     * @var ScheduledTask
     * @manyToOne
     */
    protected $scheduledTask;

    /**
     * @param int $id
     * @param string $title
     * @param string $key
     * @param TemplateSection[] $templateSections
     * @param TemplateParameter[] $templateParameters
     * @param int $templateId
     * @param string $status
     * @param mixed $mailingListIds
     * @param mixed $userIds
     * @param mixed $emailAddresses
     * @param int $mailingProfileId
     */
    public function __construct($id = null, $title = null, $key = null, $templateSections = null, $templateParameters = null, $templateId = null, $status = null, $mailingListIds = null, $userIds = null, $emailAddresses = null, $mailingProfileId = null, $scheduledTask = null) {
        $this->id = $id;
        $this->title = $title;
        $this->key = $key;
        $this->templateSections = $templateSections;
        $this->templateParameters = $templateParameters;
        $this->templateId = $templateId;
        $this->status = $status;
        $this->mailingListIds = $mailingListIds;
        $this->userIds = $userIds;
        $this->emailAddresses = $emailAddresses;
        $this->mailingProfileId = $mailingProfileId;
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
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key) {
        $this->key = $key;
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
     * @return int
     */
    public function getTemplateId() {
        return $this->templateId;
    }

    /**
     * @param int $templateId
     */
    public function setTemplateId($templateId) {
        $this->templateId = $templateId;
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
     * @return int
     */
    public function getMailingProfileId() {
        return $this->mailingProfileId;
    }

    /**
     * @param int $mailingProfileId
     */
    public function setMailingProfileId($mailingProfileId) {
        $this->mailingProfileId = $mailingProfileId;
    }


}
