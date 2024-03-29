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
        Schema::table('returned', function (Blueprint $table) {
            $table->foreign(['cons_id'])->references(['cons_id'])->on('consignment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returned', function (Blueprint $table) {
            $table->dropForeign('returned_cons_id_foreign');
        });
    }
};
