<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use ESIS\Status;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->enum('method', ['get', 'post', 'put', 'delete']);
            $table->string('route');
            $table->string('endpoint');
            $table->enum('status', ['green', 'yellow', 'red']);
            $table->json('tags');
            $table->timestamps();

            $table->primary(['method', 'route']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
