<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;
    protected $fillable = ['users_idUser', 'typeRoom_idTypeRoom', 'number', 'statusRoom', 'isBusy'];
    public function typeCheck()
    {
        $type = TypeRoom::find($this->typeRoom_idTypeRoom);
        return $type;
    }
    public function types()
    {
        return $this->belongsTo(TypeRoom::class, 'typeRoom_idTypeRoom');
    }
    protected static function factory()
    {
        return RoomFactory::new();
    }
}
