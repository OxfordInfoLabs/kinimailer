<?php

namespace Kinimailer\Services\Template;

use Kiniauth\Objects\Account\Account;
use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Template\MustacheTemplateParser;
use Kinikit\Core\Template\TemplateParser;
use Kinikit\Persistence\ORM\Exception\ObjectNotFoundException;
use Kinimailer\Objects\MailingList\MailingListSubscriber;
use Kinimailer\Objects\Template\Template;
use Kinimailer\Objects\Template\TemplateSummary;
use Kinimailer\ValueObjects\MailingList\MailingListSubscriberSummary;

class TemplateService {


    /**
     * Return a template summary by ID, or a new one if not found.
     *
     * @param $templateId
     * @return TemplateSummary
     */
    public function getTemplate($templateId) {
        try {
            return Template::fetch($templateId)->returnSummary();
        } catch (ObjectNotFoundException $e) {
            return new TemplateSummary();
        }
    }

    /**
     * Filter the templates by string and project.
     *
     * @param $filterString
     * @param $projectKey
     * @param $offset
     * @param $limit
     * @param $accountId
     * @return TemplateSummary[]
     */
    public function filterTemplates($filterString = "", $projectKey = null, $offset = 0, $limit = 10, $accountId = Account::LOGGED_IN_ACCOUNT) {
        $params = [];
        if ($accountId === null) {
            $query = "WHERE accountId IS NULL";
        } else {
            $query = "WHERE accountId = ?";
            $params[] = $accountId;
        }

        if ($filterString) {
            $query .= " AND title LIKE ?";
            $params[] = "%$filterString%";
        }

        if ($projectKey) {
            $query .= " AND project_key = ?";
            $params[] = $projectKey;
        }

        $query .= " ORDER BY title LIMIT $limit OFFSET $offset";

        return array_map(function ($instance) {
            /** @var Template $instance */
            return $instance->returnSummary();
        }, Template::filter($query, $params));
    }

    /**
     * @param TemplateSummary $templateSummary
     * @param $projectKey
     * @param $accountId
     * @return integer
     */
    public function saveTemplate($templateSummary, $projectKey = null, $accountId = Account::LOGGED_IN_ACCOUNT) {
        $template = new Template($templateSummary, $projectKey, $accountId ? $accountId : 0);

        $template->save();

        return $template->getId();
    }

    /**
     * Delete a template
     *
     * @param $templateId
     * @return void
     */
    public function removeTemplate($templateId) {
        $template = Template::fetch($templateId);
        $template->remove();
    }

    /**
     * Return the evaluated html for the supplied template.
     *
     * @param TemplateSummary $templateSummary
     * @return string
     */
    public function evaluateTemplate($templateSummary) {
        $subscriber = new MailingListSubscriber(1, 1, "joe@bloggs.com", "07777 111111", "Joe Bloggs");
        return $templateSummary->returnEvaluatedTemplateText($subscriber);
    }
}
