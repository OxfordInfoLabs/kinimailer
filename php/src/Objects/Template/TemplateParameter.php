<?php

namespace Kinimailer\Objects\Template;

class TemplateParameter {

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
     */
    private $value;

    const TYPE_TEXT = "TEXT";
    const TYPE_DATE = "DATE";
    const TYPE_DATE_TIME = "DATE_TIME";


    /**
     * @param $key
     * @param $title
     * @param $type
     * @param $value
     */
    public function __construct($key = null, $title = null, $type = null, $value = null) {
        $this->key = $key;
        $this->title = $title;
        $this->type = $type;
        $this->value = $value;
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
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
    }


}
