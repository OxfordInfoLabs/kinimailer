<?php

namespace Kinimailer\Objects\Template;

use Kinikit\Persistence\ORM\ActiveRecord;

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
     * @param $title
     * @param TemplateSection[] $sections
     * @param TemplateParameter[] $parameters
     * @param $html
     * @param $id
     */
    public function __construct($title = null, $sections = null, $parameters = null, $html = null, $id = null) {
        $this->title = $title;
        $this->sections = $sections;
        $this->parameters = $parameters;
        $this->html = $html;
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



}
