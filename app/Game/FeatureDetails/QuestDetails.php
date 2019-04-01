<?php

namespace App\Game\FeatureDetails;

/**
 * Details for a specific Quest.
 *
 * Class QuestDetails
 * @package App\Game\FeatureDetails
 */
class QuestDetails
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $experienceGained;

    /**
     * @var int
     */
    public $experienceLevel;

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
     * @return mixed
     */
    public function getExperienceGained(): int
    {
        return $this->experienceGained;
    }

    /**
     * @return mixed
     */
    public function getExperienceLevel(): int
    {
        return $this->experienceLevel;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
