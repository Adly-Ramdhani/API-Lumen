<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('token')->nullable();
            // $table->renameColumn('token', 'api_token')
            //mengganti nama kolum, arugement 1 nama kolom yang lama, arugement 2 nama kolom yang baru 

            // $table->text('token')->change();
            //mengganti tipe data dari ke text lalu di ikuti method change (karena merubah,bukan menambah)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('token');
            // $table->renameColumn('token', 'api_token')
        });
    }
};
