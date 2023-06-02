<?php

declare(strict_types=1);

namespace Duyler\EventBus\Contract\State;

use Duyler\EventBus\State\Service\StateMainBeforeService;
use Duyler\EventBus\State\StateHandlerPreparedInterface;
use Duyler\EventBus\State\StateHandlerObservedInterface;

interface StateMainBeforeHandlerInterface extends StateHandlerPreparedInterface, StateHandlerObservedInterface
{
    public function handle(StateMainBeforeService $stateService): void;
}
