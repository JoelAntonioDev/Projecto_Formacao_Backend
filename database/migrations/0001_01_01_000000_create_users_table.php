<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cria a tabela nivel_acesso
        Schema::create('nivel_acesso', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->timestamps();
        });

        DB::table('nivel_acesso')->insert([
            ['nome' => 'mentor', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'mentorado', 'created_at' => now(), 'updated_at' => now()],
        ]);


        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('nivel_acesso_id')->constrained('nivel_acesso'); 
            $table->string('bio')->nullable();
            $table->string('caminho_img')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });


        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('nivel_acesso'); // Deletar a tabela nivel_acesso
    }
};
