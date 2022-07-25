<?php

namespace Kinimailer\Objects\Template\TemplateSectionType;

/**
 * @implementation html \Kinimailer\Objects\Template\TemplateSectionType\HTMLTemplateSectionType
 */
interface TemplateSectionType {

    /**
     * Return HTML for this section type
     *
     * @return string
     */
    public function returnHTML();

}
