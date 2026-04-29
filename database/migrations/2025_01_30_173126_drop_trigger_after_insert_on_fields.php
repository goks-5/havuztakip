<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DropTriggerAfterInsertOnFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Trigger'ı sil
        DB::unprepared('DROP TRIGGER IF EXISTS after_insert_on_fields');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Trigger yeniden oluşturulabilir (isteğe bağlı)
    }
}
