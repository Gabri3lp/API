<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->string('id');
            $table->string('role');
            $table->timestamps();
        });
        
        DB::table('roles')->insert([
            'role' => 'SuperAdmin',
            'id' => '1'
        ]);
        DB::table('roles')->insert([
            'role' => 'Admin',
            'id' => '2'
        ]);
        DB::table('roles')->insert([
            'role' => 'Usuario',
            'id' => '3'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
