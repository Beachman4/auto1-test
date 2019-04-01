<?php

namespace Tests\Feature;

use App\Game\Character;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CharacterTest extends TestCase
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

    public function testShouldReturnCorrectLevel()
    {
        $character = $this->getCharacter();

        $this->assertEquals(1, $character->getLevel());
    }

    public function testShouldReturnCorrectLevelWhenGivenExperience()
    {
        $character = $this->getCharacter();

        $character->setExperience(100);

        $this->assertEquals(6, $character->getLevel());
    }
}
