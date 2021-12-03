<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPartCountFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $function = "SET GLOBAL log_bin_trust_function_creators = 1;" .
        "CREATE FUNCTION `enerji`.`part_count`(`part_id` int) RETURNS decimal(16,4)
        BEGIN
            
                set @return_value = 0;
        select sum(stock) INTO @return_value from stock_movements where ISNULL(deleted_at) and replacement_part_id = part_id;
            RETURN  @return_value ;
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
        $query = "DROP FUNCTION IF EXISTS `part_count`;";
        DB::unprepared($query);
    }
}
