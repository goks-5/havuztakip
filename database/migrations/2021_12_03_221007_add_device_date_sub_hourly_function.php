<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDeviceDateSubHourlyFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $function = "SET GLOBAL log_bin_trust_function_creators = 1;" .
        "CREATE FUNCTION `enerji`.`device_date_sub_hourly`(`device_id` int,`data_id` int,`start_time` datetime,`end_time` datetime) RETURNS decimal(20,2)
        BEGIN
        
        if isnull(end_time) or  end_time < start_time or  end_time = '' then set end_time = now();END IF;
            set @r_value = 0;
            set @s_value = 0 ;
            set @e_value = 0;
            select dd.value INTO @s_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        hourly BETWEEN DATE_ADD(start_time, INTERVAL -5 HOUR) AND DATE_ADD(start_time, INTERVAL 2 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,start_time , created_at)) limit 1;
            
                select dd.value INTO @e_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        hourly BETWEEN DATE_ADD(end_time, INTERVAL -5 HOUR) AND DATE_ADD(end_time, INTERVAL 2 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,end_time , created_at)) limit 1;
                set @r_value = @e_value - @s_value;
            RETURN @r_value;
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
        $query = "DROP FUNCTION IF EXISTS `device_date_sub_hourly`;";
        DB::unprepared($query);
    }
}
