<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SumToolRow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('tools')->insert(
            array(
                'name' => 'Etiket Toplamları',
                'image' => 'tools/sum_tool.png',
                'title' => '1',
                'unit' => '1',
                'layer' => '1',
                'color' => '1',
                'background' => '1',
                'size' => '1',
                'slug' => 'sum_tag',
                'device' => '2',
                'order' => '10',
                'created_at' => '2024-09-29'
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
