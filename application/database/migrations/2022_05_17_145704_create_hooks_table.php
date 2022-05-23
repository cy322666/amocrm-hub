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
        Schema::create('hooks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('lead_id')->nullable();
            $table->integer('contact_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('pipeline_id')->nullable();
            $table->integer('salesforce_id')->nullable();
            $table->boolean('is_send')->default(false);
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('position')->nullable();
            $table->string('company')->nullable();
            $table->string('manager')->nullable();
            $table->string('email_manager')->nullable();
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hooks');
    }
};
