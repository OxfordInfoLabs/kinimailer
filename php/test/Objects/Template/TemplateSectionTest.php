<?php

namespace Kinimailer\Test\Objects\Template;

use Kinikit\Core\DependencyInjection\Container;
use Kinimailer\Objects\Template\TemplateSection;
use Kinimailer\Objects\Template\TemplateSectionType\TemplateSectionType;
use Kinimailer\TestBase;

include_once "autoloader.php";

class TemplateSectionTest extends TestBase {

    public function testReturnHTMLReturnsCorrectHTMLForType() {

        $templateSection = new TemplateSection("bingo", "Bingo", "test", [
            "name" => "Test",
            "age" => 33
        ]);

        Container::instance()->addInterfaceImplementation(TemplateSectionType::class, "test", TestTemplateSectionType::class);

        $this->assertEquals("Test-33", $templateSection->returnHTML());
    }

}
