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
        Schema::create('attendance_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreignId('leave_master_id');
            $table->string('number', 50);
            $table->date('date');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('balance');
            $table->integer('amount');
            $table->integer('remaining');
            $table->text('description')->nullable();
            $table->text('filename')->nullable();
            $table->string('approved_by', 20)->nullable();
            $table->char('approved_status', 1)->nullable();
            $table->date('approved_date')->nullable();
            $table->text('approved_note')->nullable();
            $table->string('created_by', 20);
            $table->string('updated_by', 20)->nullable();
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
        Schema::dropIfExists('attendance_leaves');
    }
};
