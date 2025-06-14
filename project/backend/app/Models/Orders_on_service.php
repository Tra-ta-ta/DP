<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders_on_service extends Model
{
    use SoftDeletes;
    protected $fillable = ['rooms_idRoom', 'users_idUser', 'services_idService', 'status'];
    public function rooms()
    {
        return $this->belongsTo(Room::class, 'rooms_idRoom');
    }
    public function checkRoom()
    {
        $room = Room::withTrashed()->where('id', '=', $this->rooms_idRoom)->first();
        return $room;
    }
    public function checkUser()
    {
        $user = User::find($this->users_idUser);
        return $user;
    }
    public function checkService()
    {
        $service = Service::withTrashed()->where('id', '=', $this->services_idService)->first();
        return $service;
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'services_idService');
    }
}
