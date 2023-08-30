<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class addMailToReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->timestamp('report_send_date')->nullable();
        });
        $type = DB::table('data_types')->where("name" , 'reports')->first();

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id ,
                'field' => 'report_send_date',
                'type' => 'text',
                'display_name' => 'Son Mail Gönderim',
                'required' => '0',
                'browse' => '0',
                'read' => '1',
                'edit' => '0',
                'add' => '0',
                'delete' => '0',
                'details' => '{}',
                'order' => '3',
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
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('report_send_date');
        });
    }
}
