<?php


namespace Kinimailer\ValueObjects\MailingList;

/**
 * Class DirectMailingListSubscriberPreferences
 * @package Kinimailer\ValueObjects\MailingList
 */
class GuestMailingListSubscriberPreferences extends MailingListSubscriberPreferences {

    use GuestMailingListSubscriberTrait;

    /**
     * GuestMailingListSubscriberPreferences constructor.
     *
     * @param string[boolean] $mailingListPreferences
     * @param string $emailAddress
     * @param string $mobileNumber
     * @param string $name
     */
    public function __construct($mailingListPreferences, $emailAddress = null, $mobileNumber = null, $name = null) {
        parent::__construct($mailingListPreferences);
        $this->emailAddress = $emailAddress;
        $this->mobileNumber = $mobileNumber;
        $this->name = $name;
    }


}