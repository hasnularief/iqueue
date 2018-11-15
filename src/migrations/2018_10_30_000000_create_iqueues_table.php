<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIqueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iqueues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('location');
            $table->char('type', 2);
            $table->string('name')->nullable();
            $table->integer('number')->unsigned();
            $table->integer('counter')->unsigned()->nullable();
            $table->timestamp('called_at')->nullable();
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
        Schema::dropIfExists('iqueues');
    }
}
