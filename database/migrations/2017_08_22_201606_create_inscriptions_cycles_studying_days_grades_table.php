<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInscriptionsCyclesStudyingDaysGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscriptions_cycles_studying_days_grades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('state')->default(1);

            $table->integer('csdg')->unsigned();
            $table->foreign('csdg')->references('id')->on('cycles_studying_days_grades')->onDelete('cascade');
            $table->integer('inscription')->unsigned();
            $table->foreign('inscription')->references('id')->on('inscriptions')->onDelete('cascade');

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
        Schema::dropIfExists('inscriptions_cycles_studying_days_grades');
    }
}
