<?php

namespace App\Game;

use App\Game\Features\Explore;
use App\Game\Features\Quest;
use App\SaveGame;
use Illuminate\Console\Command;

/**
 * Starts the game.
 *
 * Class Game
 * @package App\Game
 */
class Game
{
    /**
     * @var Character
     */
    private $character;

    /**
     * @var Command
     */
    private $output;

    /**
     * @var SaveGame
     */
    private $savedGame;

    /**
     * Game constructor.
     * @param $output
     */
    public function __construct(Command $output)
    {
        $this->output = $output;
    }


    /**
     * The main function. Checks for save games and runs the game loop
     */
    public function run()
    {
        $saves = SaveGame::all();

        if (count($saves) > 0) {
            $this->output->info("We saved your progress from the previous game{s}. Please select which character you'd like to continue");

            $names = [];

            foreach ($saves as $save) {
                $name = $save->character_name . '-Level:' . $save->character_level;

                array_push($names, $name);
            }
            $names[] = "New Character";

            $name = $this->output->choice("Which game would you like to continue?", $names);

            if ($name != "New Character") {
                $save = $saves->filter(function ($item) use ($name) {
                    return $item->character_name == explode('-', $name)[0];
                })->first();

                $this->savedGame = $save;

                $this->parseSave($save);

                $this->gameLoop();

                return;
            }

        }

        $this->createNewCharacter();

        $this->gameLoop();

        return;
    }

    /**
     *  Asks for the name for the new character and saves the new character
     */
    public function createNewCharacter()
    {
        $this->output->info("Create a new Character!");

        $name = $this->output->ask("What is your character's name?");

        $this->output->info("Thanks! Lets start the game!");

        $this->character = new Character(["name" => $name]);

        $this->save();
    }

    /**
     *  As the function says, this is the game loop.
     *
     *  Basically asks for the next feature, after the feature is complete, it saves progress,
     *  and then asks if they would like to continue
     */
    public function gameLoop(): void
    {
        $gameDone = false;

        while (!$gameDone) {
            $this->nextFeature();

            $this->save();

            $donePlaying = $this->output->choice("Would you like to continue playing?", ["Yes", "No"]);

            if ($donePlaying === "No") {
                $gameDone = true;
            }
        }

        $this->output->info("Here are your character statistics");

        $headers = ["Name", "Experience", "Level"];

        $array = [
            [
                "name" => $this->character->getName(),
                "experience" => $this->character->getExperience(),
                "level" => $this->character->getLevel()
            ]
        ];

        $this->output->table($headers, $array);
    }

    /**
     *  Asks the user for the next feature and then continues logic.
     *  This also sets experience, depending on the outcome of the feature
     */
    public function nextFeature()
    {
        $feature = $this->output->choice("What would you like to do?", ["Quest", "Explore"]);

        $this->output->info("Current Experience: " . $this->character->getExperience());

        if ($feature == "Quest") {
            $outcome = (new Quest($this->output, $this->character->getExperience()))->run();

            if ($outcome) {
                $this->output->info("\nYou successfully completed the quest. Good job!");
                $experience = $this->character->getExperience() + $outcome->experienceGained;
                $this->character->setExperience($experience);
            } else {
                $this->output->error("\nYou failed the quest and died. You lost 5 experience points");
                if (!$this->character->getExperience() == 0 || $this->character->getExperience() > 5) {
                    $experience = $this->character->getExperience() - 5;
                    $this->character->setExperience($experience);
                }
            }

            return;
        }


        $outcome = (new Explore($this->output, $this->character->getExperience()))->run();

        if ($outcome) {
            $enemies = $outcome['enemies'];
            if ($enemies == 0) {
                $this->output->info("\nYou successfully explored the zone!");
            } else {
                $string = $enemies == 1 ? "$enemies enemy" : "$enemies enemies";
                $this->output->info("\nYou successfully explored the zone and defeated $string!");
            }
            $expPerEnemy = 5;
            $extraExp = $enemies * $expPerEnemy;

            $experienceGained = $outcome['exp_gained'] + $extraExp;

            $experience = $this->character->getExperience() + $experienceGained;

            $this->character->setExperience($experience);
        } else {
            $this->output->error("\nYou were killed by an enemy! You have lost 5 experience points.");
            if (!$this->character->getExperience() == 0 || $this->character->getExperience() > 5) {
                $experience = $this->character->getExperience() - 5;
                $this->character->setExperience($experience);
            }
        }

    }

    /**
     *  Saves the progress for the current character
     */
    public function save()
    {
        $array = [
            'character' => $this->character
        ];

        $json = json_encode($array);

        if ($this->savedGame) {
            $this->savedGame->game_details = $json;
            $this->savedGame->character_level = $this->character->getLevel();
            $this->savedGame->save();

            return;
        }

        $this->savedGame = SaveGame::create([
            'character_name' => $this->character->name,
            'character_level' => $this->character->getLevel(),
            'game_details' => $json
        ]);

        return;
    }

    /**
     * Parses the details from the saved game.
     *
     * @param SaveGame $saveGame
     */
    private function parseSave(SaveGame $saveGame)
    {
        $details = json_decode($saveGame->game_details, true);

        $this->character = new Character($details['character']);
    }

    // Getters and Setters

    /**
     * @return mixed
     */
    public function getCharacter(): Character
    {
        return $this->character;
    }

    /**
     * @param mixed $character
     * @return Game
     */
    public function setCharacter($character): Game
    {
        $this->character = $character;

        return $this;
    }
}
