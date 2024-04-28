<?php

declare(strict_types=1);

namespace Duyler\ActionBus\Dto;

use Duyler\ActionBus\Formatter\IdFormatter;
use UnitEnum;

readonly class Trigger
{
    public string $id;

    public function __construct(
        string|UnitEnum $id,
        public ?object $data = null,
        public ?string $contract = null,
    ) {
        $this->id = IdFormatter::format($id);
    }
}
