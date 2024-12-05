<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddresourceAndFiledsToDataRows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rows = [
            [
                'data_type_id' => 17,
                'field' => 'device_belongsto_field_relationship',
                'type' => 'relationship',
                'display_name' => 'fields',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([
                    'model' => 'App\\Field',
                    'table' => 'fields',
                    'type' => 'belongsTo',
                    'column' => 'field_id',
                    'key' => 'name',
                    'label' => 'id',
                    'pivot_table' => 'companies',
                    'pivot' => '0',
                    'taggable' => '0'
                ]),
                'order' => 17
            ],
            [
                'data_type_id' => 17,
                'field' => 'field_name',
                'type' => 'text',
                'display_name' => 'Field Name',
                'required' => 0,
                'browse' => 0,
                'read' => 0,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'details' => '{}',
                'order' => 17
            ],
            [
                'data_type_id' => 17,
                'field' => 'resource_type',
                'type' => 'select_dropdown',
                'display_name' => 'Resource Type',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([
                    'options' => [
                        'elektrik' => 'Elektrik',
                        'baraj_su' => 'Baraj Su',
                        'sanayi_su' => 'Sanayi Su',
                        'dogalgaz' => 'Doğalgaz',
                        'metraj' => 'Metraj'
                    ]
                ]),
                'order' => 18
            ],
            [
                'data_type_id' => 17,
                'field' => 'field_id',
                'type' => 'text',
                'display_name' => 'Field Id',
                'required' => 0,
                'browse' => 0,
                'read' => 0,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'details' => '{}',
                'order' => 19
            ]
        ];

        foreach ($rows as $row) {
            DB::table('data_rows')->updateOrInsert(
                [
                    'data_type_id' => $row['data_type_id'],
                    'field' => $row['field']
                ],
                $row
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fields = [
            'device_belongsto_field_relationship',
            'field_name',
            'resource_type',
            'field_id'
        ];

        DB::table('data_rows')
            ->where('data_type_id', 17)
            ->whereIn('field', $fields)
            ->delete();
    }
}
