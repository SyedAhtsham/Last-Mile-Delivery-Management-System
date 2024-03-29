<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivered', function (Blueprint $table) {
            $table->comment('');
            $table->unsignedBigInteger('cons_id')->index('delivered_cons_id_foreign');
            $table->string('receiverName', 55);
            $table->string('receiverContact', 14);
            $table->timestamp('deliveryDate')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivered');
    }
};
