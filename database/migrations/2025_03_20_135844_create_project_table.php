<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTable extends Migration
{
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            // id (int)
            $table->increments('id');

            // offer_id (int unsigned)
            $table->unsignedInteger('offer_id');

            // kabul_bedeli (decimal(10,2))
            $table->decimal('kabul_bedeli', 10, 2);

            // caliscak_kisi_sayisi (int)
            $table->integer('caliscak_kisi_sayisi');

            // proje_bitis_tarihi (datetime)
            $table->dateTime('proje_bitis_tarihi');

            // Eğer created_at, updated_at alanları isterseniz:
            // $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project');
    }
}
