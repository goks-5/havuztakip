<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDeviceDateData12hFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $function = "SET GLOBAL log_bin_trust_function_creators = 1;" .
        "CREATE FUNCTION `enerji`.`device_date_data_12h`(`device_id` int,`data_id` int,`qdate` datetime) RETURNS float
        BEGIN
            set @date_value = null;
            
        select dd.value INTO @date_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(qdate, INTERVAL -24 hour) AND DATE_ADD(qdate, INTERVAL 24 hour)
        
        order by ABS(TIMESTAMPDIFF(SECOND,qdate , created_at)) limit 1;
        
            RETURN @date_value;
        END";
        DB::unprepared($function);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $query = "DROP FUNCTION IF EXISTS `device_date_data_12h`;";
        DB::unprepared($query);
    }
}
