<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStockLimitsToReplacementParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replacement_parts', function (Blueprint $table) {
            $table->integer('lower_limit')->default(0)->after('unit');
            $table->integer('upper_limit')->default(1000)->after('unit');
        });

        $type = DB::table('data_types')->where("name" , 'replacement_parts')->first();

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'lower_limit',
                'type' => 'number',
                'display_name' => 'Alt Limit',
                'required' => '0',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
                'details' => '{"default":"0"}',
                'order' => '8',
            )
        );
        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'upper_limit',
                'type' => 'number',
                'display_name' => 'Üst Limit',
                'required' => '0',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
                'details' => '{"default":"1000"}',
                'order' => '8',
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
        Schema::table('replacement_parts', function (Blueprint $table) {
            $table->dropColumn('lower_limit');
            $table->dropColumn('upper_limit');
        });
    }
}
