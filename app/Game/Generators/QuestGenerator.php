<?php

namespace App\Game\Generators;

use App\Game\FeatureDetails\QuestDetails;
use App\Game\Interfaces\Generator;
use Illuminate\Support\Collection;

/**
 * The idea behind this class is to generate a list of Quests for a player to choose from.
 *
 * Each Quest has an level of the quest, but this is experience based.
 *
 * The higher the level the greater possibility that you will fail the quest and die, and lose experience.
 *
 * There will also be a quest at the same level as the player;
 *
 * There will also be a quest below the level of the player.
 * The lower the quest, the lower experience you will gain from that quest, on a percentage base.
 *
 * 6 quests will be generated, 2 high level, 2 same level and 2 low level
 *
 *
 * Class QuestGenerator
 * @package App\Game\Generators
 */
class QuestGenerator implements Generator
{
    /**
     * @var
     */
    private $characterLevel;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $possibleQuests;

    /**
     * QuestGenerator constructor.
     * @param $characterLevel
     */
    public function __construct($characterLevel)
    {
        $this->characterLevel = $characterLevel;

        $this->possibleQuests = config('quests.quests');
    }


    /**
     * Generate list of quests
     *
     * @return \Illuminate\Support\Collection
     */
    public function generateList(): Collection
    {
        $quests = collect();

        for ($i = 0; $i < 6; $i++) {
            if ($i == 0 || $i == 1) {
                $level = mt_rand(2, 5);
                $quest = $this->generate($level);
                $quests->push($quest);
            } elseif ($i == 2 || $i == 3) {
                $quest = $this->generate(0);
                $quests->push($quest);
            } else {
                $level = mt_rand(-5, -2);
                $quest = $this->generate($level);
                $quests->push($quest);
            }
        }

        return $quests;
    }

    /**
     * Generate a quest given a level
     *
     * @param $level
     * @return QuestDetails
     */
    public function generate($level): QuestDetails
    {
        $randomQuest = $this->randomQuest();

        $questLevel = $this->characterLevel + $level;

        if ($questLevel < 0) {
            $questLevel = $this->characterLevel;
        } else {
            $randomQuest['experienceGained'] = $randomQuest['experienceGained'] + $level;
        }

        $randomQuest['experienceLevel'] = $questLevel;

        return new QuestDetails($randomQuest);
    }

    /**
     * Get random quest from array
     *
     * @return mixed
     */
    private function randomQuest(): array
    {
        return array_random($this->possibleQuests);
    }
}
