<?php

namespace Kinimailer\Objects\Template\TemplateSectionType;

class HTMLTemplateSectionType implements TemplateSectionType {

    /**
     * @var string
     */
    private $value;

    /**
     * Constructor
     *
     * @param string $defaultValue
     */
    public function __construct($defaultValue) {
        $this->value = $defaultValue;
    }


    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value) {
        $this->value = $value;
    }


    /**
     * @return string|void
     */
    public function returnHTML() {
        return $this->value;
    }
}
