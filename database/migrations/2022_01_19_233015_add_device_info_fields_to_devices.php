<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDeviceInfoFieldsToDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('product',10)->nullable()->after('name');
            $table->string('hardware',10)->nullable()->after('name');
            $table->string('software',10)->nullable()->after('name');
        });

        $type = DB::table('data_types')->where("name" , 'devices')->first();

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'product',
                'type' => 'text',
                'display_name' => 'Ürün Kodu',
                'required' => '0',
                'browse' => '1',
                'read' => '1',
                'edit' => '0',
                'add' => '0',
                'delete' => '0',
                'details' => '{}',
                'order' => '3',
            )
        );

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'hardware',
                'type' => 'text',
                'display_name' => 'Donanım Versiyon',
                'required' => '0',
                'browse' => '1',
                'read' => '1',
                'edit' => '0',
                'add' => '0',
                'delete' => '0',
                'details' => '{}',
                'order' => '4',
            )
        );

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'software',
                'type' => 'text',
                'display_name' => 'Yazılım Versiyon',
                'required' => '0',
                'browse' => '1',
                'read' => '1',
                'edit' => '0',
                'add' => '0',
                'delete' => '0',
                'details' => '{}',
                'order' => '4',
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
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('product');
            $table->dropColumn('hardware');
            $table->dropColumn('software');
        });
    }
}
