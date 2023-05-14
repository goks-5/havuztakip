<?php

use App\DataType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixTrigersTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type = DataType::where('name','trigers')->first();
        
        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id,
                'field' => 'company_id',
                'type' => 'text',
                'display_name' => 'Company Id',
                'required' => '0',
                'browse' => '0',
                'read' => '0',
                'edit' => '0',
                'add' => '0',
                'delete' => '0',
                'details' => '',
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
    }
}
