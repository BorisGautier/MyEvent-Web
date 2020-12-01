<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendeursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendeurs', function (Blueprint $table) {
            $table->id();
            $table->string("codeEvent");
            $table->integer("nbreVente")->default(0);
            $table->string("codeVendeur");
            $table->string("nomVendeur");
            $table->integer("phone");
            $table->string("fcmToken")->nullable();
            $table->string("platform")->nullable();
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
        Schema::dropIfExists('vendeurs');
    }
}
