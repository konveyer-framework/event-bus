<?php

declare(strict_types=1);

namespace Duyler\EventBus\Contract\State;

use Duyler\EventBus\State\Service\StateActionAfterService;
use Duyler\EventBus\State\StateHandlerPreparedInterface;
use Duyler\EventBus\State\StateHandlerObservedInterface;

interface StateActionAfterHandlerInterface extends StateHandlerPreparedInterface, StateHandlerObservedInterface
{
    public function handle(StateActionAfterService $stateService): void;
}