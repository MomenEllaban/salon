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
        Schema::create('booking_package_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_package_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('package_service_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('qty')->default(0);
            $table->string('service_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
