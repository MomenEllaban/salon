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
        Schema::create('product_tax_mappings', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('tax_id');
            $table->double('tax_value')->default(0.00);
            $table->string('tax_type')->default('amount')->comment('flat / percent');
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
        Schema::dropIfExists('product_tax_mappings');
    }
};
