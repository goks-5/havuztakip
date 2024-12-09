<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsToDevicesTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Trigger oluşturma
        DB::unprepared('
            CREATE TRIGGER after_insert_on_fields
            AFTER INSERT ON fields
            FOR EACH ROW
            BEGIN
                INSERT INTO devices (mac, device_id, company_id, name, created_at, tags)
                VALUES (
                    "00:00:00:00:00:04",
                    CONCAT("FIELD_", DATE_FORMAT(NOW(), "%Y%m%d%H%i%s")), -- FIELD_ + tarih
                    NEW.company_id,
                    NEW.name,
                    NOW(),
                    CONCAT(
                        \'{"0":"\', NEW.name, \' ",\',
                        \'"100":"\', NEW.name, \' Saatlik",\',
                        \'"200":"\', NEW.name, \' Günlük",\',
                        \'"300":"\', NEW.name, \' Haftalık",\',
                        \'"400":"\', NEW.name, \' Aylık",\',
                        \'"500":"\', NEW.name, \' Yıllık"}\'
                    )
                );
            END
        ');
    }

    /**
     * 
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Trigger silme
        DB::unprepared('DROP TRIGGER IF EXISTS after_insert_on_fields');
    }
}

