<?php

declare(strict_types=1);

namespace Building\Infrastructure\Projector;

use Building\Domain\DomainEvent\UserCheckedOutOfBuilding;

final class RemoveUserFromCheckedInUsers
{
    public function __invoke(UserCheckedOutOfBuilding $checkOut)
    {
        $json = file_get_contents(__DIR__ . '/../../../public/incremental-' . $checkOut->aggregateId());

        $users = array_flip(json_decode($json, true));

        unset($users[$checkOut->username()]);

        file_put_contents(
            __DIR__ . '/../../../public/incremental-' . $checkOut->aggregateId(),
            json_encode(array_keys($users))
        );
    }
}
