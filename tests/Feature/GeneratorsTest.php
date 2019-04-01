<?php

namespace Tests\Feature;

use App\Game\Character;
use App\Game\Generators\ExploreGenerator;
use App\Game\Generators\QuestGenerator;
use Tests\TestCase;

class GeneratorsTest extends TestCase
{

    private $character;

    private function getCharacter()
    {
        if ($this->character) {
            return $this->character;
        }

        $values = [
            "name" => "Test"
        ];

        return new Character($values);
    }

    public function testQuestGenerator()
    {
        $generator = new QuestGenerator($this->getCharacter()->getExperience());

        $this->assertEquals(6, count($generator->generateList()));

    }

    public function testQuestGeneratorForLevel()
    {
        $generator = new QuestGenerator(10);

        $quest = $generator->generate(0);

        $this->assertEquals(10, $quest->getExperienceLevel());
    }

    public function testZonesGenerator()
    {
        $generator = new ExploreGenerator();

        $this->assertEquals(6, count($generator->generateList()));

    }

    public function testZonesGeneratorForLevel()
    {
        $generator = new ExploreGenerator();
        $generator->generateListOfZones();

        $zone = $generator->generate(3);

        $this->assertEquals(15, $zone->getExperienceGained());
    }
}
