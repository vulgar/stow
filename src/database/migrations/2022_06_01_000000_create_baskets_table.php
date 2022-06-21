<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // check for existence of users table, fail if doesn't exist
        if(!Schema::hasTable('users') || !Schema::hasColumn("users","id")){
            throw new Exception("You must have a `users` table with an `id` column.");
        }
        // check for existence of baskets table, fail if exists

        Schema::create('baskets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('instance');
            $table->string('slug');
            $table->boolean('locked')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baskets');
    }
}
