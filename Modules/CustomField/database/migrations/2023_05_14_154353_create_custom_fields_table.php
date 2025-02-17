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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->Integer('custom_field_group_id')->nullable();
            $table->string('label')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('required')->default(0);
            $table->text('values')->nullable();
            $table->integer('is_export')->nullable()->default(0);
            $table->enum('visible', ['true', 'false'])->default('false')->nullable();
            $table->integer('is_view')->nullable()->default(0);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_fields');
    }
};
