<?php


namespace Kinimailer\Objects\MailingList;


use Kinikit\Persistence\ORM\ActiveRecord;

class MailingListSummary extends ActiveRecord {


    /**
     * @var integer
     */
    protected $id;


    /**
     * @var string
     * @required
     */
    protected $key;

    /**
     * @var string
     * @required
     */
    protected $title;


    /**
     * @var string
     */
    protected $description;


    /**
     * @var boolean
     */
    protected $anonymousSignUp = false;


    /**
     * MailingListSummary constructor.
     *
     * @param string $key
     * @param string $title
     * @param string $description
     * @param boolean $anonymousSignUp
     * @param integer $id
     */
    public function __construct($key, $title, $description = null, $anonymousSignUp = false, $id = null) {
        $this->key = $key;
        $this->title = $title;
        $this->description = $description;
        $this->anonymousSignUp = $anonymousSignUp;
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->key;
    }


    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }


    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isAnonymousSignUp() {
        return $this->anonymousSignUp;
    }


}