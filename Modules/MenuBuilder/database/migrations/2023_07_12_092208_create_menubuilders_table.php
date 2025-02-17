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
        Schema::create('menubuilders', function (Blueprint $table) {
            $table->id();
            $table->string('menu_type')->default('vertical');
            $table->string('menu_item_type')->default('link');
            $table->string('title');
            $table->string('nickname')->nullable();
            $table->string('short_title')->nullable();
            $table->boolean('is_route')->default(true);
            $table->string('route')->nullable();
            $table->string('url')->nullable();
            $table->string('active')->nullable()->default('/');

            $table->integer('order')->default(0);
            $table->tinyinteger('menu_level')->default(0);

            $table->string('start_icon')->nullable();
            $table->string('end_icon')->nullable();

            $table->unsignedBigInteger('parent_id')->nullable();

            $table->string('link_class')->nullable();
            $table->string('item_class')->nullable();

            $table->longText('permission')->nullable();
            $table->longText('role')->nullable();

            $table->boolean('is_tooltip')->default(false);
            $table->boolean('is_disabled')->default(false);

            $table->boolean('status')->default(true);
            $table->string('target_type')->default('_self')->nullable();
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
        Schema::dropIfExists('menubuilders');
    }
};
