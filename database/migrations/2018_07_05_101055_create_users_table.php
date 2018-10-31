<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name',255);            
            $table->string('last_name',255);
            $table->tinyInteger('name_access')->default(1);

            $table->string('email',255);
            $table->tinyInteger('email_access')->default(0);

            $table->integer('state_id')->nullable($value = true);

            $table->integer('city_id')->nullable($value = true);
            $table->tinyInteger('city_id_access')->default(0);

            $table->string('occupation',255)->default('');
            $table->tinyInteger('occupation_access')->default(0);

            $table->string('resume',255)->default('');

            $table->string('work_website',255)->default('');
            $table->tinyInteger('work_website_access')->default(0);

            $table->string('education',255)->default('');
            $table->tinyInteger('education_access')->default(0);
            
            $table->string('high_school',255)->default('');
            $table->tinyInteger('high_school_access')->default(0);
            
            $table->string('college',255)->default('');
            $table->tinyInteger('college_access')->default(0);

            $table->string('alma_matter',255)->default('');
            $table->tinyInteger('alma_matter_access')->default(0);

            $table->string('likes',255)->comment('likes, hobbies,interests')->default('');
            $table->tinyInteger('likes_access')->default(0);

            $table->integer('military_id')->nullable($value = true);
            $table->tinyInteger('military_id_access')->default(0);

            $table->integer('political_id')->nullable($value = true); //political affiliation
            $table->tinyInteger('political_id_access')->default(0);

            $table->integer('religion_id')->nullable($value = true);
            $table->tinyInteger('religion_id_access')->default(0);

            $table->integer('relationship_id')->nullable($value = true)->comment('relation ship status');
            $table->tinyInteger('relationship_id_access')->default(0);
            
            $table->float('capture_distance')->comment('Distance range for other members profiles capture')->nullable($value = true);
            $table->integer('capture_time_period')->comment('Time for how long a profile needs to be in range, to appear in captured suggestions')->default(0);
            $table->string('capture_filter_ids')->default('')->comment('options which maches you would like to be notified of when someone comes into close range');

            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive');
            $table->string('password');
            $table->rememberToken();

            $table->string('verify_code')->default('')->comment('used for forgot password');
            $table->dateTime('verify_code_created_at')->nullable($value=true)->comment('time at which verify code was saved');
            $table->string('security_code')->default('')->comment('used for set new pass in case of forget password');

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
}
