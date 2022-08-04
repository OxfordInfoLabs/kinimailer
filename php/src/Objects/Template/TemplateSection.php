<?php

namespace Kinimailer\Objects\Template;

use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Logging\Logger;
use Kinimailer\Objects\Template\TemplateSectionType\TemplateSectionType;

class TemplateSection {

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     * @json
     * @sqlType longtext
     */
    private $data;


    const TYPE_HTML = "html";
    const TYPE_EXTERNAL_HTML = "external_html";

    /**
     * @param $key
     * @param $title
     * @param $type
     * @param $data
     */
    public function __construct($key = null, $title = null, $type = null, $data = null) {
        $this->key = $key;
        $this->title = $title;
        $this->type = $type;
        $this->data = $data;
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
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data) {
        $this->data = $data;
    }


    /**
     * @return string
     */
    public function returnHTML() {

        // Grab a matching template section type for our type.
        $implementationClass = Container::instance()->getInterfaceImplementationClass(TemplateSectionType::class, $this->type);

        /**
         * @var ObjectBinder $objectBinder
         */
        $objectBinder = Container::instance()->get(ObjectBinder::class);

        // Create a new instance of the appropriate template section type
        $templateSectionType = $objectBinder->bindFromArray($this->data ?? [], $implementationClass);

        return $templateSectionType->returnHTML();

    }

}
