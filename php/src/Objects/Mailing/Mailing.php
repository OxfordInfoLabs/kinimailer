<?php

namespace Kinimailer\Objects\Mailing;

use Kiniauth\Traits\Account\AccountProject;

/**
 * @table km_mailing
 * @generate
 */
class Mailing extends MailingSummary {

    // Add account project standard attributes
    use AccountProject;


    /**
     * @param MailingSummary $mailingSummary
     * @param string $projectKey
     * @param integer $accountId
     */
    public function __construct($mailingSummary, $projectKey = null, $accountId = null) {
        if ($mailingSummary) {
            parent::__construct(
                $mailingSummary->getId(),
                $mailingSummary->getTitle(),
                $mailingSummary->getKey(),
                $mailingSummary->getTemplateSections(),
                $mailingSummary->getTemplateParameters(),
                $mailingSummary->getTemplateId(),
                $mailingSummary->getStatus(),
                $mailingSummary->getMailingListIds(),
                $mailingSummary->getUserIds(),
                $mailingSummary->getEmailAddresses(),
                $mailingSummary->getMailingProfileId()
            );
        }
        $this->projectKey = $projectKey;
        $this->accountId = $accountId;
    }

    /**
     * @return MailingSummary
     */
    public function returnSummary() {
        return new MailingSummary(
            $this->id,
            $this->title,
            $this->key,
            $this->templateSections,
            $this->templateParameters,
            $this->templateId,
            $this->status,
            $this->mailingListIds,
            $this->userIds,
            $this->emailAddresses,
            $this->mailingProfileId
        );
    }
}
