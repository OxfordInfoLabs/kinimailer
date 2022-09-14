<?php


namespace Kinimailer\Test\Services\Mailing;

use Kiniauth\Test\Services\Security\AuthenticationHelper;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Persistence\ORM\Exception\ObjectNotFoundException;
use Kinimailer\Objects\Mailing\MailingProfile;
use Kinimailer\Objects\Mailing\MailingProfileSummary;
use Kinimailer\Services\Mailing\MailingProfileService;
use Kinimailer\TestBase;

class MailingProfileServiceTest extends TestBase {

    /**
     * @var MailingProfileService
     */
    private $mailingProfileService;


    public function setUp(): void {
        $this->mailingProfileService = Container::instance()->get(MailingProfileService::class);
    }


    public function testCanCreateUpdateAndDeleteMailingProfilesAsAdmin() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $newProfile = new MailingProfileSummary("Test Admin", "test@excellent.com", "bingo@bongo.com");
        $newId = $this->mailingProfileService->saveMailingProfile($newProfile, null, 0);
        $newProfile->setId($newId);

        $reProfile = MailingProfile::fetch($newId);
        $this->assertEquals($newProfile, $reProfile->returnSummary());
        $this->assertEquals(0, $reProfile->getAccountId());
        $this->assertEquals(null, $reProfile->getProjectKey());

        $reProfile->setTitle("Updated");
        $reProfile->setFromAddress("new@excellent.com");
        $reProfile->setReplyToAddress("other@excellent.com");

        $this->mailingProfileService->saveMailingProfile($reProfile, null, 0);

        $reReProfile = MailingProfile::fetch($newId);
        $this->assertEquals($reProfile->returnSummary(), $reReProfile->returnSummary());
        $this->assertEquals(0, $reReProfile->getAccountId());
        $this->assertEquals(null, $reReProfile->getProjectKey());


        $this->mailingProfileService->removeMailingProfile($newId);

        try {
            MailingProfile::fetch($newId);
            $this->fail("Should have thrown here");
        } catch (ObjectNotFoundException $e) {
            // Success
        }


    }


    public function testCanCreateUpdateAndDeleteMailingProfilesAsAccountHolder() {

        AuthenticationHelper::login("sam@samdavisdesign.co.uk", "password");

        $newProfile = new MailingProfileSummary("Test Account", "test@excellent.com", "bingo@bongo.com");
        $newId = $this->mailingProfileService->saveMailingProfile($newProfile, "wiperBlades", 1);
        $newProfile->setId($newId);

        $reProfile = MailingProfile::fetch($newId);
        $this->assertEquals($newProfile, $reProfile->returnSummary());
        $this->assertEquals(1, $reProfile->getAccountId());
        $this->assertEquals("wiperBlades", $reProfile->getProjectKey());

        $reProfile->setTitle("Updated");
        $reProfile->setFromAddress("new@excellent.com");
        $reProfile->setReplyToAddress("other@excellent.com");

        $this->mailingProfileService->saveMailingProfile($reProfile, "wiperBlades", 1);

        $reReProfile = MailingProfile::fetch($newId);
        $this->assertEquals($reProfile->returnSummary(), $reReProfile->returnSummary());
        $this->assertEquals(1, $reReProfile->getAccountId());
        $this->assertEquals("wiperBlades", $reReProfile->getProjectKey());


        $this->mailingProfileService->removeMailingProfile($newId);

        try {
            MailingProfile::fetch($newId);
            $this->fail("Should have thrown here");
        } catch (ObjectNotFoundException $e) {
            // Success
        }


    }


    public function testCanFilterMailingProfiles() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $profile1 = new MailingProfileSummary("My Profile", "test@test.com", "bingo@banjo.com", 1);
        $this->mailingProfileService->saveMailingProfile($profile1, null, 0);
        $profile2 = new MailingProfileSummary("Another Profile", "home@test.com", "apple@banjo.com", 2);
        $this->mailingProfileService->saveMailingProfile($profile2, null, 0);
        $profile3 = new MailingProfileSummary("Interesting Profile", "away@me.com", "pear@banjo.com", 3);
        $this->mailingProfileService->saveMailingProfile($profile3, null, 1);
        $profile4 = new MailingProfileSummary("Wonderful Profile", "office@me.com", "guava@banjo.com", 4);
        $this->mailingProfileService->saveMailingProfile($profile4, null, 1);
        $profile5 = new MailingProfileSummary("Banging Profile", "granted@test.com", "mango@banjo.com", 5);
        $this->mailingProfileService->saveMailingProfile($profile5, null, 2);

        // Filter as admin first of all
        $results = $this->mailingProfileService->filterMailingProfiles("", null, 0, 10, null);
        $this->assertEquals([$profile2, $profile5, $profile3, $profile1, $profile4], $results);

        $results = $this->mailingProfileService->filterMailingProfiles("", null, 0, 10, 1);
        $this->assertEquals([$profile3, $profile4], $results);

        $results = $this->mailingProfileService->filterMailingProfiles("ng", null, 0, 10, null);
        $this->assertEquals([$profile5, $profile3, $profile1], $results);

        $results = $this->mailingProfileService->filterMailingProfiles("granted", null, 0, 10, null);
        $this->assertEquals([$profile5], $results);

        $results = $this->mailingProfileService->filterMailingProfiles("guava", null, 0, 10, null);
        $this->assertEquals([$profile4], $results);


        // Limit and offset
        $results = $this->mailingProfileService->filterMailingProfiles("", null, 0, 3, null);
        $this->assertEquals([$profile2, $profile5, $profile3], $results);

        $results = $this->mailingProfileService->filterMailingProfiles("", null, 2, 10, null);
        $this->assertEquals([$profile3, $profile1, $profile4], $results);

        // Filter as user
        AuthenticationHelper::login("sam@samdavisdesign.co.uk", "password");

        $results = $this->mailingProfileService->filterMailingProfiles("", null, 0, 10, null);
        $this->assertEquals([$profile3, $profile4], $results);

        $results = $this->mailingProfileService->filterMailingProfiles("office", null, 0, 10, null);
        $this->assertEquals([$profile4], $results);


    }

}