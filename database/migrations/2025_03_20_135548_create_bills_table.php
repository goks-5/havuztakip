<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            // id (int)
            $table->increments('id'); 
            // project_id (int)
            $table->integer('project_id')->unsigned();
            // fatura_adi (varchar(255))
            $table->string('fatura_adi', 255);
            // fatura_numarasi (int)
            $table->integer('fatura_numarasi');
            // fatura_bedeli (decimal(10,2))
            $table->decimal('fatura_bedeli', 10, 2);
            // tedarikci (varchar(255))
            $table->string('tedarikci', 255);

            // Eğer created_at, updated_at alanları da isterseniz:
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
