<?php

namespace Kinimailer\Controllers\Test;

use Kinimailer\Objects\MailingList\MailingListSubscriber;

class MailingList {

    /**
     * Get the last subscriber for the mailing list key
     *
     * @http GET /lastSubscriber/$mailingListKey
     *
     * @objectInterceptorDisabled
     */
    public function lastSubscriber($mailingListKey) {

        $mailingList = \Kinimailer\Objects\MailingList\MailingList::filter("WHERE key = ?", $mailingListKey);
        if ($mailingList[0] ?? null) {
            $subscribers = MailingListSubscriber::filter("WHERE mailing_list_id = ? ORDER BY id DESC LIMIT 1", $mailingList[0]->getId());
            return $subscribers[0] ?? null;
        }

    }


}