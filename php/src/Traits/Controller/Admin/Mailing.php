<?php

namespace Kinimailer\Traits\Controller\Admin;

use Kiniauth\Objects\Account\Account;
use Kinimailer\Objects\Mailing\MailingSummary;

trait Mailing {

    /**
     * Save a mailing object
     *
     * @http POST /
     *
     * @param MailingSummary $mailingSummary
     * @return int|null
     */
    public function saveMailing($mailingSummary) {
        return $this->mailingService->saveMailing($mailingSummary, null, 0);
    }

    use \Kinimailer\Traits\Controller\Account\Mailing;

}