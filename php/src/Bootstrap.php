<?php


namespace Kinimailer;


use Kiniauth\Services\Workflow\Task\Task;
use Kinikit\Core\ApplicationBootstrap;
use Kinikit\Core\DependencyInjection\Container;
use Kinimailer\Services\Mailing\MailingProcessorScheduledTask;
use Kinintel\Services\Alert\AlertGroupTask;

class Bootstrap implements ApplicationBootstrap {

    public function setup() {
        Container::instance()->addInterfaceImplementation(Task::class, "mailing", MailingProcessorScheduledTask::class);
    }
}