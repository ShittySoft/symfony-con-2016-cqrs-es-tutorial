<?php

declare(strict_types=1);

namespace Building\Infrastructure\Projector;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIntoBuilding;

final class AddBuilding
{
    public function __invoke(NewBuildingWasRegistered $registered)
    {
        file_put_contents(__DIR__ . '/../../../public/incremental-' . $registered->aggregateId(), '[]');
    }
}
