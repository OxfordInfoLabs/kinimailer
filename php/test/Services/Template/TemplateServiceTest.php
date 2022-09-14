<?php

namespace Kinimailer\Test\Services\Template;

use Kiniauth\Test\Services\Security\AuthenticationHelper;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Template\MustacheTemplateParser;
use Kinikit\Core\Testing\MockObject;
use Kinikit\Core\Testing\MockObjectProvider;
use Kinimailer\Objects\Template\Template;
use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;
use Kinimailer\Objects\Template\TemplateSummary;
use Kinimailer\Services\Template\TemplateService;
use Kinimailer\TestBase;

include_once "autoloader.php";

class TemplateServiceTest extends TestBase {

    /**
     * @var TemplateService
     */
    private $templateService;

    /**
     * @var MockObject
     */
    private $templateParser;

    public function setUp(): void {
        $this->templateParser = MockObjectProvider::instance()->getMockInstance(MustacheTemplateParser::class);
        $this->templateService = new TemplateService($this->templateParser);
    }


    public function testCanCreateReadUpdateAndDeleteTemplatesAsAdmin() {
        AuthenticationHelper::login("admin@kinicart.com", "password");

        $template = new TemplateSummary(
            "Test",
            [new TemplateSection("test_section", "Test Section", TemplateSection::TYPE_HTML)],
            [new TemplateParameter("test_param", "Test Parameter", TemplateParameter::TYPE_TEXT)],
            "<html><body><h1>Test Header</h1></body></html>"
        );

        // CREATE
        $templateId = $this->templateService->saveTemplate($template, null, 0);
        $this->assertNotNull($templateId);

        // Check Account
        $checkTemplate = Template::fetch($templateId);
        $this->assertSame(0, $checkTemplate->getAccountId());

        // READ
        $reTemplate = $this->templateService->getTemplate($templateId);
        $this->assertEquals("Test", $reTemplate->getTitle());
        $this->assertEquals("test_section", $reTemplate->getSections()[0]->getKey());
        $this->assertEquals("test_param", $reTemplate->getParameters()[0]->getKey());
        $this->assertEquals("<html><body><h1>Test Header</h1></body></html>", $reTemplate->getHtml());

        // UPDATE
        $update = new TemplateSummary(
            "Test Update",
            [new TemplateSection("test_section", "Test Section", TemplateSection::TYPE_HTML)],
            [new TemplateParameter("test_param", "Test Parameter", TemplateParameter::TYPE_TEXT)],
            "<html><body><h1>Test Header</h1></body></html>"
        );
        $update->setId($templateId);
        $this->templateService->saveTemplate($update, null, 0);
        $this->assertEquals($update->getId(), $templateId);
        $this->assertEquals("Test Update", $update->getTitle());

        // DELETE
        $this->templateService->removeTemplate($templateId);
        $newTemplate = $this->templateService->getTemplate($templateId);
        // Should return a new object, if original not found
        $this->assertNull($newTemplate->getId());
    }

    public function testCanCreateReadUpdateAndDeleteTemplatesAsAccountHolder() {
        AuthenticationHelper::login("sam@samdavisdesign.co.uk", "password");

        $template = new TemplateSummary(
            "Test",
            [new TemplateSection("test_section", "Test Section", TemplateSection::TYPE_HTML)],
            [new TemplateParameter("test_param", "Test Parameter", TemplateParameter::TYPE_TEXT)],
            "<html><body><h1>Test Header</h1></body></html>"
        );

        // CREATE
        $templateId = $this->templateService->saveTemplate($template, null, 1);
        $this->assertNotNull($templateId);

        // Check Account
        $checkTemplate = Template::fetch($templateId);
        $this->assertSame(1, $checkTemplate->getAccountId());

        // READ
        $reTemplate = $this->templateService->getTemplate($templateId);
        $this->assertEquals("Test", $reTemplate->getTitle());
        $this->assertEquals("test_section", $reTemplate->getSections()[0]->getKey());
        $this->assertEquals("test_param", $reTemplate->getParameters()[0]->getKey());
        $this->assertEquals("<html><body><h1>Test Header</h1></body></html>", $reTemplate->getHtml());

        // UPDATE
        $update = new TemplateSummary(
            "Test Update",
            [new TemplateSection("test_section", "Test Section", TemplateSection::TYPE_HTML)],
            [new TemplateParameter("test_param", "Test Parameter", TemplateParameter::TYPE_TEXT)],
            "<html><body><h1>Test Header</h1></body></html>"
        );
        $update->setId($templateId);
        $this->templateService->saveTemplate($update, null, 1);
        $this->assertEquals($update->getId(), $templateId);
        $this->assertEquals("Test Update", $update->getTitle());

        // DELETE
        $this->templateService->removeTemplate($templateId);
        $newTemplate = $this->templateService->getTemplate($templateId);
        // Should return a new object, if original not found
        $this->assertNull($newTemplate->getId());
    }

    public function testCanEvaluateTemplateSummaryAndReturnHTML() {
        AuthenticationHelper::login("sam@samdavisdesign.co.uk", "password");
        $template = new TemplateSummary(
            "Test",
            [new TemplateSection(
                "test_section",
                "Test Section",
                TemplateSection::TYPE_HTML,
                [
                    "value" => "<h1>Hello World</h1>"
                ]
            )],
            [new TemplateParameter(
                "test_param",
                "Test Parameter",
                TemplateParameter::TYPE_TEXT,
                "Hello Param"

            )],
            "ROGER THE RABBIT"
        );

        $this->templateParser->returnValue("parseTemplateText", "I AM TRUE", [
            "ROGER THE RABBIT", [
                "params" => [
                    "test_param" => "Hello Param"
                ],
                "sections" => [
                    "test_section" => "<h1>Hello World</h1>"
                ]
            ]
        ]);

        $result = $this->templateService->evaluateTemplate($template);

        $this->assertEquals("I AM TRUE", $result);

    }

}
