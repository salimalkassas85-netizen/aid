<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishes', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('sender_name');
            $table->string('receiver_name');
            $table->string('relationship');
            $table->string('style');
            $table->string('audio_style')->nullable();
            $table->text('message');
            $table->string('audio_path')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('facebook_shares')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishes');
    }
};
