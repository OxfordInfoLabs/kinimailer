<?php


namespace Kinimailer\Services\Mailing;


use Kiniauth\Services\Workflow\Task\Task;

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
        $this->mailingService->processMailing($configuration, true);
    }
}