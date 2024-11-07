<?php

declare(strict_types=1);

namespace Duyler\EventBus\Internal\Listener\State;

use Duyler\EventBus\Bus\State;
use Duyler\EventBus\Contract\StateMainInterface;
use Duyler\EventBus\Internal\Event\TaskBeforeRunEvent;

class StateMainBeforeEventListener
{
    public function __construct(
        private StateMainInterface $stateMain,
        private State $state,
    ) {}

    public function __invoke(TaskBeforeRunEvent $event): void
    {
        $this->state->setBeginAction($event->task->action->id);
        $this->stateMain->before($event->task);
    }
}
