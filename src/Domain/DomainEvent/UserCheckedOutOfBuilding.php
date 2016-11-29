<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class UserCheckedOutOfBuilding extends AggregateChanged
{
    public static function fromBuildingAndUsername(Uuid $buildingId, string $username) : self
    {
        return self::occur((string) $buildingId, ['username' => $username]);
    }

    public function username() : string
    {
        return $this->payload['username'];
    }
}
