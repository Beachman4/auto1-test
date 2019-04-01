<?php

namespace App\Console\Commands;

use App\Game\Game;
use Illuminate\Console\Command;

class PlayGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:play';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the game!';

    private $game;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->game = new Game($this);

        $this->game->run();

        $this->game->save();

        $this->info("Not to worry, your progress has been saved!");

        $this->info("Thanks for playing!");

        return;
    }
}
