<?php


namespace Kinimailer\Objects\Mailing;


use Kiniauth\Objects\Attachment\AttachmentSummary;
use Kinikit\Core\Communication\Email\Attachment\EmailAttachment;
use Kinikit\Core\Communication\Email\Email;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Security\Hash\SHA512HashProvider;
use Kinimailer\Objects\Template\TemplateSummary;

/**
 * Extension of email class which overrides the hash function
 * where we are using section based hashing for content.
 *
 * Class MailingEmail
 * @package Kinimailer\Objects\Mailing
 */
class MailingEmail extends Email {

    /**
     * @var TemplateSummary
     */
    private $template;

    /**
     * MailingEmail constructor.
     * @param string $from
     * @param string $replyTo
     * @param string[] $recipients
     * @param TemplateSummary $template
     * @param EmailAttachment[] $attachments
     */
    public function __construct($from, $replyTo, $recipients, $template, $subscriber = null, $attachments = []) {

        if ($template) {
            $this->template = $template;
            parent::__construct($from, $recipients, $template->returnEvaluatedTemplateTitle($subscriber), $template->returnEvaluatedTemplateText($subscriber),
                null, null, $replyTo, $attachments);
        }

    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash() {
        if (sizeof($this->template->getContentHashSections())) {
            $hashString = join(",", $this->getRecipients()) . $this->getSubject() . $this->template->returnEvaluatedContentHashString();
            $hashProvider = Container::instance()->get(SHA512HashProvider::class);
            return $hashProvider->generateHash($hashString);
        } else
            return parent::getHash();
    }


}