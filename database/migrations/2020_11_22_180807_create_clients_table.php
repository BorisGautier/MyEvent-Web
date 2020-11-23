<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string("codeEvent");
            $table->string("codePass");
            $table->string("nomClient")->nullable();
            $table->string("nomPack")->nullable();
            $table->string("nomVendeur")->nullable();
            $table->string("presence")->default("non");
            $table->integer("telClient")->nullable();
            $table->string("valide")->default("non");
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
        Schema::dropIfExists('clients');
    }
}
