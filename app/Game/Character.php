<?php

namespace App\Game;

/**
 * The Character class.
 *
 * This is used to keep track of experience for the character and is saved in the database
 *
 * Class Character
 * @package App\Game
 */
class Character
{
    /**
     * @var int
     */
    private $perLevelExp = 20;

    /**
     * @var int
     */
    public $experience = 0;

    /**
     * @var string
     */
    public $name;

    /**
     * Character constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Calculates the character's level based on experience
     *
     * @return int
     */
    public function getLevel(): int
    {
        return intval($this->getExperience() / $this->perLevelExp) + 1;
    }


    //Getters and Setters

    /**
     * @return int
     */
    public function getPerLevelExp(): int
    {
        return $this->perLevelExp;
    }

    /**
     * @return mixed
     */
    public function getExperience(): int
    {
        return $this->experience;
    }

    /**
     * @param mixed $experience
     */
    public function setExperience($experience): Character
    {
        $this->experience = $experience;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): Character
    {
        $this->name = $name;

        return $this;
    }
}
