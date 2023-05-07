<?php

use App\DataType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class CreateTrigersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('trigers')) {
            Schema::create('trigers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('device_tags');
                $table->string('condition')->default('==');
                $table->string('level')->default('0');
                $table->boolean('last_status')->default(0);
                $table->json('users');
                $table->timestamps();
            });
        }

        $type = DataType::firstOrNew([
            'name' => 'trigers',
            'slug' => 'trigers',
            'display_name_singular' => 'Tetikleyici',
            'display_name_plural' => 'Tetikleyiciler',
            'icon' => 'voyager-bolt',
            'model_name' => 'App\\Triger',
            'controller' => 'App\\Http\\Controllers\\Trigers',
            'generate_permissions' => 1,
            'server_side' => 0,
            'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}'
        ]);

        $type->save();
        
        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id,
                'field' => 'device_tags',
                'type' => 'text',
                'display_name' => 'Cihaz',
                'required' => '1',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
                'details' => '{}',
                'order' => '1',
            )
        );

        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id,
                'field' => 'condition',
                'type' => 'select_dropdown',
                'display_name' => 'Koşul',
                'required' => '1',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
                'details' => '{"options":{"==":"Eşit",">=":"Büyük Eşit",">=":"Küçük Eşit"}}',
                'order' => '2',
            )
        );
        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id,
                'field' => 'level',
                'type' => 'number',
                'display_name' => 'Seviye',
                'required' => '1',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
                'details' => '{}',
                'order' => '3',
            )
        );
        DB::table('data_rows')->insert(
            array(
                'data_type_id' => $type->id,
                'field' => 'users',
                'type' => 'select_dropdown',
                'display_name' => 'Kullanıcılar',
                'required' => '1',
                'browse' => '0',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
                'details' => '',
                'order' => '4',
            )
        );


        $menu = Menu::where('name', 'admin')->firstOrFail();
        $parent = MenuItem::where('icon_class', 'voyager-dashboard')->first();


        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title'   => 'Mesaj Tetikleyici',
            'url'     => '',
            'route'   => 'voyager.trigers.index',
        ]);
        if (!$menuItem->exists) {
            $menuItem->fill([
                'target'     => '_self',
                'icon_class' => 'voyager-boat',
                'color'      => null,
                'parent_id'  => $parent->id,
                'order'      => 8,
            ])->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
