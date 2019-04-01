<?php

namespace App\Game\Generators;

use App\Game\FeatureDetails\Zone;
use App\Game\Interfaces\Generator;
use Illuminate\Support\Collection;

/**
 * Generates a list of zones for the player to choose from
 *
 * Class ExploreGenerator
 * @package App\Game\Generators
 */
class ExploreGenerator implements Generator
{
    /**
     * @var
     */
    private $zones;

    /**
     *  Takes the array stored in config/zones.php and adds experience
     */
    public function generateListOfZones()
    {
        $zones = config('zones.zones');

        $zonesArray = [];

        foreach ($zones as $zone) {
            $array = [
                'zone' => $zone,
                'experienceGained' => 5
            ];

            array_push($zonesArray, $array);
        }

        $this->zones = $zonesArray;
    }


    /**
     * Generates a list of zones to explore
     *
     * @return \Illuminate\Support\Collection
     */
    public function generateList(): Collection
    {
        $this->generateListOfZones();

        $zones = collect();

        for ($i = 0; $i < 6; $i++) {
            if ($i == 0 || $i == 1) {
                $zone = $this->generate(Zone::DANGER_LEVEL_LOW);
                $zones->push($zone);
            } elseif ($i == 2 || $i == 3) {
                $zone = $this->generate(Zone::DANGER_LEVEL_MEDIUM);
                $zones->push($zone);
            } else {
                $zone = $this->generate(Zone::DANGER_LEVEL_HIGH);
                $zones->push($zone);
            }
        }

        return $zones;
    }

    /**
     * Generates a zone to explore
     *
     * @param $dangerLevel
     * @return Zone
     */
    public function generate($dangerLevel): Zone
    {
        $zone = $this->getRandomZone();

        $zone['dangerLevel'] = $dangerLevel;
        if ($dangerLevel == Zone::DANGER_LEVEL_HIGH) {
            $zone['experienceGained'] += 10;
        } elseif ($dangerLevel == Zone::DANGER_LEVEL_MEDIUM) {
            $zone['experienceGained'] += 5;
        }

        $zoneObject = new Zone($zone);

        return $zoneObject;
    }

    /**
     * Retrieves a random zone from the array
     *
     * @return mixed
     */
    public function getRandomZone(): array
    {
        return array_random($this->zones);
    }
}
