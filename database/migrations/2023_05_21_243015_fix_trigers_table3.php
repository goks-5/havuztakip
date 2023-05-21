<?php

use App\DataType;
use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\DataRow;

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
        $type->display_name_singular = 'Bildirim';
        $type->display_name_plural = 'Bildirimler';
        $type->save();
        
        $row = DataRow::where('data_type_id',$type->id)->where('field','condition')->first();

        $row->details = json_decode('{"options":{"==":"Eşit",">=":"Büyük Eşit","<=":"Küçük Eşit","!=":"Değişim"}}');
        $row->save();

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
