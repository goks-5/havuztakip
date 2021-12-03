<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDeviceDateSubFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $function = "CREATE FUNCTION `enerji`.`device_date_sub`(`device_id` int,`data_id` int,`start_time` datetime,`end_time` datetime) RETURNS decimal(20,2)
        BEGIN
        
        if isnull(end_time) or  end_time < start_time or  end_time = '' then set end_time = now();END IF;
            set @r_value = 0;
            set @s_value = 0 ;
            set @e_value = 0;
            select dd.value INTO @s_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(start_time, INTERVAL -55 MINUTE) AND DATE_ADD(start_time, INTERVAL 55 MINUTE)
        order by ABS(TIMESTAMPDIFF(SECOND,start_time , created_at)) limit 1;
        
        if @s_value = 0 then
            select dd.value INTO @s_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(start_time, INTERVAL -6 HOUR) AND DATE_ADD(start_time, INTERVAL 6 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,start_time , created_at)) limit 1;
        
        end if;
        
        if @s_value = 0 then
            select dd.value INTO @s_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(start_time, INTERVAL -12 HOUR) AND DATE_ADD(start_time, INTERVAL 12 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,start_time , created_at)) limit 1;
        
        end if;
        
        if @s_value = 0 then
            select dd.value INTO @s_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(start_time, INTERVAL -24 HOUR) AND DATE_ADD(start_time, INTERVAL 24 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,start_time , created_at)) limit 1;
        
        end if;
        
        if @s_value = 0 then
            select dd.value INTO @s_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(start_time, INTERVAL -48 HOUR) AND DATE_ADD(start_time, INTERVAL 48 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,start_time , created_at)) limit 1;
        
        end if;
        if @s_value = 0 then
            select dd.value INTO @s_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(start_time, INTERVAL -96 HOUR) AND DATE_ADD(start_time, INTERVAL 96 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,start_time , created_at)) limit 1;
        
        end if;
        
        
            
                select dd.value INTO @e_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(end_time, INTERVAL -55 MINUTE) AND DATE_ADD(end_time, INTERVAL 55 MINUTE)
        order by ABS(TIMESTAMPDIFF(SECOND,end_time , created_at)) limit 1;
        
        if @e_value = 0 then
        
                select dd.value INTO @e_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(end_time, INTERVAL -6 HOUR) AND DATE_ADD(end_time, INTERVAL 6 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,end_time , created_at)) limit 1;
        
        end if;
        
        if @e_value = 0 then
        
                select dd.value INTO @e_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(end_time, INTERVAL -12 HOUR) AND DATE_ADD(end_time, INTERVAL 12 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,end_time , created_at)) limit 1;
        
        end if;
        
        if @e_value = 0 then
        
                select dd.value INTO @e_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(end_time, INTERVAL -24 HOUR) AND DATE_ADD(end_time, INTERVAL 24 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,end_time , created_at)) limit 1;
        
        end if;
        
        if @e_value = 0 then
        
                select dd.value INTO @e_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(end_time, INTERVAL -48 HOUR) AND DATE_ADD(end_time, INTERVAL 48 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,end_time , created_at)) limit 1;
        
        end if;
        
        if @e_value = 0 then
        
                select dd.value INTO @e_value from device_datas dd
        where 
        dd.device_id = device_id and dd.data_id = data_id  and
        created_at BETWEEN DATE_ADD(end_time, INTERVAL -96 HOUR) AND DATE_ADD(end_time, INTERVAL 96 HOUR)
        order by ABS(TIMESTAMPDIFF(SECOND,end_time , created_at)) limit 1;
        
        end if;
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
        $query = "DROP FUNCTION IF EXISTS `device_date_sub`;";
        DB::unprepared($query);
    }
}
