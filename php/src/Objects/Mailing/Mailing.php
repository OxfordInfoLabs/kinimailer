<?php

namespace Kinimailer\Objects\Mailing;

use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTask;
use Kiniauth\Traits\Account\AccountProject;
use Kinikit\Core\Logging\Logger;
use Kinimailer\Objects\Template\Template;


/**
 * @table km_mailing
 * @generate
 */
class Mailing extends MailingSummary {

    // Add account project standard attributes
    use AccountProject;

    /**
     * @var Template
     * @manyToOne
     * @parentJoinColumns template_id
     */
    protected $template;


    /**
     * @var MailingProfile
     * @manyToOne
     * @parentJoinColumns mailing_profile_id
     */
    protected $mailingProfile;


    /**
     * @var ScheduledTask
     * @oneToOne
     * @childJoinColumns configuration,task_identifier=mailing,description=Mailing
     * @saveCascade
     */
    protected $scheduledTask;


    /**
     * @param MailingSummary $mailingSummary
     * @param string $projectKey
     * @param integer $accountId
     */
    public function __construct($mailingSummary, $projectKey = null, $accountId = null) {
        if ($mailingSummary) {

            parent::__construct(
                $mailingSummary->getTitle(),
                $mailingSummary->getTemplateSections(),
                $mailingSummary->getTemplateParameters(),
                $mailingSummary->getTemplate() ? new Template($mailingSummary->getTemplate(), $projectKey, $accountId) : null,
                $mailingSummary->getStatus(),
                $mailingSummary->getMailingListIds(),
                $mailingSummary->getUserIds(),
                $mailingSummary->getEmailAddresses(),
                $mailingSummary->getMailingProfile() ? new MailingProfile($mailingSummary->getMailingProfile(), $projectKey, $accountId) : null,
                $mailingSummary->getScheduledTask() ? new ScheduledTask($mailingSummary->getScheduledTask(), $projectKey, $accountId) : null,
                $mailingSummary->getAttachments() ?? [],
                $mailingSummary->isAllowAdhocTriggerFromOtherAccounts(),
                $mailingSummary->getId()
            );
        }
        $this->projectKey = $projectKey;
        $this->accountId = $accountId;
    }

    /**
     * @return Template
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * @param Template $template
     */
    public function setTemplate($template) {
        $this->template = $template;
    }

    /**
     * @return MailingProfile
     */
    public function getMailingProfile() {
        return $this->mailingProfile;
    }

    /**
     * @param MailingProfile $mailingProfile
     */
    public function setMailingProfile($mailingProfile) {
        $this->mailingProfile = $mailingProfile;
    }

    /**
     * @return ScheduledTask
     */
    public function getScheduledTask() {
        return $this->scheduledTask;
    }

    /**
     * @param ScheduledTask $scheduledTask
     */
    public function setScheduledTask($scheduledTask) {
        $this->scheduledTask = $scheduledTask;
    }


    /**
     * @return MailingSummary
     */
    public function returnSummary() {
        return new MailingSummary(
            $this->title,
            $this->templateSections,
            $this->templateParameters,
            $this->template ? $this->template->returnSummary() : null,
            $this->status,
            $this->mailingListIds,
            $this->userIds,
            $this->emailAddresses,
            $this->mailingProfile ? $this->mailingProfile->returnSummary() : null,
            $this->scheduledTask ? $this->scheduledTask->returnSummary() : null,
            $this->attachments,
            $this->allowAdhocTriggerFromOtherAccounts,
            $this->id
        );
    }
}
