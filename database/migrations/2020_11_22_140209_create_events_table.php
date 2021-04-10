<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('codeEvent')->unique();
            $table->date('dateEvent');
            $table->integer('idUser');
            $table->integer('nbrePackage')->default(0);
            $table->integer('nbrePlace')->default(0);
            $table->string('nomEvent');
            $table->string('urlZip')->nullable();
            $table->string('public')->default("non");
            $table->date('dateFin')->nullable();
            $table->double('lon')->nullable();
            $table->double('lat')->nullable();
            $table->string('adresse')->nullable();
            $table->string('siteWeb')->nullable();
            $table->string('description')->nullable();
            $table->string('ville')->nullable();
            $table->string('cover')->nullable();
            $table->integer('vues')->default(0);
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
        Schema::dropIfExists('events');
    }
}
