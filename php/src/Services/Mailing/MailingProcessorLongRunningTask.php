<?php


namespace Kinimailer\Services\Mailing;


use Kiniauth\Services\Workflow\Task\LongRunning\LongRunningTask;
use Kinikit\Core\DependencyInjection\Container;

class MailingProcessorLongRunningTask extends LongRunningTask {

    /**
     * @var integer
     */
    private $mailingId;

    /**
     * @var MailingService
     */
    private $mailingService;

    /**
     * MailingProcessorLongRunningTask constructor.
     *
     * @param integer $mailingId
     * @param MailingService $mailingService
     */
    public function __construct($mailingId, $mailingService = null) {
        $this->mailingId = $mailingId;
        $this->mailingService = $mailingService ?? Container::instance()->get(MailingService::class);


    }

    /**
     * Run function
     *
     * @return mixed|void
     */
    public function run() {

        // Process mailing
        $this->mailingService->processMailing($this->mailingId, $this);
    }
}