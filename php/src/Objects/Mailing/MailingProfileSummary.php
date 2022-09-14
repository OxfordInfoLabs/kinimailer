<?php

namespace Kinimailer\Objects\Mailing;

use Kinikit\Persistence\ORM\ActiveRecord;

class MailingProfileSummary extends ActiveRecord {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @required
     */
    protected $title;

    /**
     * @var string
     * @required
     */
    protected $fromAddress;

    /**
     * @var string
     * @required
     */
    protected $replyToAddress;


    /**
     * MailingProfileSummary constructor.
     *
     * @param string $title
     * @param string $fromAddress
     * @param string $replyToAddress
     */
    public function __construct($title, $fromAddress, $replyToAddress, $id = null) {
        $this->title = $title;
        $this->fromAddress = $fromAddress;
        $this->replyToAddress = $replyToAddress;
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFromAddress() {
        return $this->fromAddress;
    }

    /**
     * @param string $fromAddress
     */
    public function setFromAddress($fromAddress) {
        $this->fromAddress = $fromAddress;
    }

    /**
     * @return string
     */
    public function getReplyToAddress() {
        return $this->replyToAddress;
    }

    /**
     * @param string $replyToAddress
     */
    public function setReplyToAddress($replyToAddress) {
        $this->replyToAddress = $replyToAddress;
    }


}
