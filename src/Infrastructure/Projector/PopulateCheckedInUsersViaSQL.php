<?php

declare(strict_types=1);

namespace Building\Infrastructure\Projector;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIntoBuilding;
use Building\Domain\DomainEvent\UserCheckedOutOfBuilding;
use Doctrine\DBAL\Connection;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream\StreamName;

final class PopulateCheckedInUsersViaSQL
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke()
    {
        $buildings = [];

        $sql = <<<'SQL'
SELECT
  event_name, aggregate_id, payload
FROM event_stream
WHERE event_name IN(?, ?, ?)
ORDER BY aggregate_id, version ASC
SQL;

        $statement = $this->connection->executeQuery(
            $sql,
            [
                NewBuildingWasRegistered::class,
                UserCheckedIntoBuilding::class,
                UserCheckedOutOfBuilding::class
            ]
        );

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            if ($row['event_name'] === NewBuildingWasRegistered::class) {
                $buildings[$row['aggregate_id']] = [];
            }

            if ($row['event_name'] === UserCheckedIntoBuilding::class) {
                $buildings[$row['aggregate_id']][json_decode($row['payload'], true)['username']] = null;
            }

            if ($row['event_name'] === UserCheckedOutOfBuilding::class) {
                unset($buildings[$row['aggregate_id']][json_decode($row['payload'], true)['username']]);
            }
        }

        $usersInBuildings = array_map('array_keys', $buildings);

        foreach ($usersInBuildings as $buildingId => $users) {
            file_put_contents(__DIR__ . '/../../../public/sql-' . $buildingId, json_encode($users));
        }
    }
}
