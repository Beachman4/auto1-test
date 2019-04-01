<?php

namespace App\Game\FeatureDetails;

/**
 *
 * Details for a specific Zone/Explore
 *
 * Each Explore Zone has a danger level.
 *
 * The higher the danger level from 1 to 3, the more likely you are to encounter creatures and die
 *
 * Class Zone
 * @package App\Game\FeatureDetails
 */
class Zone
{
    /**
     * @var int
     */
    public $experienceGained;

    /**
     * @var int
     */
    public $dangerLevel;

    /**
     * @var string
     */
    public $zone;

    const DANGER_LEVEL_LOW = 1;
    const DANGER_LEVEL_MEDIUM = 2;
    const DANGER_LEVEL_HIGH = 3;

    /**
     * QuestDetails constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return int
     */
    public function getExperienceGained(): int
    {
        return $this->experienceGained;
    }

    /**
     * @return int
     */
    public function getDangerLevel(): int
    {
        return $this->dangerLevel;
    }

    /**
     * @return string
     */
    public function getZone(): string
    {
        return $this->zone;
    }
}
