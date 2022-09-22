<?php


namespace Kinimailer\Test\Objects\Mailing;


use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Security\Hash\HashProvider;
use Kinikit\Core\Security\Hash\SHA512HashProvider;
use Kinimailer\Objects\Mailing\MailingEmail;
use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;
use Kinimailer\Objects\Template\TemplateSummary;
use Kinimailer\TestBase;

include_once "autoloader.php";

class MailingEmailTest extends TestBase {

    public function testContentHashBasedOnFullEvaluatedTemplateContentIfNoContentHashSections() {

        $template = new TemplateSummary("Mr Blobby", [
            new TemplateSection("main", "Main", TemplateSection::TYPE_HTML, ["value" => "<h1>Hello</h1>"]),
            new TemplateSection("content", "Content", TemplateSection::TYPE_HTML, ["value" => "<p>Welcome {{params.name}}</p>"]),
        ], [
            new TemplateParameter("name", "Name", TemplateParameter::TYPE_TEXT, "Joe Bloggs")
        ], "{{sections.main}}{{sections.content}}");

        $email = new MailingEmail("john@smith.com", "reply@test.com", ["test@me.com", "support@test.com"], $template);

        /**
         * @var HashProvider $hashProvider
         */
        $hashProvider = Container::instance()->get(SHA512HashProvider::class);

        $expectedHashContent = "test@me.com,support@test.comMr Blobby<h1>Hello</h1><p>Welcome Joe Bloggs</p>";

        $this->assertEquals($hashProvider->generateHash($expectedHashContent), $email->getHash());


    }


    public function testContentHashBasedOnSectionTemplateContentIfContentHashSections() {

        $template = new TemplateSummary("Mr Blobby", [
            new TemplateSection("main", "Main", TemplateSection::TYPE_HTML, ["value" => "<h1>Hello</h1>"]),
            new TemplateSection("content", "Content", TemplateSection::TYPE_HTML, ["value" => "<p>Welcome {{params.name}}</p>"]),
        ], [
            new TemplateParameter("name", "Name", TemplateParameter::TYPE_TEXT, "Joe Bloggs")
        ], "{{sections.main}}{{sections.content}}", ["content"]);

        $email = new MailingEmail("john@smith.com", "reply@test.com", ["test@me.com", "support@test.com"], $template);

        /**
         * @var HashProvider $hashProvider
         */
        $hashProvider = Container::instance()->get(SHA512HashProvider::class);

        $expectedHashContent = "test@me.com,support@test.comMr Blobby<p>Welcome Joe Bloggs</p>";

        $this->assertEquals($hashProvider->generateHash($expectedHashContent), $email->getHash());


    }


}