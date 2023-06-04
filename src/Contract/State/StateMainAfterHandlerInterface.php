<?php

declare(strict_types=1);

namespace Duyler\EventBus\Contract\State;

use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBus\State\StateHandlerPreparedInterface;
use Duyler\EventBus\State\StateHandlerObservedInterface;

interface StateMainAfterHandlerInterface extends StateHandlerPreparedInterface, StateHandlerObservedInterface
{
    public function handle(StateMainAfterService $stateService): void;
}