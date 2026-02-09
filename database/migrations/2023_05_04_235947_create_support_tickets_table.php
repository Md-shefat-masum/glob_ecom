<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no');
            $table->unsignedBigInteger('support_taken_by')->comment('user_id');
            $table->string('subject');
            $table->longText('message')->nullable();
            $table->string('attachment')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=>Pending;1=>In Progress;2=>Solved;3=>Rejected;4=>On Hold');
            $table->string('slug');
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
        Schema::dropIfExists('support_tickets');
    }
}
