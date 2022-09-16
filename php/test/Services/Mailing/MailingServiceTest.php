<?php


namespace Kinimailer\Test\Services\Mailing;


use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTaskSummary;
use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTaskTimePeriod;
use Kiniauth\Test\Services\Security\AuthenticationHelper;
use Kinikit\Core\DependencyInjection\Container;
use Kinimailer\Objects\Mailing\MailingProfile;
use Kinimailer\Objects\Mailing\MailingProfileSummary;
use Kinimailer\Objects\Mailing\MailingSummary;
use Kinimailer\Objects\Template\Template;
use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;
use Kinimailer\Objects\Template\TemplateSummary;
use Kinimailer\Services\Mailing\MailingProfileService;
use Kinimailer\Services\Mailing\MailingService;
use Kinimailer\Services\Template\TemplateService;
use Kinimailer\TestBase;

include_once "autoloader.php";

class MailingServiceTest extends TestBase {

    /**
     * @var TemplateService
     */
    private $templateService;

    /**
     * @var MailingProfileService
     */
    private $mailingProfileService;

    /**
     * @var MailingService
     */
    private $mailingService;

    public function setUp(): void {
        $this->templateService = Container::instance()->get(TemplateService::class);
        $this->mailingProfileService = Container::instance()->get(MailingProfileService::class);
        $this->mailingService = Container::instance()->get(MailingService::class);
    }

    public function testCanCreateAndUpdateMailingObjects() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        // Create new template
        $templateSummary = new TemplateSummary("New one", [], [], "BINGO BANGO");
        $templateId = $this->templateService->saveTemplate($templateSummary);
        $templateSummary->setId($templateId);

        $mailingProfileSummary = new MailingProfileSummary("Bob is your uncle", "from@test.com", "replyto@test.com");
        $profileId = $this->mailingProfileService->saveMailingProfile($mailingProfileSummary);
        $mailingProfileSummary->setId($profileId);

        $scheduledTaskSummary = new ScheduledTaskSummary(null, "Scheduled Mailing", null, [new ScheduledTaskTimePeriod(5, null, 12, 20)]);

        $mailingSummary = new MailingSummary("Test Mailing", "My Favourite Mailing", [new TemplateSection("main", "Main", "text", "Hello world")], [new TemplateParameter("param1", "Param 1", "text", "Test")], $templateSummary,
            MailingSummary::STATUS_DRAFT, [1, 3, 5], [1, 7, 9], ["bing@bong.com", "me@test.com"], $mailingProfileSummary,
            $scheduledTaskSummary);
        $mailingId = $this->mailingService->saveMailing($mailingSummary);

        // Update summary with expected saved values
        $mailingSummary->setId($mailingId);
        $mailingSummary->getScheduledTask()->setId(1);
        $mailingSummary->getScheduledTask()->setTaskIdentifier("mailing");
        $mailingSummary->getScheduledTask()->setConfiguration($mailingId);


        // Grab Mailing again
        $reMailing = $this->mailingService->getMailing($mailingId);
        $mailingSummary->getScheduledTask()->setNextStartTime($reMailing->getScheduledTask()->getNextStartTime());

        $this->assertEquals($mailingSummary, $reMailing);

    }


}