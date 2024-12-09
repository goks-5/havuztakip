<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsToAfterUpdateDevicesTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Güncelleme sonrası trigger oluşturma
        DB::unprepared('
            CREATE TRIGGER after_update_on_fields
            AFTER UPDATE ON fields
            FOR EACH ROW
            BEGIN
                IF NEW.name <> OLD.name THEN
                    UPDATE devices
                    SET 
                        name = NEW.name,
                        tags = CONCAT(
                            \'{"0":"\', NEW.name, \' ",\',
                            \'"100":"\', NEW.name, \' Saatlik",\',
                            \'"200":"\', NEW.name, \' Günlük",\',
                            \'"300":"\', NEW.name, \' Haftalık",\',
                            \'"400":"\', NEW.name, \' Aylık",\',
                            \'"500":"\', NEW.name, \' Yıllık"}\'
                        )
                    WHERE name = OLD.name;
                END IF;
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
        // Trigger silme
        DB::unprepared('DROP TRIGGER IF EXISTS after_update_on_fields');
    }
}
