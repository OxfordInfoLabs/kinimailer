<?php

namespace Kinimailer\Objects\Template;

use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Template\MustacheTemplateParser;
use Kinikit\Core\Template\TemplateParser;
use Kinikit\Persistence\ORM\ActiveRecord;
use Kinimailer\Objects\MailingList\MailingListSubscriber;
use Kinimailer\ValueObjects\MailingList\MailingListSubscriberSummary;

class TemplateSummary extends ActiveRecord {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     * @required
     */
    protected $title;

    /**
     * @var TemplateSection[]
     * @json
     * @sqlType longtext
     */
    protected $sections;

    /**
     * @var TemplateParameter[]
     * @json
     * @sqlType longtext
     */
    protected $parameters;

    /**
     * @var string
     * @sqlType longtext
     */
    protected $html;

    /**
     * Array of section keys which form the content hash for this template.
     * if this is supplied as blank array the default hash is used
     *
     * @var string[]
     * @json
     */
    protected $contentHashSections = [];


    /**
     * @param $title
     * @param TemplateSection[] $sections
     * @param TemplateParameter[] $parameters
     * @param $html
     * @param $id
     */
    public function __construct($title = null, $sections = null, $parameters = null, $html = null, $contentHashSections = [], $id = null) {
        $this->title = $title;
        $this->sections = $sections;
        $this->parameters = $parameters;
        $this->html = $html;
        $this->contentHashSections = $contentHashSections;
        $this->id = $id;
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
    public function getHtml() {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml($html) {
        $this->html = $html;
    }

    /**
     * @return string[]
     */
    public function getContentHashSections() {
        return $this->contentHashSections;
    }

    /**
     * @param string[] $contentHashSections
     */
    public function setContentHashSections($contentHashSections) {
        $this->contentHashSections = $contentHashSections;
    }

    /**
     * @param MailingListSubscriber $subscriber
     *
     * @return string
     */
    public function returnEvaluatedTemplateTitle($subscriber) {

        // Generate the model for this template
        $model = $this->generateModel($subscriber);

        // Get the template parser
        $templateParser = Container::instance()->get(MustacheTemplateParser::class);
        return $templateParser->parseTemplateText($this->getTitle(), $model);

    }


    /**
     * @param MailingListSubscriber $subscriber
     *
     * @return string
     */
    public function returnEvaluatedTemplateText($subscriber) {

        // Generate the model for this template
        $model = $this->generateModel($subscriber);

        // Get the template parser
        $templateParser = Container::instance()->get(MustacheTemplateParser::class);
        return $templateParser->parseTemplateText($this->getHtml(), $model);

    }


    /**
     * @param TemplateParser $templateParser
     * @return string
     */
    public function returnEvaluatedContentHashString() {
        $model = $this->generateModel();
        $contentHashString = "";
        foreach ($this->contentHashSections as $contentHashSection) {
            $contentHashString .= $model["sections"][$contentHashSection] ?? "";
        }
        return $contentHashString;
    }


    // Generate the model for this template summary (params and sections).
    private function generateModel($subscriber = null) {

        $params = [];
        foreach ($this->getParameters() ?? [] as $parameter) {
            $params[$parameter->getKey()] = $parameter->getValue();
        }

        $sections = [];
        $templateParser = Container::instance()->get(MustacheTemplateParser::class);
        foreach ($this->getSections() ?? [] as $section) {
            $model = ["params" => $params];
            $sections[$section->getKey()] = $templateParser->parseTemplateText($section->returnHTML(), $model);
        }

        if ($subscriber) {
            $subscriber = new MailingListSubscriberSummary($subscriber);
        }

        return ["params" => $params, "sections" => $sections, "subscriber" => $subscriber];
    }


}
