<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDeviceData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    
        $type = DB::table('data_types')->where("name" , 'devices')->first();

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'offset',
                'type' => 'hidden',
                'display_name' => 'offset',
                'required' => '0',
                'browse' => '0',
                'read' => '0',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
                'details' => '{}',
                'order' => '0',
            )
        );

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'multiplier',
                'type' => 'hidden',
                'display_name' => 'multiplier',
                'required' => '0',
                'browse' => '0',
                'read' => '0',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
                'details' => '{}',
                'order' => '0',
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
        
    }
}
