<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FaultsTableTool extends Migration
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
                'name' => 'Son Arızalar',
                'image' => 'tools/July2020/VBNqm3C3fAOyVdEniVLF.png',
                'title' => '0',
                'unit' => '0',
                'layer' => '1',
                'color' => '1',
                'background' => '1',
                'size' => '1',
                'slug' => 'faults_table',
                'order' => '11',
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
