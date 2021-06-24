<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->integer('number')->nullable();
            $table->string('status')->nullable()->default('free')->index();
            $table->integer('seats')->nullable()->index();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('waiter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
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
        Schema::dropIfExists('tables');
    }
}
