<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

final class CheckUserOutOfBuilding extends Command
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var Uuid
     */
    private $buildingId;

    private function __construct(Uuid $buildingId, string $username)
    {
        $this->init();

        $this->buildingId = $buildingId;
        $this->username = $username;
    }

    public static function fromBuildingAndUsername(Uuid $buildingId, string $name) : self
    {
        return new self($buildingId, $name);
    }

    public function username() : string
    {
        return $this->username;
    }

    public function buildingId() : Uuid
    {
        return $this->buildingId;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'username' => $this->username,
            'buildingId' => (string) $this->buildingId,
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->username = (string) $payload['username'];
        $this->buildingId = Uuid::fromString($payload['buildingId']);
    }
}
