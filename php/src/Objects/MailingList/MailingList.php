<?php

namespace Kinimailer\Objects\MailingList;

use Kiniauth\Objects\Account\Account;
use Kiniauth\Traits\Account\AccountProject;
use Kinikit\Core\Validation\FieldValidationError;

/**
 * @table km_mailing_list
 * @generate
 */
class MailingList extends MailingListSummary {


    // Add account project standard attributes
    use AccountProject;


    /**
     * Construct a new mailing list
     *
     * MailingList constructor.
     *
     * @param MailingListSummary $mailingListSummary
     * @param string $projectKey
     * @param integer $accountId
     */
    public function __construct($mailingListSummary, $projectKey = null, $accountId = null) {
        if ($mailingListSummary) {
            parent::__construct($mailingListSummary->getKey(), $mailingListSummary->getTitle(), $mailingListSummary->getDescription(), $mailingListSummary->isAnonymousSignUp(), $mailingListSummary->getId());
        }
        $this->projectKey = $projectKey;
        $this->accountId = $accountId;
    }

    /**
     * @param string $key
     */
    public function setKey($key) {
        $this->key = $key;
    }


    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }


    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }


    /**
     * @param bool $anonymousSignUp
     */
    public function setAnonymousSignUp($anonymousSignUp) {
        $this->anonymousSignUp = $anonymousSignUp;
    }


    /**
     * Check for duplicate mailing lists with same key in particular
     */
    public function validate() {
        $validationErrors = [];

        $existingKeyMatches = MailingList::values("COUNT(*)", "WHERE key = ? AND account_id = ? AND id <> ?", $this->key, $this->accountId, $this->id ?? -1);
        if ($existingKeyMatches[0] > 0) {
            $validationErrors["key"]["duplicate"] = new FieldValidationError("key", "duplicate", "A mailing list already exists with this key");
        }

        return $validationErrors;
    }


    /**
     * @return MailingListSummary
     */
    public function returnSummary() {
        return new MailingListSummary($this->key, $this->title, $this->description, $this->anonymousSignUp, $this->id);
    }


}