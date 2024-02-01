<?php

declare(strict_types=1);

namespace Duyler\EventBus\Action;

use Duyler\EventBus\Action\Exception\ActionReturnValueExistsException;
use Duyler\EventBus\Action\Exception\ActionReturnValueNotExistsException;
use Duyler\EventBus\Action\Exception\ActionReturnValueWillBeCompatibleException;
use Duyler\EventBus\Action\Exception\InvalidArgumentFactoryException;
use Duyler\EventBus\Contract\ActionRunnerInterface;
use Duyler\EventBus\Dto\Action;
use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\EventBus\Internal\Event\ActionAfterRunEvent;
use Duyler\EventBus\Internal\Event\ActionBeforeRunEvent;
use Duyler\EventBus\Internal\Event\ActionThrownExceptionEvent;
use Override;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

class ActionRunner implements ActionRunnerInterface
{
    public function __construct(
        private ActionContainerProvider $actionContainerProvider,
        private ActionHandlerArgumentBuilder $argumentBuilder,
        private ActionHandlerBuilder $handlerBuilder,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @throws ActionReturnValueNotExistsException
     * @throws ActionReturnValueWillBeCompatibleException
     * @throws InvalidArgumentFactoryException
     * @throws Throwable
     * @throws ActionReturnValueExistsException
     */
    #[Override]
    public function runAction(Action $action): Result
    {
        $container = $this->actionContainerProvider->get($action);

        try {
            $actionInstance = $this->handlerBuilder->build($action, $container);
            $argument = $this->argumentBuilder->build($action, $container);
            $this->eventDispatcher->dispatch(new ActionBeforeRunEvent($action));
            $resultData = ($actionInstance)($argument);
            $result = $this->prepareResult($action, $resultData);
        } catch (Throwable $exception) {
            $this->eventDispatcher->dispatch(new ActionThrownExceptionEvent($action, $exception));
            throw $exception;
        }

        $this->eventDispatcher->dispatch(new ActionAfterRunEvent($action));

        return $result;
    }

    /**
     * @throws ActionReturnValueExistsException
     * @throws ActionReturnValueNotExistsException
     * @throws ActionReturnValueWillBeCompatibleException
     * @todo To be refactoring
     */
    private function prepareResult(Action $action, mixed $resultData): Result
    {
        if ($resultData instanceof Result) {
            if ($action->contract === null && $resultData->data !== null) {
                throw new ActionReturnValueExistsException($action->id);
            }

            if ($resultData->data !== null && $resultData->data instanceof $action->contract === false) {
                throw new ActionReturnValueWillBeCompatibleException($action->id, $action->contract);
            }

            if ($action->contract !== null && $resultData->data === null) {
                throw new ActionReturnValueNotExistsException($action->id);
            }

            return $resultData;
        }

        if ($resultData !== null) {
            if ($action->contract === null) {
                throw new ActionReturnValueExistsException($action->id);
            }

            if ($resultData instanceof $action->contract === false) {
                throw new ActionReturnValueWillBeCompatibleException($action->id, $action->contract);
            }

            return new Result(ResultStatus::Success, $resultData);
        }

        if ($action->contract !== null) {
            throw new ActionReturnValueNotExistsException($action->id);
        }

        return new Result(ResultStatus::Success);
    }
}
