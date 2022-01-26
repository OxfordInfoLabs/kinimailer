<?php


namespace Kinimailer\ValueObjects\MailingList;


class UserMailingListSubscriberPreferences extends MailingListSubscriberPreferences {

    /**
     * @var integer
     */
    private $userId;

    /**
     * Constructor
     *
     * UserMailingListSubscriberPreferences constructor.
     * @param $mailingListPreferences
     * @param $userId
     */
    public function __construct($mailingListPreferences, $userId) {
        parent::__construct($mailingListPreferences);
        $this->userId = $userId;
    }


    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }


}