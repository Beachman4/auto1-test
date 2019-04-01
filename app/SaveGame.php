<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaveGame extends Model
{
    protected $fillable = ['character_name', 'game_details', 'character_level'];
}
