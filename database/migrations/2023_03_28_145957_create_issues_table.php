<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['land disputes', 'family disputes', 'labor disputes', 'disputes with the traffic police']);
            $table->string('image_path');
            $table->text('content');
            $table->enum('status', ['new', 'in progress', 'completed']);
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('lawyer_id')->nullable()->constrained('users');
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
        Schema::dropIfExists('issues');
    }
}
