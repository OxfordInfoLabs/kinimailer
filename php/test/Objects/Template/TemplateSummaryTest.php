<?php

namespace Kinimailer\Test\Objects\Template;

use Kinimailer\Objects\Template\Template;
use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;
use Kinimailer\Objects\Template\TemplateSummary;
use Kinimailer\TestBase;

include_once "autoloader.php";

class TemplateSummaryTest extends TestBase {


    public function testCanMergeTemplateData() {

        $section1 = new TemplateSection("test", "Test Section", TemplateSection::TYPE_HTML, "An example section");
        $section2 = new TemplateSection("test2", "Test Section 2", TemplateSection::TYPE_HTML, "Another example section");
        $section3 = new TemplateSection("test3", "Test Section 3", TemplateSection::TYPE_HTML, "A final example section");
        $section1Updated = new TemplateSection("test", "Updated Section", TemplateSection::TYPE_HTML, "Updated section");

        $param1 = new TemplateParameter("test", "Test Parameter", TemplateParameter::TYPE_TEXT, "An example parameter");
        $param2 = new TemplateParameter("test2", "Test Parameter 2", TemplateParameter::TYPE_TEXT, "Another example parameter");
        $param3 = new TemplateParameter("test3", "Test Parameter 3", TemplateParameter::TYPE_TEXT, "A final example parameter");
        $param1Updated = new TemplateParameter("test", "Updated Parameter", TemplateParameter::TYPE_TEXT, "Updated parameter");

        $templateSummary = new TemplateSummary("Hello world", [
            $section1,
            $section2
        ], [
            $param1,
            $param2
        ], "Test HTML");


        $templateSummary->mergeData([$section3, $section1Updated], [$param3, $param1Updated]);

        $this->assertEquals(new TemplateSummary("Hello world", [
            $section1Updated,
            $section2,
            $section3
        ], [
            $param1Updated,
            $param2,
            $param3
        ], "Test HTML"), $templateSummary);


    }


}