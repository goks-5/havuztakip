<?php

use App\DataType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\DataRow;

class FixTrigersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trigers', function (Blueprint $table) {
            $table->text('mail_body')->afrer('users');
        });

        $type = DataType::where('name','trigers')->first();
        $row = DataRow::where('data_type_id',$type->id)->where('field','condition')->first();

        $row->details = '{"options":{"==":"Eşit",">=":"Büyük Eşit","<=":"Küçük Eşit"}}';
        $row->save();

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id,
                'field' => 'mail_body',
                'type' => 'rich_text_box',
                'display_name' => 'Mail İçeriği',
                'required' => '1',
                'browse' => '0',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
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
