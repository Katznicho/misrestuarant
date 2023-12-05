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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('amount');
            $table->string('phone_number');
            $table->string("payment_mode");
            $table->text('description')->nullable;
            $table->string('reference');
            $table->foreignId("customer_id")->references("id")->on("customers")->onDelete("cascade")->nullable();
            $table->foreignId("card_id")->references("id")->on("cards")->onDelete("cascade")->nullable();
            $table->foreignId("branch_id")->references("id")->on("branches")->onDelete("cascade")->nullable();
            $table->foreignId("user_id")->references("id")->on("users")->onDelete("cascade")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};