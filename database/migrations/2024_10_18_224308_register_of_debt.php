<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('register_of_debt', function (Blueprint $table) {
            $table->string('uuid')->nullable(false)->unique()->index();
            $table->string('amount');
            $table->string('dueDate');
            $table->string('name');
            $table->string('email');
            $table->string('government_id');
            $table->unsignedBigInteger('collectionlist_id');
            $table->dateTime('notified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_of_debt');
    }
};
