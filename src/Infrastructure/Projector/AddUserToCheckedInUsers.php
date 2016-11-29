<?php

declare(strict_types=1);

namespace Building\Infrastructure\Projector;

use Building\Domain\DomainEvent\UserCheckedIntoBuilding;

final class AddUserToCheckedInUsers
{
    public function __invoke(UserCheckedIntoBuilding $checkedIn)
    {
        $json = file_get_contents(__DIR__ . '/../../../public/incremental-' . $checkedIn->aggregateId());

        $users = array_flip(json_decode($json, true));

        $users[$checkedIn->username()] = null;

        file_put_contents(
            __DIR__ . '/../../../public/incremental-' . $checkedIn->aggregateId(),
            json_encode(array_keys($users))
        );
    }
}
