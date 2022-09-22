<?php

namespace Kinimailer\Objects\Template;

use Kiniauth\Traits\Account\AccountProject;

/**
 * @table km_template
 * @generate
 */
class Template extends TemplateSummary {

    // Add account project standard attributes
    use AccountProject;

    /**
     * @param TemplateSummary $templateSummary
     * @param $projectKey
     * @param $accountId
     */
    public function __construct($templateSummary, $projectKey = null, $accountId = null) {
        if ($templateSummary) {
            parent::__construct($templateSummary->getTitle(), $templateSummary->getSections(), $templateSummary->getParameters(), $templateSummary->getHtml(), $templateSummary->getContentHashSections(), $templateSummary->getId());
        }
        $this->projectKey = $projectKey;
        $this->accountId = $accountId;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @param TemplateSection[] $sections
     */
    public function setSections($sections) {
        $this->sections = $sections;
    }

    /**
     * @param TemplateParameter[] $parameters
     */
    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    /**
     * @param string $html
     */
    public function setHtml($html) {
        $this->html = $html;
    }

    public function returnSummary() {
        return new TemplateSummary($this->getTitle(), $this->getSections(), $this->getParameters(), $this->getHtml(), $this->getContentHashSections(), $this->getId());
    }
}
