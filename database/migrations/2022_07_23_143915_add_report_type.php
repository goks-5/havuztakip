<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;

class AddReportType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $type = DataType::where('model_name' ,'App\Report')->first();
       $row = DataRow::where('data_type_id',$type->id)->where('field','type')->first();
       $row->details = '{"options":{"1":"Veriler Sutunlarda","2":"Veriler Satırlarda","3":"Veriler Farklarla Sutunlarda ","4":"Veriler Farklarla Satırlarda"}}';
       $row->save();        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $type = DataType::where('model_name' ,'App\Report')->first();
        $row = DataRow::where('data_type_id',$type->id)->where('field','type')->first();
        $row->details = '{"options":{"1":"Veriler Sutunlarda","2":"Veriler Sat\u0131rlarda"}}';
        $row->save();
    }
}
