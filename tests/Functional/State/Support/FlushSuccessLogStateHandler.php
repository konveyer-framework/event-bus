<?php

declare(strict_types=1);

namespace Duyler\EventBus\Test\Functional\State\Support;

use Duyler\EventBus\Contract\State\MainBeforeStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainBeforeService;
use Duyler\EventBus\State\StateContext;
use Override;

class FlushSuccessLogStateHandler implements MainBeforeStateHandlerInterface
{
    public function __construct(
        private string $actionId,
    ) {}

    #[Override]
    public function handle(StateMainBeforeService $stateService, StateContext $context): void
    {
        $stateService->flushSuccessLog();
    }

    #[Override]
    public function observed(StateContext $context): array
    {
        return [$this->actionId];
    }
}
