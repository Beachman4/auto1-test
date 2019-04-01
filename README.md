
Whoa.

First let me say, this project was incredibly fun.

I'm personally a huge fan of rpgs. I made this kind of resembling World of Warcraft. The zones listed in `config/zones.php` are WoW Zones.

I implemented all features.  
I combined exploration and fighting, by determining fighting based on a danger level for zones.
I also included quests, located in `config/quests.php`.
I only included a few, but you can add more and the selection of quests will be based off of this.

Originally, I was going to do the RESTful approach, because that's what I've done throughout my career,
however the command line seemed more approriate for this type of application.

For RESTFul, I would need to find some way to keep track of state, whether that be with a frontend or in the backend using tables/redis.

Allow me to break down the application.

The command is located in `app/Console/Commands/PlayGame.php`.

All it does is create the Game and once it finishes the game cycle, it'll save.

In `app/Game/Game.php`, this is where we start the game, check for saved games and run the game loop.

The Game Loop is a while loop that will ask the player what they would like to do and then call that feature based on their response.

I decided to save progress on each loop, in the event the player exits the console and is not able to get to the point of the command where it saves(in `app/Console/Commands/PlayGame.php` line 47).

Depending on the users input, the app will generate a list of zones or quests for the user to complete and each zone/quest has a number of experience it will give.

Zone difficulty is based on a scale from 1-3. 1 being the lowest and 3 being the highest. 2 and 3 have increased experience gains.

With exploring zones, you have the possibility of running into enemies. This possibility goes up as the difficulty goes up. Your chance to defeat the enemy stays the same, however your chance to encounter more enemies goes up( 5 max ).

For each enemy you kill, that will add 5 experience onto the experience gained from the zone.

For quests, experience is based on the amount you give it, but it is also affected by the experience level of the quest.

For example, you have 40 experience points, the generator is going to generate 6 quests. 2 low difficulty, 2 medium and 2 hard.  The low difficulty will have reduced experience depending on how less the experience level of the quest. Medium stays the same, where as higher, the experience gain goes up depending on the experience level.

With this app, you also have the chance to die. I'm not a fan of games that make you start over whenever you die, unless it's some sort of hardmode feature, so when you die, you lose 5 experience points.  Should it be lower? Probably, but I wanted it to feel like a setback, while not being a huge setback.

Okay, lets get down to how you run this application.

# How to run the application

I included a blank sqlite file in database, called database.sqlite.
##### ** Note ** You can run this using mysql. Please look below for instructions for that.

First, we need to get the dependencies.

    composer install

Second, we need to migrate the save_games table.

    php artisan migrate
    
And that's it. To start a game, run

    php artisan game:play
    
    
Also, if you want to have a fresh install, just run,

    php artisan migrate:fresh
    
    
# Requirements
- PHP >= 7.1
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- SQLite (sqlite3 and libsqlite3-dev)
- SQLite3 PHP Extension(php7.0-sqlite3)
    
    
# Testing

Just run the command,

    phpunit


## Things to do and improve on
I actually fully plan to continue this, because it's something I enjoyed so much, I want to expand on it.

- Classes, like warrior or paladin
- Skills, like strength, agility, intellect
- Complete the RESTFul approach and implement a front-end to make it look pretty
- Gear

#### Below are things that would be nice
- Implement User system
- Allow multiplayer system where you can fight others or join others



# To run on Mysql

You simply need to edit .env and change DB_CONNECTION to mysql and add the details below that.