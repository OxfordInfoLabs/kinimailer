<?php


namespace Kinimailer\Test\Services\Mailing;


use Kiniauth\Objects\Communication\Email\StoredEmailSendResult;
use Kiniauth\Objects\Communication\Email\StoredEmailSummary;
use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTaskSummary;
use Kiniauth\Objects\Workflow\Task\Scheduled\ScheduledTaskTimePeriod;
use Kiniauth\Services\Communication\Email\EmailService;
use Kiniauth\Services\Workflow\Task\LongRunning\LongRunningTaskService;
use Kiniauth\Test\Services\Security\AuthenticationHelper;
use Kinikit\Core\Communication\Email\Email;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Testing\MockObjectProvider;
use Kinimailer\Controllers\Account\MailingList;
use Kinimailer\Objects\Mailing\Mailing;
use Kinimailer\Objects\Mailing\MailingEmail;
use Kinimailer\Objects\Mailing\MailingLogSet;
use Kinimailer\Objects\Mailing\MailingProfile;
use Kinimailer\Objects\Mailing\MailingProfileSummary;
use Kinimailer\Objects\Mailing\MailingSummary;
use Kinimailer\Objects\MailingList\MailingListSubscriber;
use Kinimailer\Objects\MailingList\MailingListSummary;
use Kinimailer\Objects\Template\Template;
use Kinimailer\Objects\Template\TemplateParameter;
use Kinimailer\Objects\Template\TemplateSection;
use Kinimailer\Objects\Template\TemplateSummary;
use Kinimailer\Services\Mailing\MailingProcessorLongRunningTask;
use Kinimailer\Services\Mailing\MailingProfileService;
use Kinimailer\Services\Mailing\MailingService;
use Kinimailer\Services\MailingList\MailingListService;
use Kinimailer\Services\Template\TemplateService;
use Kinimailer\TestBase;
use Kinimailer\ValueObjects\Mailing\AdhocMailing;
use PHPUnit\Framework\MockObject\MockObject;

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

    /**
     * @var MockObject
     */
    private $emailService;

    /**
     * @var MockObject
     */
    private $mailingListService;

    /**
     * @var LongRunningTaskService
     */
    private $longRunningTaskService;


    public function setUp(): void {

        $this->templateService = Container::instance()->get(TemplateService::class);
        $this->mailingProfileService = Container::instance()->get(MailingProfileService::class);
        $this->longRunningTaskService = Container::instance()->get(LongRunningTaskService::class);

        $this->emailService = MockObjectProvider::instance()->getMockInstance(EmailService::class);
        $this->mailingListService = MockObjectProvider::instance()->getMockInstance(MailingListService::class);
        $this->mailingService = new MailingService($this->emailService, $this->templateService, $this->mailingListService);

    }

    public function testCanCreateAndUpdateMailingObjects() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        // Create new template
        $templateSummary = new TemplateSummary("New one", [], [], "BINGO BANGO");
        $templateId = $this->templateService->saveTemplate($templateSummary, null, 0);
        $templateSummary->setId($templateId);

        $mailingProfileSummary = new MailingProfileSummary("Bob is your uncle", "from@test.com", "replyto@test.com");
        $profileId = $this->mailingProfileService->saveMailingProfile($mailingProfileSummary, null, 0);
        $mailingProfileSummary->setId($profileId);

        $scheduledTaskSummary = new ScheduledTaskSummary(null, null, null, [new ScheduledTaskTimePeriod(5, null, 12, 20)]);

        $mailingSummary = new MailingSummary("Test Mailing", [new TemplateSection("main", "Main", "text", "Hello world")], [new TemplateParameter("param1", "Param 1", "text", "Test")], $templateSummary,
            MailingSummary::STATUS_DRAFT, [1, 3, 5], [1, 7, 9], ["bing@bong.com", "me@test.com"], $mailingProfileSummary,
            $scheduledTaskSummary);
        $mailingId = $this->mailingService->saveMailing($mailingSummary, null, 0);

        // Update summary with expected saved values
        $mailingSummary->setId($mailingId);
        $mailingSummary->getScheduledTask()->setId(1);
        $mailingSummary->getScheduledTask()->setTaskIdentifier("mailing");
        $mailingSummary->getScheduledTask()->setDescription("Mailing");
        $mailingSummary->getScheduledTask()->setConfiguration($mailingId);


        // Grab Mailing again
        $reMailing = $this->mailingService->getMailing($mailingId);
        $mailingSummary->getScheduledTask()->setNextStartTime($reMailing->getScheduledTask()->getNextStartTime());
        $mailingSummary->setStatus(Mailing::STATUS_SCHEDULED);

        $this->assertEquals($mailingSummary, $reMailing);

        $reMailing->setScheduledTask(null);
        $this->mailingService->saveMailing($reMailing, null, 0);

        $reMailing = $this->mailingService->getMailing($mailingId);
        $this->assertEquals(Mailing::STATUS_DRAFT, $reMailing->getStatus());


    }


    public function testForAdhocMailingWithEmailAddressesTheyAreSentIndividuallyAndLogged() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $template = new TemplateSummary("Main Title", [new TemplateSection("top", "Top Section",
            TemplateSection::TYPE_HTML)],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT)],
            '<h1>Welcome {{params.param1}}</h1>{{sections.top}}');

        $templateId = $this->templateService->saveTemplate($template, null, 0);
        $template = $this->templateService->getTemplate($templateId);

        $profile = new MailingProfileSummary("Test", "from@hello.com", "reply@hello.com");
        $profileId = $this->mailingProfileService->saveMailingProfile($profile, null, 0);
        $profile = MailingProfile::fetch($profileId)->returnSummary();

        $mailing = new MailingSummary("Test Mailing", [new TemplateSection("top", "Main Title", TemplateSection::TYPE_HTML, ["value" => '<p>Thanks for coming</p>'])],
            [new TemplateParameter("param1", "Parameter 1", TemplateParameter::TYPE_TEXT, "Joe Bloggs")], $template, MailingSummary::STATUS_DRAFT, null, null, [
                "mark@hello.com", "james@hello.com"
            ], $profile, null);

        $mailingId = $this->mailingService->saveMailing($mailing, null, 0);

        $template = Template::fetch($templateId);
        $template->setSections($mailing->getTemplateSections());
        $template->setParameters($mailing->getTemplateParameters());
        $template->setTitle("Test Mailing");


        // Programme email responses
        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_SENT, null, 50),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["mark@hello.com"], $template, null), 0
            ]);


        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_FAILED, "BAD EMAIL SEND", 51),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["james@hello.com"], $template), 0
            ]);


        // Process mailing
        $this->mailingService->processMailing($mailingId);

        // Check mailing log entries stored correctly
        $mailingLogSets = MailingLogSet::filter("WHERE mailing_id = $mailingId");
        $this->assertEquals(1, sizeof($mailingLogSets));
        $logSet = $mailingLogSets[0];
        $logEntries = $logSet->getLogEntries();
        $this->assertEquals(2, sizeof($logEntries));

        $this->assertEquals("james@hello.com", $logEntries[0]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_FAILED, $logEntries[0]->getStatus());
        $this->assertEquals("BAD EMAIL SEND", $logEntries[0]->getFailureMessage());
        $this->assertEquals(51, $logEntries[0]->getAssociatedItemId());

        $this->assertEquals("mark@hello.com", $logEntries[1]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_SENT, $logEntries[1]->getStatus());
        $this->assertNull($logEntries[1]->getFailureMessage());
        $this->assertEquals(50, $logEntries[1]->getAssociatedItemId());

        // Check status is sent
        $mailing = Mailing::fetch($mailingId);
        $this->assertEquals(Mailing::STATUS_SENT, $mailing->getStatus());

    }


    public function testForAdhocMailingWithMailingListsTheyAreSentIndividuallyAndLogged() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $template = new TemplateSummary("Main Title", [new TemplateSection("top", "Top Section",
            TemplateSection::TYPE_HTML)],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT)],
            '<h1>Welcome {{params.param1}}</h1>{{sections.top}}');

        $templateId = $this->templateService->saveTemplate($template, null, 0);
        $template = $this->templateService->getTemplate($templateId);

        $profile = new MailingProfileSummary("Test", "from@hello.com", "reply@hello.com");
        $profileId = $this->mailingProfileService->saveMailingProfile($profile, null, 0);
        $profile = MailingProfile::fetch($profileId)->returnSummary();

        $mailing = new MailingSummary("Test {{params.param1}}", [new TemplateSection("top", "Main Title", TemplateSection::TYPE_HTML, ["value" => '<p>Thanks for coming</p>'])],
            [new TemplateParameter("param1", "Parameter 1", TemplateParameter::TYPE_TEXT, "Joe Bloggs")], $template, MailingSummary::STATUS_DRAFT, [
                1, 2, 3
            ], null, null, $profile, null);

        $mailingId = $this->mailingService->saveMailing($mailing, null, 0);


        $template = Template::fetch($templateId);
        $template->setSections($mailing->getTemplateSections());
        $template->setParameters($mailing->getTemplateParameters());
        $template->setTitle("Test {{params.param1}}");


        // Programme mailing list responses
        $this->mailingListService->returnValue("getSubscribersForMailingList", [
            new MailingListSubscriber(1, null, "mark@test.com", null, "Mark Jones"),
            new MailingListSubscriber(1, null, "james@test.com")
        ], [1]);

        $this->mailingListService->returnValue("getSubscribersForMailingList", [
            new MailingListSubscriber(2, null, "peter@test.com", null, "Peter Smith"),
            new MailingListSubscriber(2, null, "paul@test.com")
        ], [2]);

        $this->mailingListService->returnValue("getSubscribersForMailingList", [], [3]);

        // Programme email responses
        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_SENT, null, 50),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["Mark Jones<mark@test.com>"], $template), 0
            ]);


        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_FAILED, "BAD EMAIL SEND", 51),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["james@test.com"], $template), 0
            ]);

        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_SENT, null, 52),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["Peter Smith<peter@test.com>"], $template), 0
            ]);


        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_FAILED, "BAD EMAIL SEND", 53),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["paul@test.com"], $template), 0
            ]);

        // Process mailing
        $this->mailingService->processMailing($mailingId);

        // Check mailing log entries stored correctly
        $mailingLogSets = MailingLogSet::filter("WHERE mailing_id = $mailingId");
        $this->assertEquals(1, sizeof($mailingLogSets));
        $logSet = $mailingLogSets[0];
        $logEntries = $logSet->getLogEntries();
        $this->assertEquals(4, sizeof($logEntries));

        $this->assertEquals("Mark Jones<mark@test.com>", $logEntries[0]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_SENT, $logEntries[0]->getStatus());
        $this->assertNull($logEntries[0]->getFailureMessage());
        $this->assertEquals(50, $logEntries[0]->getAssociatedItemId());

        $this->assertEquals("Peter Smith<peter@test.com>", $logEntries[1]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_SENT, $logEntries[1]->getStatus());
        $this->assertNull($logEntries[1]->getFailureMessage());
        $this->assertEquals(52, $logEntries[1]->getAssociatedItemId());

        $this->assertEquals("james@test.com", $logEntries[2]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_FAILED, $logEntries[2]->getStatus());
        $this->assertEquals("BAD EMAIL SEND", $logEntries[2]->getFailureMessage());
        $this->assertEquals(51, $logEntries[2]->getAssociatedItemId());

        $this->assertEquals("paul@test.com", $logEntries[3]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_FAILED, $logEntries[3]->getStatus());
        $this->assertEquals("BAD EMAIL SEND", $logEntries[3]->getFailureMessage());
        $this->assertEquals(53, $logEntries[3]->getAssociatedItemId());

        // Check status is sent
        $mailing = Mailing::fetch($mailingId);
        $this->assertEquals(Mailing::STATUS_SENT, $mailing->getStatus());

    }


    public function testForScheduledMailingWithMailingListsTheyAreSentIndividuallyAndLogged() {


        AuthenticationHelper::login("admin@kinicart.com", "password");

        $template = new TemplateSummary("Main Title", [new TemplateSection("top", "Top Section",
            TemplateSection::TYPE_HTML)],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT)],
            '<h1>Welcome {{params.param1}}</h1>{{sections.top}}');

        $templateId = $this->templateService->saveTemplate($template, null, 0);
        $template = $this->templateService->getTemplate($templateId);

        $profile = new MailingProfileSummary("Test", "from@hello.com", "reply@hello.com");
        $profileId = $this->mailingProfileService->saveMailingProfile($profile, null, 0);
        $profile = MailingProfile::fetch($profileId)->returnSummary();

        $mailing = new MailingSummary("Test {{params.param1}}", [new TemplateSection("top", "Main Title", TemplateSection::TYPE_HTML, ["value" => '<p>Thanks for coming</p>'])],
            [new TemplateParameter("param1", "Parameter 1", TemplateParameter::TYPE_TEXT, "Joe Bloggs")], $template, MailingSummary::STATUS_DRAFT, [
                1, 2, 3
            ], null, null, $profile, new ScheduledTaskSummary(null, null, null, [new ScheduledTaskTimePeriod(11, null, 10, 23)]));

        $mailingId = $this->mailingService->saveMailing($mailing, null, 0);


        $template = Template::fetch($templateId);
        $template->setSections($mailing->getTemplateSections());
        $template->setParameters($mailing->getTemplateParameters());
        $template->setTitle("Test {{params.param1}}");


        // Programme mailing list responses
        $this->mailingListService->returnValue("getSubscribersForMailingList", [
            new MailingListSubscriber(1, null, "mark@test.com", null, "Mark Jones"),
            new MailingListSubscriber(1, null, "james@test.com")
        ], [1]);

        $this->mailingListService->returnValue("getSubscribersForMailingList", [
            new MailingListSubscriber(2, null, "peter@test.com", null, "Peter Smith"),
            new MailingListSubscriber(2, null, "paul@test.com")
        ], [2]);

        $this->mailingListService->returnValue("getSubscribersForMailingList", [], [3]);

        // Programme email responses
        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_SENT, null, 50),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["Mark Jones<mark@test.com>"], $template), 0
            ]);


        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_FAILED, "BAD EMAIL SEND", 51),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["james@test.com"], $template), 0
            ]);

        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_SENT, null, 52),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["Peter Smith<peter@test.com>"], $template), 0
            ]);


        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_FAILED, "BAD EMAIL SEND", 53),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["paul@test.com"], $template), 0
            ]);

        // Process mailing
        $this->mailingService->processMailing($mailingId);

        // Check mailing log entries stored correctly
        $mailingLogSets = MailingLogSet::filter("WHERE mailing_id = $mailingId");
        $this->assertEquals(1, sizeof($mailingLogSets));
        $logSet = $mailingLogSets[0];
        $logEntries = $logSet->getLogEntries();
        $this->assertEquals(4, sizeof($logEntries));

        $this->assertEquals("Mark Jones<mark@test.com>", $logEntries[0]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_SENT, $logEntries[0]->getStatus());
        $this->assertNull($logEntries[0]->getFailureMessage());
        $this->assertEquals(50, $logEntries[0]->getAssociatedItemId());

        $this->assertEquals("Peter Smith<peter@test.com>", $logEntries[1]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_SENT, $logEntries[1]->getStatus());
        $this->assertNull($logEntries[1]->getFailureMessage());
        $this->assertEquals(52, $logEntries[1]->getAssociatedItemId());

        $this->assertEquals("james@test.com", $logEntries[2]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_FAILED, $logEntries[2]->getStatus());
        $this->assertEquals("BAD EMAIL SEND", $logEntries[2]->getFailureMessage());
        $this->assertEquals(51, $logEntries[2]->getAssociatedItemId());

        $this->assertEquals("paul@test.com", $logEntries[3]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_FAILED, $logEntries[3]->getStatus());
        $this->assertEquals("BAD EMAIL SEND", $logEntries[3]->getFailureMessage());
        $this->assertEquals(53, $logEntries[3]->getAssociatedItemId());

        // Check status is scheduled
        $mailing = Mailing::fetch($mailingId);
        $this->assertEquals(Mailing::STATUS_SCHEDULED, $mailing->getStatus());


    }


    public function testIfRunNowPassedAsTrueMailingIsProcessedForScheduledTaskImmediatelyAndThenSetBackToScheduledStatus() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $template = new TemplateSummary("Main Title", [new TemplateSection("top", "Top Section",
            TemplateSection::TYPE_HTML)],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT)],
            '<h1>Welcome {{params.param1}}</h1>{{sections.top}}');

        $templateId = $this->templateService->saveTemplate($template, null, 0);
        $template = $this->templateService->getTemplate($templateId);

        $profile = new MailingProfileSummary("Test", "from@hello.com", "reply@hello.com");
        $profileId = $this->mailingProfileService->saveMailingProfile($profile, null, 0);
        $profile = MailingProfile::fetch($profileId)->returnSummary();

        $mailing = new MailingSummary("Test Mailing", [new TemplateSection("top", "Main Title", TemplateSection::TYPE_HTML, ["value" => '<p>Thanks for coming</p>'])],
            [new TemplateParameter("param1", "Parameter 1", TemplateParameter::TYPE_TEXT, "Joe Bloggs")], $template, MailingSummary::STATUS_DRAFT, null, null, [
                "mark@hello.com", "james@hello.com"
            ], $profile, new ScheduledTaskSummary(null, null, null, [new ScheduledTaskTimePeriod(11, null, 10, 23)]));

        $mailingId = $this->mailingService->saveMailing($mailing, null, 0);


        $template = Template::fetch($templateId);
        $template->setSections($mailing->getTemplateSections());
        $template->setParameters($mailing->getTemplateParameters());
        $template->setTitle("Test Mailing");


        // Programme email responses
        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_SENT, null, 50),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["mark@hello.com"], $template), 0
            ]);


        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_FAILED, "BAD EMAIL SEND", 51),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["james@hello.com"], $template), 0
            ]);


        // Process mailing
        $this->mailingService->processMailing($mailingId);

        // Check mailing log entries stored correctly
        $mailingLogSets = MailingLogSet::filter("WHERE mailing_id = $mailingId");
        $this->assertEquals(1, sizeof($mailingLogSets));
        $logSet = $mailingLogSets[0];
        $logEntries = $logSet->getLogEntries();
        $this->assertEquals(2, sizeof($logEntries));

        $this->assertEquals("james@hello.com", $logEntries[0]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_FAILED, $logEntries[0]->getStatus());
        $this->assertEquals("BAD EMAIL SEND", $logEntries[0]->getFailureMessage());
        $this->assertEquals(51, $logEntries[0]->getAssociatedItemId());

        $this->assertEquals("mark@hello.com", $logEntries[1]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_SENT, $logEntries[1]->getStatus());
        $this->assertNull($logEntries[1]->getFailureMessage());
        $this->assertEquals(50, $logEntries[1]->getAssociatedItemId());

        // Check status is scheduled
        $mailing = Mailing::fetch($mailingId);
        $this->assertEquals(Mailing::STATUS_SCHEDULED, $mailing->getStatus());

    }

    public function testIfMailingInSendingOrSentStateProcessIsAbortedImmediately() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $template = new TemplateSummary("Main Title", [new TemplateSection("top", "Top Section",
            TemplateSection::TYPE_HTML)],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT)],
            '<h1>Welcome {{params.param1}}</h1>{{sections.top}}');

        $templateId = $this->templateService->saveTemplate($template, null, 0);
        $template = $this->templateService->getTemplate($templateId);

        $profile = new MailingProfileSummary("Test", "from@hello.com", "reply@hello.com");
        $profileId = $this->mailingProfileService->saveMailingProfile($profile, null, 0);
        $profile = MailingProfile::fetch($profileId)->returnSummary();

        $mailing = new MailingSummary("Test Mailing", [new TemplateSection("top", "Main Title", TemplateSection::TYPE_HTML, ["value" => '<p>Thanks for coming</p>'])],
            [new TemplateParameter("param1", "Parameter 1", TemplateParameter::TYPE_TEXT, "Joe Bloggs")], $template, MailingSummary::STATUS_DRAFT, null, null, [
                "mark@hello.com", "james@hello.com"
            ], $profile, new ScheduledTaskSummary(null, null, null, [new ScheduledTaskTimePeriod(11, null, 10, 23)]));


        // Set sending status
        $mailing->setStatus(Mailing::STATUS_SENDING);

        $mailing = new Mailing($mailing, null, 1);
        $mailing->save();
        $mailingId = $mailing->getId();

        $this->mailingService->processMailing($mailingId);

        // Check no change to status
        $mailing = Mailing::fetch($mailingId);
        $this->assertEquals(Mailing::STATUS_SENDING, $mailing->getStatus());

        // Check no mailing logs generated
        $mailingLogSets = MailingLogSet::filter("WHERE mailing_id = $mailingId");
        $this->assertEquals(0, sizeof($mailingLogSets));


    }


    public function testLongRunningTaskCallsProcessAndUpdatesProgress() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $template = new TemplateSummary("Main Title", [new TemplateSection("top", "Top Section",
            TemplateSection::TYPE_HTML)],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT)],
            '<h1>Welcome {{params.param1}}</h1>{{sections.top}}');

        $templateId = $this->templateService->saveTemplate($template, null, 0);
        $template = $this->templateService->getTemplate($templateId);

        $profile = new MailingProfileSummary("Test", "from@hello.com", "reply@hello.com");
        $profileId = $this->mailingProfileService->saveMailingProfile($profile, null, 0);
        $profile = MailingProfile::fetch($profileId)->returnSummary();

        $mailing = new MailingSummary("Test Mailing", [new TemplateSection("top", "Main Title", TemplateSection::TYPE_HTML, ["value" => '<p>Thanks for coming</p>'])],
            [new TemplateParameter("param1", "Parameter 1", TemplateParameter::TYPE_TEXT, "Joe Bloggs")], $template, MailingSummary::STATUS_DRAFT, null, null, [
                "mark@hello.com", "james@hello.com"
            ], $profile, null);

        $mailingId = $this->mailingService->saveMailing($mailing, null, 0);


        $template = Template::fetch($templateId);
        $template->setSections($mailing->getTemplateSections());
        $template->setParameters($mailing->getTemplateParameters());
        $template->setTitle("Test Mailing");


        // Programme email responses
        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_SENT, null, 50),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["mark@hello.com"], $template), 0
            ]);


        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_FAILED, "BAD EMAIL SEND", 51),
            [
                new MailingEmail("from@hello.com", "reply@hello.com", ["james@hello.com"], $template), 0
            ]);


        $task = new MailingProcessorLongRunningTask($mailingId, $this->mailingService);
        $this->longRunningTaskService->startTask("Mailing", $task, "test-mailing", null, 0);

        // Check mailing log entries stored correctly
        $mailingLogSets = MailingLogSet::filter("WHERE mailing_id = $mailingId");
        $this->assertEquals(1, sizeof($mailingLogSets));
        $logSet = $mailingLogSets[0];
        $logEntries = $logSet->getLogEntries();
        $this->assertEquals(2, sizeof($logEntries));

        $this->assertEquals("james@hello.com", $logEntries[0]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_FAILED, $logEntries[0]->getStatus());
        $this->assertEquals("BAD EMAIL SEND", $logEntries[0]->getFailureMessage());
        $this->assertEquals(51, $logEntries[0]->getAssociatedItemId());

        $this->assertEquals("mark@hello.com", $logEntries[1]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_SENT, $logEntries[1]->getStatus());
        $this->assertNull($logEntries[1]->getFailureMessage());
        $this->assertEquals(50, $logEntries[1]->getAssociatedItemId());

        // Check status is sent
        $mailing = Mailing::fetch($mailingId);
        $this->assertEquals(Mailing::STATUS_SENT, $mailing->getStatus());

        // Now check that the long running task was updated
        $longRunningTask = $this->longRunningTaskService->getStoredTaskByTaskKey("test-mailing");
        $this->assertEquals(["total" => 2, "completed" => [
            [
                'id' => 13,
                'logSetId' => 5,
                'emailAddress' => 'mark@hello.com',
                'mobileNumber' => null,
                'status' => 'SENT',
                'failureMessage' => null,
                'associatedItemId' => 50
            ],
            [
                'id' => 14,
                'logSetId' => 5,
                'emailAddress' => 'james@hello.com',
                'mobileNumber' => null,
                'status' => 'FAILED',
                'failureMessage' => 'BAD EMAIL SEND',
                'associatedItemId' => 51
            ]

        ]], $longRunningTask->getProgressData());

    }


    public function testCanSendAdhocMailingWithCustomTitleAndOriginalMailingLeftUnchanged() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $template = new TemplateSummary("Main Title", [new TemplateSection("top", "Top Section",
            TemplateSection::TYPE_HTML)],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT)],
            '<h1>Welcome {{params.param1}}</h1>{{sections.top}}');

        $templateId = $this->templateService->saveTemplate($template, null, 0);
        $template = $this->templateService->getTemplate($templateId);


        $mailing = new MailingSummary("Test {{params.param1}}", [new TemplateSection("top", "Main Title", TemplateSection::TYPE_HTML, ["value" => '<p>Thanks for coming</p>'])],
            [new TemplateParameter("param1", "Parameter 1", TemplateParameter::TYPE_TEXT, "Joe Bloggs")], $template, MailingSummary::STATUS_DRAFT, [
                1, 2, 3
            ], null, null, null, new ScheduledTaskSummary(null, null, null, [new ScheduledTaskTimePeriod(11, null, 10, 23)]));

        $mailingId = $this->mailingService->saveMailing($mailing, null, 0);


        $adhocMailing = new AdhocMailing($mailingId, "Mark Test", "mark@test.com",
            [new TemplateSection("top", "Top Section",
                TemplateSection::TYPE_HTML, ["value" => "Running in the wild"])],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT, "Staggering in the dark")],
            "My new subject access request", "from@test.com", "reply@test.com"
        );


        $template = new Template(new TemplateSummary("My new subject access request", [new TemplateSection("top", "Top Section",
            TemplateSection::TYPE_HTML, ["value" => "Running in the wild"])],
            [new TemplateParameter("param1", "Param 1", TemplateParameter::TYPE_TEXT, "Staggering in the dark")],
            '<h1>Welcome {{params.param1}}</h1>{{sections.top}}', [], $template->getId()), null, 0);

        // Programme email responses
        $this->emailService->returnValue("send",
            new StoredEmailSendResult(StoredEmailSendResult::STATUS_SENT, null, 55),
            [
                new MailingEmail("from@test.com", "reply@test.com", ["Mark Test <mark@test.com>"], $template,
                    new MailingListSubscriber($mailingId, null, "mark@test.com", null, "Mark Test")), 0
            ]);


        $this->mailingService->processAdhocMailing($adhocMailing);


        // Check mailing log entries stored correctly
        $mailingLogSets = MailingLogSet::filter("WHERE mailing_id = $mailingId");
        $this->assertEquals(1, sizeof($mailingLogSets));
        $logSet = $mailingLogSets[0];
        $logEntries = $logSet->getLogEntries();
        $this->assertEquals(1, sizeof($logEntries));

        $this->assertEquals("Mark Test <mark@test.com>", $logEntries[0]->getEmailAddress());
        $this->assertEquals(StoredEmailSummary::STATUS_SENT, $logEntries[0]->getStatus());
        $this->assertNull($logEntries[0]->getFailureMessage());
        $this->assertEquals(55, $logEntries[0]->getAssociatedItemId());


    }

}