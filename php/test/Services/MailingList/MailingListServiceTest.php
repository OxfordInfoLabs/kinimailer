<?php

namespace Kinimailer\Services\MailingList;

use Kiniauth\Test\Services\Security\AuthenticationHelper;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Testing\MockObjectProvider;
use Kinikit\Core\Validation\ValidationException;
use Kinikit\Persistence\ORM\Exception\ObjectNotFoundException;
use Kinimailer\Objects\MailingList\MailingList;
use Kinimailer\Objects\MailingList\MailingListSubscriber;
use Kinimailer\Objects\MailingList\MailingListSummary;
use Kinimailer\Services\Mailing\MailingService;
use Kinimailer\TestBase;
use Kinimailer\ValueObjects\MailingList\GuestMailingListSubscriberPreferences;
use Kinimailer\ValueObjects\MailingList\UserMailingListSubscriberPreferences;

include_once "autoloader.php";

class MailingListServiceTest extends TestBase {

    /**
     * @var MailingListService
     */
    private $mailingListService;


    /**
     * @var MailingService
     */
    private $mailingService;

    /**
     * Mailing list service
     */
    public function setUp(): void {
        $this->mailingService = MockObjectProvider::instance()->getMockInstance(MailingService::class);
        $this->mailingListService = Container::instance()->get(MailingListService::class);
        $this->mailingListService->setMailingService($this->mailingService);
    }

    public function testCanCreateReadUpdateAndDeleteMailingListAsSuperAdmin() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $mailingList = new MailingListSummary("newone", "New Mailing List", "My New test mailing list");
        $newId = $this->mailingListService->saveMailingList($mailingList);
        $this->assertNotNull($newId);
        $reList = MailingList::fetch($newId);
        $this->assertEquals("newone", $reList->getKey());
        $this->assertEquals("New Mailing List", $reList->getTitle());
        $this->assertEquals("My New test mailing list", $reList->getDescription());
        $this->assertFalse($reList->isAnonymousSignUp());
        $this->assertSame(0, $reList->getAccountId());

        // Check can get one
        $reSummary = $this->mailingListService->getMailingList($newId);
        $this->assertEquals($reList->returnSummary(), $reSummary);

        // Update one
        $update = new MailingListSummary("updatedone", "Updated Mailing List", "Updated Mailing", true, 1, $newId);
        $this->mailingListService->saveMailingList($update);

        $reSummary = $this->mailingListService->getMailingList($newId);
        $this->assertEquals($update, $reSummary);


        // Delete
        $this->mailingListService->removeMailingList($newId);
        try {
            $this->mailingListService->getMailingList($newId);
            $this->fail("Should have thrown here");
        } catch (ObjectNotFoundException $e) {
            // Success
        }


    }


    public function testCanCreateReadUpdateAndDeleteMailingListAsAccountHolder() {

        AuthenticationHelper::login("sam@samdavisdesign.co.uk", "password");

        $mailingList = new MailingListSummary("newone", "New Mailing List", "My New test mailing list", false, 3);
        $newId = $this->mailingListService->saveMailingList($mailingList);
        $this->assertNotNull($newId);
        $reList = MailingList::fetch($newId);
        $this->assertEquals("newone", $reList->getKey());
        $this->assertEquals("New Mailing List", $reList->getTitle());
        $this->assertEquals("My New test mailing list", $reList->getDescription());
        $this->assertFalse($reList->isAnonymousSignUp());
        $this->assertEquals(3, $reList->getAutoResponderMailingId());
        $this->assertSame(1, $reList->getAccountId());

        // Check can get one
        $reSummary = $this->mailingListService->getMailingList($newId);
        $this->assertEquals($reList->returnSummary(), $reSummary);

        // Update one
        $update = new MailingListSummary("updatedone", "Updated Mailing List", "Updated Mailing", true, null, $newId);
        $this->mailingListService->saveMailingList($update);

        $reSummary = $this->mailingListService->getMailingList($newId);
        $this->assertEquals($update, $reSummary);


        // Delete
        $this->mailingListService->removeMailingList($newId);
        try {
            $this->mailingListService->getMailingList($newId);
            $this->fail("Should have thrown here");
        } catch (ObjectNotFoundException $e) {
            // Success
        }


    }


    public function testDuplicateMailingListKeysAreNotPermittedInSameAccountCombination() {

        AuthenticationHelper::login("admin@kinicart.com", "password");
        $mailingList = new MailingListSummary("duplicate", "New Mailing List", "My New test mailing list");

        $this->mailingListService->saveMailingList($mailingList);
        try {
            $this->mailingListService->saveMailingList($mailingList);
            $this->fail("Should have thrown here");
        } catch (ValidationException $e) {
            $this->assertTrue(true);
        }

        AuthenticationHelper::login("simon@peterjonescarwash.com", "password");
        $this->mailingListService->saveMailingList($mailingList);
        try {
            $this->mailingListService->saveMailingList($mailingList);
            $this->fail("Should have thrown here");
        } catch (ValidationException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->mailingListService->saveMailingList($mailingList, "soapSuds");
            $this->fail("Should have thrown here");
        } catch (ValidationException $e) {
            $this->assertTrue(true);
        }


    }


    public function testCanCheckForDuplicateMailingListKeys() {

        AuthenticationHelper::login("admin@kinicart.com", "password");
        $mailingList = new MailingListSummary("newduplicate", "New Mailing List", "My New test mailing list");

        // Should be fine for a new item
        $this->assertTrue($this->mailingListService->isKeyAvailableForMailingList("newduplicate"));

        // Save it.
        $newId = $this->mailingListService->saveMailingList($mailingList);

        // Should not be fine for a new item or a different id
        $this->assertFalse($this->mailingListService->isKeyAvailableForMailingList("newduplicate"));
        $this->assertFalse($this->mailingListService->isKeyAvailableForMailingList("newduplicate", 100));

        // Should be fine for same id
        $this->assertTrue($this->mailingListService->isKeyAvailableForMailingList("newduplicate", $newId));

        // Try different account
        AuthenticationHelper::login("simon@peterjonescarwash.com", "password");

        // Should be fine for a new item
        $this->assertTrue($this->mailingListService->isKeyAvailableForMailingList("newduplicate"));

        // Save it.
        $newId = $this->mailingListService->saveMailingList($mailingList);

        // Should not be fine for a new item or a different id
        $this->assertFalse($this->mailingListService->isKeyAvailableForMailingList("newduplicate"));
        $this->assertFalse($this->mailingListService->isKeyAvailableForMailingList("newduplicate", 100));

        // Should be fine for same id
        $this->assertTrue($this->mailingListService->isKeyAvailableForMailingList("newduplicate", $newId));


    }


    public function testCanGetMailingListByKeyAndParentAccountId() {

        AuthenticationHelper::login("admin@kinicart.com", "password");
        $mailingList = new MailingListSummary("adminowned", "New Mailing List", "My New test mailing list");
        $id = $this->mailingListService->saveMailingList($mailingList);
        $savedMailingList = MailingList::fetch($id)->returnSummary();

        // Check direct account id lookup
        $this->assertEquals($savedMailingList, $this->mailingListService->getMailingListByKey("adminowned", 0));

        AuthenticationHelper::login("sam@samdavisdesign.co.uk", "password");
        $reMailingList = $this->mailingListService->getMailingListByKey("adminowned");
        $this->assertEquals($savedMailingList, $reMailingList);

    }


    public function testCanUpdateMailingListPreferencesForGuest() {

        AuthenticationHelper::login("admin@kinicart.com", "password");
        $id1 = $this->mailingListService->saveMailingList(new MailingListSummary("adminuseronly", "New User only", "My New user only mailing list"));
        $id2 = $this->mailingListService->saveMailingList(new MailingListSummary("adminguest", "New Guest List", "My guest mailing list", true));
        $id3 = $this->mailingListService->saveMailingList(new MailingListSummary("adminguest2", "New Guest List 2", "My guest mailing list 2", true));

        // Logout
        AuthenticationHelper::logout();

        $preferences = new GuestMailingListSubscriberPreferences([
            "adminuseronly" => 1,
            "adminguest" => 1,
            "adminguest2" => 1
        ], "test@noaccount.com", "07890 111111", "Test No Account",
            "My Organisation");

        $this->mailingListService->updateSubscriptionPreferences($preferences);


        // Check we subscribed
        $matches = MailingListSubscriber::filter("WHERE emailAddress = ? AND mobileNumber = ? AND name = ? AND organisation = ? AND mailing_list_id IN (?,?,?) ",
            "test@noaccount.com", "07890 111111", "Test No Account", "My Organisation", $id1, $id2, $id3);

        $this->assertEquals(2, sizeof($matches));
        $this->assertEquals($id2, $matches[0]->getMailingListId());
        $this->assertEquals($id3, $matches[1]->getMailingListId());


        $preferences = new GuestMailingListSubscriberPreferences([
            "adminguest" => 0,
            "adminguest2" => 1
        ], "test@noaccount.com", "07890 111111", "Test No Account", "Test alternative org");

        $this->mailingListService->updateSubscriptionPreferences($preferences);


        // Check we subscribed
        $matches = MailingListSubscriber::filter("WHERE emailAddress = ? AND mobileNumber = ? AND name = ? AND organisation = ? AND mailing_list_id IN (?,?,?) ",
            "test@noaccount.com", "07890 111111", "Test No Account", "Test alternative org", $id1, $id2, $id3);

        $this->assertEquals(1, sizeof($matches));
        $this->assertEquals($id3, $matches[0]->getMailingListId());


    }


    public function testCanUpdateMailingListPreferencesForUser() {


        AuthenticationHelper::login("admin@kinicart.com", "password");
        $id1 = $this->mailingListService->saveMailingList(new MailingListSummary("anotheruseronly", "New User only", "My New user only mailing list"));
        $id2 = $this->mailingListService->saveMailingList(new MailingListSummary("anotherguest", "New Guest List", "My guest mailing list", true));
        $id3 = $this->mailingListService->saveMailingList(new MailingListSummary("anotherguest2", "New Guest List 2", "My guest mailing list 2", true));

        // Logout
        AuthenticationHelper::logout();

        $preferences = new UserMailingListSubscriberPreferences([
            "anotheruseronly" => 1,
            "anotherguest" => 1,
            "anotherguest2" => 1
        ], 1);

        $this->mailingListService->updateSubscriptionPreferences($preferences);


        // Check we subscribed
        $matches = MailingListSubscriber::filter("WHERE user_id = ? AND mailing_list_id IN (?,?,?) ",
            1, $id1, $id2, $id3);

        $this->assertEquals(3, sizeof($matches));
        $this->assertEquals($id1, $matches[0]->getMailingListId());
        $this->assertEquals($id2, $matches[1]->getMailingListId());
        $this->assertEquals($id3, $matches[2]->getMailingListId());


        $preferences = new UserMailingListSubscriberPreferences([
            "anotheruseronly" => 0,
            "anotherguest" => 0,
            "anotherguest2" => 1
        ], 1);

        $this->mailingListService->updateSubscriptionPreferences($preferences);


        // Check we subscribed
        $matches = MailingListSubscriber::filter("WHERE user_id = ? AND mailing_list_id IN (?,?,?) ",
            1, $id1, $id2, $id3);

        $this->assertEquals(1, sizeof($matches));
        $this->assertEquals($id3, $matches[0]->getMailingListId());


    }




    public function testCanUnsubscribeSingleSubscriptionUsingUnsubscribeKey() {

        AuthenticationHelper::login("admin@kinicart.com", "password");
        $this->mailingListService->saveMailingList(new MailingListSummary("testunsubscribe", "Test", "Test ML", true));

        AuthenticationHelper::logout();

        $this->mailingListService->updateSubscriptionPreferences(new GuestMailingListSubscriberPreferences([
            "testunsubscribe" => 1
        ], "geoff@test.com"));

        $key = MailingListSubscriber::filter("WHERE email_address = ?", "geoff@test.com")[0]->getUnsubscribeKey();

        // Bad email address
        $this->mailingListService->unsubscribeBykey($key, "wrongemail@test.com");

        // Should still be subscribed
        $entries = MailingListSubscriber::filter("WHERE email_address = ?", "geoff@test.com");
        $this->assertEquals(1, sizeof($entries));

        // Correct email address
        $this->mailingListService->unsubscribeBykey($key, $entries[0]->returnEmailHash());

        $entries = MailingListSubscriber::filter("WHERE email_address = ?", "geoff@test.com");
        $this->assertEquals(0, sizeof($entries));

        // Phone ones
        $this->mailingListService->updateSubscriptionPreferences(new GuestMailingListSubscriberPreferences([
            "testunsubscribe" => 1
        ], "geoff@test.com", "07545 898787"));

        $key = MailingListSubscriber::filter("WHERE email_address = ?", "geoff@test.com")[0]->getUnsubscribeKey();


        // Bad mobile number
        $this->mailingListService->unsubscribeBykey($key, null, "07545 898787");

        // Should still be subscribed
        $entries = MailingListSubscriber::filter("WHERE email_address = ?", "geoff@test.com");
        $this->assertEquals(1, sizeof($entries));

        // Correct mobile
        $this->mailingListService->unsubscribeBykey($key, null, $entries[0]->returnMobileHash());

        $entries = MailingListSubscriber::filter("WHERE email_address = ?", "geoff@test.com");
        $this->assertEquals(0, sizeof($entries));


    }

    public function testCanFilterMailingListsByFilterString() {
        AuthenticationHelper::login("sam@samdavisdesign.co.uk", "password");

        $this->mailingListService->saveMailingList(new MailingListSummary("filter1", "New filter 1", "My New filter mailing list 1", true));
        $this->mailingListService->saveMailingList(new MailingListSummary("filter2", "New filter 2", "My New filter mailing list 2", true));
        $this->mailingListService->saveMailingList(new MailingListSummary("filter3", "New filter 3", "My New filter mailing list 3", true));

        $results = $this->mailingListService->filterMailingLists("New filter 1");

        $this->assertEquals(1, sizeof($results));

        $results = $this->mailingListService->filterMailingLists("");

        $this->assertEquals(3, sizeof($results));

        AuthenticationHelper::logout();
    }


    public function testIfAutoResponderSetForMailingMailingIsSentOnNewSubscriptionToSubscriber() {

        AuthenticationHelper::login("admin@kinicart.com", "password");

        $mailingList = new MailingListSummary("brandnewml", "New Mailing List", "My New test mailing list", true, 3);
        $this->mailingListService->saveMailingList($mailingList);

        AuthenticationHelper::logout();

        $preferences = new GuestMailingListSubscriberPreferences([
            "brandnewml" => 1
        ], "test@noaccount.com", "07890 111111", "Test No Account",
            "My Organisation");


        $this->mailingListService->updateSubscriptionPreferences($preferences);

        $this->assertTrue($this->mailingService->methodWasCalled("processSingleMailingListSubscriberMailing", [
            3, "brandnewml", "test@noaccount.com"
        ]));


        // Check no double call if update
        $this->mailingService->resetMethodCallHistory("processSingleMailingListSubscriberMailing");


        $preferences = new GuestMailingListSubscriberPreferences([
            "brandnewml" => 1
        ], "test@noaccount.com", "07890 111111", "Name update",
            "My Organisation");


        $this->assertFalse($this->mailingService->methodWasCalled("processSingleMailingListSubscriberMailing", [
            3, "brandnewml", "test@noaccount.com"
        ]));



        $preferences = new GuestMailingListSubscriberPreferences([
            "brandnewml" => 1
        ], "another@noaccount.com", "07890 111111", "Test No Account",
            "My Organisation");


        $this->mailingListService->updateSubscriptionPreferences($preferences);

        $this->assertTrue($this->mailingService->methodWasCalled("processSingleMailingListSubscriberMailing", [
            3, "brandnewml", "another@noaccount.com"
        ]));

    }


}
