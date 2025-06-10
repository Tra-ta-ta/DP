<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats_has_messages', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->primary();
            $table->integer('chats_idChat');
            $table->foreign('chats_idChat')->references('id')->on('chats')->onDelete('NO ACTION')->onUpdate('NO ACTION');
            $table->integer('messages_idMessage');
            $table->foreign('messages_idMessage')->references('id')->on('messages')->onDelete('NO ACTION')->onUpdate('NO ACTION');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats_has_messages');
    }
};
