<?php namespace Nocio\FormStore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNocioFormstoreSubmissions extends Migration
{
    public function up()
    {
        Schema::create('nocio_formstore_submissions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('status')->default(0);
            $table->dateTime('treated')->nullable();
            $table->integer('submitter_id');
            $table->integer('form_id');
            $table->integer('data_id');
            $table->string('data_type');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('nocio_formstore_submissions');
    }
}