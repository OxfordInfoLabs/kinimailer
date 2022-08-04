<?php

namespace Kinimailer\Objects\Template\TemplateSectionType;

/**
 * @implementation html \Kinimailer\Objects\Template\TemplateSectionType\HTMLTemplateSectionType
 * @implementation external_html \Kinimailer\Objects\Template\TemplateSectionType\ExternalHTMLTemplateSectionType
 */
interface TemplateSectionType {

    /**
     * Return HTML for this section type
     *
     * @return string
     */
    public function returnHTML();

}
