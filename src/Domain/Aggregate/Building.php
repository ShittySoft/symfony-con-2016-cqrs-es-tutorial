<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIntoBuilding;
use Building\Domain\DomainEvent\UserCheckedOutOfBuilding;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var <string, null>array
     */
    private $checkedInUsers = [];

    public static function new(string $name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        if (array_key_exists($username, $this->checkedInUsers)) {
            throw new \DomainException(sprintf(
                'The user "%s" is already checked into building "%s" (%s)',
                $username,
                $this->name,
                (string) $this->uuid
            ));
        }

        $this->recordThat(UserCheckedIntoBuilding::fromBuildingAndUsername(
            $this->uuid,
            $username
        ));
    }

    public function checkOutUser(string $username)
    {
        if (! array_key_exists($username, $this->checkedInUsers)) {
            throw new \DomainException(sprintf(
                'The user "%s" is not checked into building "%s" (%s)',
                $username,
                $this->name,
                (string) $this->uuid
            ));
        }

        $this->recordThat(UserCheckedOutOfBuilding::fromBuildingAndUsername(
            $this->uuid,
            $username
        ));
    }

    protected function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    protected function whenUserCheckedIntoBuilding(UserCheckedIntoBuilding $event)
    {
        $this->checkedInUsers[$event->username()] = null;
    }

    protected function whenUserCheckedOutOfBuilding(UserCheckedOutOfBuilding $event)
    {
        unset($this->checkedInUsers[$event->username()]);
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }
}
