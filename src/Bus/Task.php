<?php

declare(strict_types=1);

namespace Duyler\EventBus\Bus;

use Duyler\EventBus\Dto\Action;
use Duyler\EventBus\Dto\Result;
use Closure;
use Fiber;
use LogicException;

class Task
{
    public readonly Action $action;
    private mixed $value = null;
    private ?Fiber $fiber = null;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function run(Closure $actionHandler): void
    {
        $this->fiber = new Fiber($actionHandler);
        $this->value = $this->fiber->start();
    }

    public function isRunning(): bool
    {
        return $this->fiber && $this->fiber->isSuspended();
    }

    public function resume(mixed $data = null): void
    {
        $this->value = $this->fiber?->resume($data) ?? throw new LogicException('Fiber is not running');
    }

    /** @psalm-suppress MixedReturnStatement */
    public function getResult(): Result
    {
        return $this->fiber?->getReturn() ?? throw new LogicException('Fiber is not running');
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
