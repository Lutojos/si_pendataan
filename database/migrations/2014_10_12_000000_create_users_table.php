<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('phone_number', 15)->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedTinyInteger('gender')->nullable()->comment('1:Laki-laki,2:Perempuan');
            $table->date('birthofdate')->nullable();
            $table->string('birthofplace', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('image_path')->nullable()->comment('foto ktp');
            $table->string('room_number', 8)->nullable();
            $table->date('entry_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
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
        Schema::dropIfExists('users');
    }
};
