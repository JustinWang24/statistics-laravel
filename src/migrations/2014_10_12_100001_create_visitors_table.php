<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     * This is a basic table to store the visitors steps
     * 这个是保存用户浏览器提交的基础信息的数据库表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nf_statistic_visitors', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedSmallInteger('device_type');            // Type: Desktop, mobile, tablet
            $table->string('device_name');                          // User's browser type: desktop, tablet, mobile
            $table->string('user_agent');                           // User's browser name
            $table->string('user_agent_version');                   // User's browser version
            $table->unsignedInteger('screen_width');                // Screen size: width in px
            $table->unsignedInteger('screen_height');               // Screen size: height in px
            $table->string('user_os');                // User computer OS

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
        Schema::dropIfExists('nf_statistic_visitors');
    }
}
