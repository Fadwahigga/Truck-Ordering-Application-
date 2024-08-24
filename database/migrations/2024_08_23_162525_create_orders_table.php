<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pickup_location');
            $table->string('delivery_location');
            $table->string('size');
            $table->string('weight');
            $table->timestamp('pickup_time')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            $table->timestamp('delivery_time')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            $table->enum('status', ['pending', 'in progress', 'delivered'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
