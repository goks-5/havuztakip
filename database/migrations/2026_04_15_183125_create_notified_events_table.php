<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifiedEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('notified_events', function (Blueprint $table) {
            // $table->id(); yerine alttakini yaz:
            $table->bigIncrements('id'); 
            
            $table->integer('device_id');
            $table->string('tag_id');
            $table->float('min_value')->default(0);
            $table->float('max_value')->default(100);
            $table->string('email');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notified_events');
    }
}
