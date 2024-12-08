<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateResourceTypeWithPermissionsInDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('enerji.data_rows')
            ->where('data_type_id', 17)
            ->where('field', 'resource_type')
            ->update([
                'type' => 'hidden', // Yeni tür
                'details' => json_encode([ ]),
                'display_name' => 'Energy Type', // Yeni isim
                'required' => 1, // Yeni gereklilik
                'browse' => 0, // Yeni tarama ayarı
                'read' => 0, // Yeni okuma izni
                'edit' => 1, // Yeni düzenleme izni
                'add' => 1, // Yeni ekleme izni
                'delete' => 1, // Yeni silme izni
                'order' => 20 // Yeni sıralama
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('enerji.data_rows')
            ->where('data_type_id', 17)
            ->where('field', 'resource_type')
            ->update([
                'type' => 'select_dropdown', // Eski tür
                'details' => json_encode([
                    'options' => [
                        'elektrik' => 'Elektrik',
                        'baraj_su' => 'Baraj Su',
                        'sanayi_su' => 'Sanayi Su',
                        'dogalgaz' => 'Doğalgaz',
                        'metraj' => 'Metraj'
                    ]
                ]),
                'display_name' => 'Resource Type', // Eski isim
                'required' => 0, // Eski gereklilik
                'browse' => 1, // Eski tarama ayarı
                'read' => 1, // Eski okuma izni
                'edit' => 1, // Eski düzenleme izni
                'add' => 1, // Eski ekleme izni
                'delete' => 1, // Eski silme izni
                'order' => 18 // Eski sıralama
            ]);
    }
}
