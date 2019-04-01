<?php

namespace App\Game\Features;

use App\Game\FeatureDetails\QuestDetails;
use App\Game\Generators\QuestGenerator;
use App\Game\Interfaces\Feature;
use Illuminate\Console\Command;

/**
 * Class Quest
 * @package App\Game
 */
class Quest implements Feature
{
    /**
     * @var QuestDetails
     */
    private $questDetail;

    /**
     * @var int
     */
    private $characterExperience;

    /**
     * @var Command
     */
    private $output;

    /**
     * Quest constructor.
     * @param $questDetail
     */
    public function __construct(Command $output, $characterExperience)
    {
        $this->output = $output;
        $this->characterExperience = $characterExperience;
    }


    /**
     * Main logic behind asking which quest the player would like to try
     *
     * @return QuestDetails|null
     */
    public function run(): ?QuestDetails
    {
        $generator = new QuestGenerator($this->characterExperience);

        $quests = $generator->generateList();

        $questChoices = [];
        $this->output->info("Please note: Quest Experience is compared to your current experience!");
        $headers = ["Name", "Quest Experience", "Experience Gained", "Difficulty"];
        $options = [];

        foreach ($quests as $key => $quest) {
            if ($quest->getExperienceLevel() < $this->characterExperience) {
                $difficulty = "Low";
            } elseif ($quest->getExperienceLevel() > $this->characterExperience) {
                $difficulty = "High";
            } else {
                $difficulty = "Medium";
            }
            $array = [
                "name" => $quest->getName(),
                "quest_difficulty" => $quest->getExperienceLevel(),
                "experience_gained" => $quest->getExperienceGained(),
                "difficulty" => $difficulty
            ];
            array_push($options, $array);

            array_push($questChoices, $key . "-" . $quest->getName() . "-" . $quest->getDescription());
        }

        $this->output->table($headers, $options);

        $quest = $this->output->choice("Which Quest would you like to do?", $questChoices);

        $array = $quests->toArray();

        $explode = explode('-', $quest);

        $questWanted = $array[$explode[0]];

        $this->questDetail = $questWanted;

        $this->progressBar();

        if ($this->calculateChance()) {
            return null;
        }

        return $this->questDetail;
    }

    /**
     * Calculate the possibility for completing the quest.
     * Based on quest level.
     *
     * Higher the level, lower the possibility of completing the quest
     *
     * @return int
     */
    public function calculatePossibility()
    {
        $questExperienceLevel = $this->questDetail->getExperienceLevel();
        if ($questExperienceLevel == $this->characterExperience) {
            $possibility = 50;
        } elseif ($questExperienceLevel < $this->characterExperience) {
            // If quest level is less than character experience
            // start at 70, for every experience level below, add 2 experience
            $below = $questExperienceLevel - $this->characterExperience;
            $total = $below * 2;
            $possibility = 70 + $total;
        } else {
            //If it's greater than
            //Start at 20, same as above subtract add
            $below = $this->characterExperience - $questExperienceLevel;
            $total = $below * 2;
            $possibility = 35 - $total;
        }

        return $possibility;
    }

    /**
     * Calculate chance based on possibility of completing the quest based on quest level
     *
     * @return bool
     */
    public function calculateChance()
    {
        $possibility = $this->calculatePossibility();

        $random = mt_rand(0, 100);

        if ($possibility < $random) {
            return true;
        }

        return false;
    }

    /**
     * Create the progress bar to give the sense that we are completing a quest
     */
    private function progressBar()
    {
        $bar = $this->output->getOutput()->createProgressBar(5);

        $i = 0;

        while ($i != 5) {
            sleep(1);

            if ($i == 3) {
                $this->output->line("\nYou're doing really hard work right now, you best believe it!");
            }

            $bar->advance();

            $i++;
        }
        $bar->finish();
    }
}
