<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsToAfterDeleteDevicesTrigger extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER after_delete_on_fields
            AFTER DELETE ON fields
            FOR EACH ROW
            BEGIN
                DELETE FROM devices
                WHERE name = OLD.name;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_delete_on_fields');
    }
}
