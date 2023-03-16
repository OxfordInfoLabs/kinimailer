<?php


namespace Kinimailer\Services\Mailing;


use Kiniauth\Services\Workflow\Task\Task;
use Kinimailer\Objects\Mailing\Mailing;

class MailingProcessorScheduledTask implements Task {

    /**
     * @var MailingService
     */
    private $mailingService;

    /**
     * MailingProcessorScheduledTask constructor.
     *
     * @param MailingService $mailingService
     */
    public function __construct($mailingService = null) {
        $this->mailingService = $mailingService;
    }

    /**
     * Run the process mailing function for the configured mailing with runNow flag to ensure it runs
     *
     * @param $configuration
     * @return bool|void
     */
    public function run($configuration) {
        
        // Grab the mailing and ensure that it is in scheduled status before proceeding
        $mailing = $this->mailingService->getMailing($configuration);

        if ($mailing->getStatus() == Mailing::STATUS_SCHEDULED) {
            $this->mailingService->processMailing($configuration);
        }
    }
}