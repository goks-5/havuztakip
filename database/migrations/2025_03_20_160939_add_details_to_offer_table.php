<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToOfferTable extends Migration
{
    public function up()
    {
        Schema::table('offer', function (Blueprint $table) {
            $table->text('details')->nullable();
        });
    }

    public function down()
    {
        Schema::table('offer', function (Blueprint $table) {
            $table->dropColumn('details');
        });
    }
}
