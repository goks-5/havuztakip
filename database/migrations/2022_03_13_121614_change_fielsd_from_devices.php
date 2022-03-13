<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeFielsdFromDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('product');
            $table->dropColumn('hardware');
            $table->dropColumn('software');
        });
        $type = DB::table('data_types')->where("name" , 'devices')->first();
       
        DB::table('data_rows')->where('data_type_id',$type->id )->where('field','product')->delete();
        DB::table('data_rows')->where('data_type_id',$type->id )->where('field','hardware')->delete();
        DB::table('data_rows')->where('data_type_id',$type->id )->where('field','software')->delete();

        Schema::table('devices', function (Blueprint $table) {
            $table->text('product_detail')->nullable()->after('name');
            $table->text('field_detail')->nullable()->after('name');
        });


        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'product_detail',
                'type' => 'multiple_text',
                'display_name' => 'Ürün Detayı',
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
                'field' => 'field_detail',
                'type' => 'multiple_text',
                'display_name' => 'Saha Detayı',
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


        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('field_detail');
            $table->dropColumn('product_detail');
        });
        $type = DB::table('data_types')->where("name" , 'devices')->first();
       
        DB::table('data_rows')->where('data_type_id',$type->id )->where('field_detail','product')->delete();
        DB::table('data_rows')->where('data_type_id',$type->id )->where('product_detail','hardware')->delete();
    }
}
