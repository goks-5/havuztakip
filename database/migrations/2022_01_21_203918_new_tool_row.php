<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewToolRow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('data_rows')->insert(
            array(
                'name' => 'Son Veri Zamanı',
                'image' => 'tools/June2020/Al5WBbGyaja5LhBZ8kRz.png',
                'title' => '1',
                'unit' => '0',
                'layer' => '1',
                'color' => '1',
                'background' => '1',
                'size' => '1',
                'slug' => 'last_date',
                'device' => '3',
                'order' => '9',
                'created_at' => '2022-01-21'
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
