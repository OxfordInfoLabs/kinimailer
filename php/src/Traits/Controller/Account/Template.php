<?php

namespace Kinimailer\Traits\Controller\Account;

use Kiniauth\Objects\Account\Account;
use Kinimailer\Objects\Template\TemplateSummary;
use Kinimailer\Services\Template\TemplateService;

trait Template {

    /**
     * @var TemplateService
     */
    private $templateService;

    /**
     * @param TemplateService $templateService
     */
    public function __construct($templateService) {
        $this->templateService = $templateService;
    }

    /**
     * Return a template by ID
     *
     * @http GET /$templateId
     *
     * @param $templateId
     * @return TemplateSummary
     */
    public function getTemplate($templateId) {
        return $this->templateService->getTemplate($templateId);
    }

    /**
     * Filter the templates
     *
     * @http GET /
     *
     * @param $filterString
     * @param $projectKey
     * @param $offset
     * @param $limit
     * @param $accountId
     * @return TemplateSummary[]
     */
    public function filterTemplates($filterString = "", $projectKey = null, $offset = 0, $limit = 10, $accountId = Account::LOGGED_IN_ACCOUNT) {
        return $this->templateService->filterTemplates($filterString, $projectKey, $offset, $limit, $accountId);
    }

    /**
     * Save a Template
     *
     * @http POST /
     * @unsanitise templateSummary
     *
     * @param TemplateSummary $templateSummary
     * @param $projectKey
     * @param $accountId
     * @return int
     */
    public function saveTemplate($templateSummary, $projectKey = null, $accountId = Account::LOGGED_IN_ACCOUNT) {
        return $this->templateService->saveTemplate($templateSummary, $projectKey, $accountId);
    }

    /**
     * Delete a template
     *
     * @http DELETE /
     *
     * @param $templateId
     * @return void
     */
    public function removeTemplate($templateId) {
        $this->templateService->removeTemplate($templateId);
    }

    /**
     * Return the evaluated HTML for the template
     *
     * @http POST /evaluate
     * @unsanitise templateSummary
     *
     * @param TemplateSummary $templateSummary
     * @return string
     */
    public function evaluateTemplate($templateSummary) {
        return $this->templateService->evaluateTemplate($templateSummary);
    }

}
