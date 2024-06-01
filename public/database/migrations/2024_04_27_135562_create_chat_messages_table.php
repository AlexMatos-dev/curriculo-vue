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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('chat_message_id', true);
            $table->string('message', 700);
            $table->unsignedBigInteger('sender_message_id');
            $table->string('sender_message_table_name', 30);
            $table->unsignedBigInteger('receiver_message_id');
            $table->string('receiver_message_table_name', 30);
            $table->unsignedBigInteger('chat_attachment_id')->nullable();
            $table->tinyInteger('message_read')->default(0);
            $table->string('category', 20);
            $table->unsignedBigInteger('job_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
