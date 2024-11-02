<?php

declare(strict_types=1);

namespace Duyler\EventBus\State\Service;

use Duyler\EventBus\Internal\Event\BusIsResetEvent;
use Duyler\EventBus\Service\ActionService;
use Duyler\EventBus\Service\QueueService;
use Duyler\EventBus\Service\EventService;
use Duyler\EventBus\State\Service\Trait\ActionServiceTrait;
use Duyler\EventBus\State\Service\Trait\QueueServiceTrait;
use Duyler\EventBus\State\Service\Trait\EventServiceTrait;
use Psr\EventDispatcher\EventDispatcherInterface;

class StateMainCyclicService
{
    use QueueServiceTrait;
    use ActionServiceTrait;
    use EventServiceTrait;

    public function __construct(
        private readonly QueueService $queueService,
        private readonly ActionService $actionService,
        private readonly EventService $eventService,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function reset(): void
    {
        $this->eventDispatcher->dispatch(new BusIsResetEvent());
    }
}
