<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMessageToFoodOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('food_order', function (Blueprint $table) {
        $table->text('message')->nullable()->after('quantity');
    });
}

public function down()
{
    Schema::table('food_order', function (Blueprint $table) {
        $table->dropColumn('message');
    });
}

}
