<?php

namespace Kinimailer\ValueObjects\MailingList;

abstract class MailingListSubscriberPreferences {

    /**
     * An array of booleans indexed by mailing list key
     * describing whether the list is meant to be subscribed to or not.
     *
     * @var string[boolean]
     */
    private $mailingListPreferences;

    /**
     * MailingListSubscriberPreferences constructor.
     *
     * @param string[boolean] $mailingListPreferences
     */
    public function __construct($mailingListPreferences) {
        $this->mailingListPreferences = $mailingListPreferences;
    }


    /**
     * @return string
     */
    public function getMailingListPreferences() {
        return $this->mailingListPreferences;
    }

    /**
     * @param string $mailingListPreferences
     */
    public function setMailingListPreferences($mailingListPreferences) {
        $this->mailingListPreferences = $mailingListPreferences;
    }


}