<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->boolean('isadmin')->default(false);
            $table->string('name')->nullable();
            $table->string('phone_no', 15)->nullable();
            $table->string('gender', 1)->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode', 20)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->boolean('issocial')->default(false);
            $table->string('provider', 20)->nullable();
            $table->string('image')->nullable();
            $table->boolean('isemailverified')->default(false);
            $table->boolean('isnumberverified')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
