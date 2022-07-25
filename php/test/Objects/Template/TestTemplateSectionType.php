<?php

namespace Kinimailer\Test\Objects\Template;

use Kinimailer\Objects\Template\TemplateSectionType\TemplateSectionType;

class TestTemplateSectionType implements TemplateSectionType {

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $age;

    /**
     * @param string $name
     * @param int $age
     */
    public function __construct($name, $age) {
        $this->name = $name;
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getAge() {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age) {
        $this->age = $age;
    }


    /**
     * @return string
     */
    public function returnHTML() {
        return $this->name . "-" . $this->age;
    }
}
