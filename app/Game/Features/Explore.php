<?php

namespace App\Game\Features;

use App\Game\FeatureDetails\Zone;
use App\Game\Generators\ExploreGenerator;
use App\Game\Interfaces\Feature;
use Illuminate\Console\Command;

/**
 * Class Explore
 * @package App\Game
 */
class Explore implements Feature
{
    /**
     * @var Zone
     */
    private $exploreDetail;

    /**
     * @var int
     */
    private $characterExperience;

    /**
     * @var Command
     */
    private $output;

    /**
     * @var int
     */
    private $enemiesEncountered = 0;

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
     *  Holds the logic for asking which Zone they would like to explore
     *
     * @return array|null
     */
    public function run(): ?array
    {
        $generator = new ExploreGenerator();

        $zones = $generator->generateList();

        $this->output->info("The higher the Danger Level, the more likely you will come across an enemy!");

        $headers = ['Zone', 'Danger Level', 'Experience Gained'];
        $zonesArray = [];
        $options = [];

        foreach ($zones as $key => $zone) {
            $array = [
                'zone' => $zone->getZone(),
                'danger_level' => $zone->getDangerLevel(),
                'experience_gained' => $zone->getExperienceGained()
            ];


            array_push($options, $array);

            array_push($zonesArray, $key . "-" . $zone->zone);
        }

        $this->output->table($headers, $options);

        $zone = $this->output->choice("Please select the zone you'd like to explore from the list above.", $zonesArray);

        $zoneArray = $zones->toArray();

        $chosenZone = $zoneArray[explode('-', $zone)[0]];

        $this->exploreDetail = $chosenZone;

        $died = $this->progressBar();

        if ($died) {
            return null;
        }

        return [
            "exp_gained" => $this->exploreDetail->getExperienceGained(),
            "enemies" => $this->enemiesEncountered
        ];
    }

    /**
     * This will create the progress bar to give the player the sense that you are actually exploring a zone
     * This will also check the enemy logic
     *
     * @return bool
     */
    private function progressBar(): bool
    {
        $bar = $this->output->getOutput()->createProgressBar(5);

        $i = 0;

        while ($i != 5) {
            sleep(1);

            $bar->advance();

            if ($this->shouldEnemyAppear()) {
                if ($this->enemyAppeared()) {
                    return true;
                }
            }

            $i++;
        }
        $bar->finish();

        return false;
    }

    /**
     * This runs on every second that you are exploring a zone.
     * Depending on the danger level, there is a percentage chance you will encounter an enemy.
     * High: 75%
     * Medium: 50%
     * Low: 25%
     *
     * @return bool
     */
    private function shouldEnemyAppear(): bool
    {
        if ($this->exploreDetail->getDangerLevel() == Zone::DANGER_LEVEL_HIGH) {
            $possibility = 75;
        } elseif ($this->exploreDetail->getDangerLevel() == Zone::DANGER_LEVEL_MEDIUM) {
            $possibility = 50;
        } else {
            $possibility = 25;
        }
        $random = mt_rand(0, 100);

        if ($possibility > $random) {
            return true;
        }

        return false;
    }

    /**
     * Basically there is a 75% chance you will kill an enemy
     *
     * @return bool
     */
    private function enemyAppeared():bool
    {
        $random = mt_rand(0, 100);

        if (75 < $random) {
            return true;
        }

        $this->enemiesEncountered += 1;

        return false;
    }
}
