<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    protected $fillable = ['name', 'thirdname', 'surname', 'phone', 'login', 'password', 'roles_idRole'];
    protected $hidden = ['password'];
    public function roles()
    {
        return $this->belongsTo(Role::class, 'roles_idRole');
    }
    public function orders()
    {
        $query = Order::withTrashed();
        $query->where('users_iduser', $this->id);
        $orders = $query->get();
        return $orders;
    }
    public function getRoom()
    {
        $order = Order::query()->where('users_iduser', $this->id)->first();
        return $order->rooms_idRoom;
    }
    public function getOrder()
    {
        $order = Order::query()->where('users_iduser', $this->id)->first();
        return $order->id;
    }
    public function getServices()
    {
        $services = Orders_on_service::with(['service' => function ($query) {
            $query->withTrashed();
        }])->where('users_iduser', $this->id)->get();

        return $services;
    }
    public function isAdmin()
    {
        $role = Role::find($this->roles_idRole);
        return $role->value == 'Администратор' ? true : false;
    }
    public function isUser()
    {
        $role = Role::find($this->roles_idRole);
        return $role->value == 'Пользователь' ? true : false;
    }
    public function isPersonal()
    {
        $role = Role::find($this->roles_idRole);
        return $role->value == 'Персонал' ? true : false;
    }
    public function isMenager()
    {
        $role = Role::find($this->roles_idRole);
        return $role->value == 'Менеджер' ? true : false;
    }
    public function currentRoom()
    {
        $room = Room::where('users_idUser', '=', $this->id)->first();
        return $room;
    }
    public function chats()
    {
        return $this->belongsToMany(
            Chat::class,
            'chats_has_user',
            'users_idUser',
            'chats_idChat'
        );
    }
}
