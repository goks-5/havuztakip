<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer', function (Blueprint $table) {
            $table->increments('id'); // Otomatik artan birincil anahtar
            $table->string('offer_no', 255); // Teklif numarası
            $table->string('demand_no', 255); // Talep numarası
            $table->string('title', 255); // Başlık
            $table->date('delivery_date'); // Teslim tarihi
            $table->string('company', 255); // Şirket
            $table->string('person_name', 255); // Kişi adı
            $table->string('person_email', 255); // Kişi e-posta adresi
            $table->string('currency', 10); // Para birimi
            $table->text('explanation')->nullable(); // Açıklama (opsiyonel)
            $table->text('piece')->nullable(); // Parça bilgisi (opsiyonel)
            $table->text('unit_price')->nullable(); // Birim fiyat (opsiyonel)
            $table->text('total_price')->nullable(); // Toplam fiyat (opsiyonel)
            $table->decimal('total', 10, 2); // Toplam tutar
            $table->tinyInteger('is_editable')->default(1); // Düzenlenebilirlik
            $table->timestamp('created_at')->useCurrent(); // Oluşturulma zamanı
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer');
    }
}
