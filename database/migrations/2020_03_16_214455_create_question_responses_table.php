<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'question_responses', function ($table) {
                $table->increments('id');
                $table->text('response');
                $table->enum('type', ['voice', 'sms']);
                $table->string('session_sid');
                $table->string('recording_sid');
                $table->string("country");
                $table->string('storage_status')->default("not_processed");
                $table->string('transcribe_status')->default("not_processed");
                $table->string('phone_no');
                $table->integer('question_id')->unsigned(); 
                $table->timestamps();
                $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');


            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_responses');
    }
}
