<?php

use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

class CreateTrigersRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $menuItem = MenuItem::where('route','voyager.trigers.index')->first();
        $menuItem->title = 'Bildirimler';
        $menuItem->save();

        Permission::generateFor('trigers');




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
