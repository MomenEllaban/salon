<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistic_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description');
            $table->string('mobile')->nullable();
            $table->integer('logistic_id');
            $table->integer('country_id');
            $table->integer('state_id');
            $table->double('standard_delivery_charge')->default(0.00);
            $table->double('express_delivery_charge')->default(0.00);
            $table->string('standard_delivery_time')->nullable()->comment('1 - 3 days');
            $table->string('express_delivery_time')->nullable()->comment('1 day');
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logistic_zones');
    }
};
