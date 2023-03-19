<?php

declare(strict_types=1);

namespace Duyler\EventBus\Storage;

use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Task;
use RecursiveArrayIterator;
use function array_flip;
use function array_intersect_key;

class TaskStorage extends AbstractStorage
{
    public function save(Task $task): void
    {
        $this->data[$task->action->id] = $task;
    }

    public function getAllByRequired(RecursiveArrayIterator $required): array
    {
        return array_intersect_key($this->data, array_flip($required->getArrayCopy()));
    }

    public function getResult(string $actionId): ?Result
    {
        return $this->data[$actionId]->result ?? null;
    }

    public function get(string $actionId): Task
    {
        return $this->data[$actionId];
    }
}