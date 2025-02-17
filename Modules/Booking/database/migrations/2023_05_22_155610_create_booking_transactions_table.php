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
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('booking_id');

            $table->string('external_transaction_id')->nullable();

            $table->string('transaction_type')->nullable();

            $table->double('discount_percentage')->default(0);

            $table->double('discount_amount')->default(0);

            $table->double('tip_amount')->default(0);

            $table->longText('tax_percentage')->nullable();

            $table->boolean('payment_status')->default(0);
            $table->string('request_token')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

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
        Schema::dropIfExists('booking_transactions');
    }
};
