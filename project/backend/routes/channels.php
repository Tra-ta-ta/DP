<?php

use App\Broadcasting\ChatChannel;
use App\Broadcasting\ChatsMenager;
use App\Broadcasting\OrderChannel;
use App\Broadcasting\PersonalChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('Chat.{id}', ChatChannel::class);
Broadcast::channel('Order.{id}', OrderChannel::class);
Broadcast::channel('Personal.{id}', PersonalChannel::class);
Broadcast::channel('ChatMenager', ChatsMenager::class);
