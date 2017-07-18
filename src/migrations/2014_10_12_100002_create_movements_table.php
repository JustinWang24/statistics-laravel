<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovementsTable extends Migration
{
    /**
     * Run the migrations.
     * This is a basic table to store the visitors steps
     * 这个是保存用户在网站内的活动信息
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nf_statistic_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('visitor_id');                  // Visitor's ID
            $table->unsignedInteger('user_id')->default(0);         // 用户在系统中的 ID, 由于一个用户可能有多个账户, 所以放在 movement 里面

            $table->ipAddress('ip');                                // User's IP
            $table->string('language');                             // User's browser language
            $table->string('url');                                  // User's URL
            $table->string('uri');                                  // User's URI
            $table->string('method');                               // User's request method
            $table->string('referrer');                             // The referrer URL

            /**
             * The created year, month, week, day, hour, minute. For better performance when statistic
             * 为了更好的性能, 把创建的时间点分开来
             */
            $table->unsignedInteger('year');
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('week');
            $table->unsignedSmallInteger('day');
            $table->unsignedSmallInteger('hour');
            $table->unsignedSmallInteger('minute');

            $table->float('lat')->nullable();                       // Optional: user's latitude
            $table->float('lng')->nullable();                       // Optional: user's longitude
            $table->text('post_data_in_json')->nullable();          // POST data in json
            $table->text('get_data_in_json')->nullable();           // GET params in json
            $table->string('country')->nullable();                  // Country
            $table->string('state')->nullable();                    // State
            $table->string('city')->nullable();                     // City

            $table->index(['visitor_id','user_id','day','hour'],'newflit_movements');
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
        Schema::dropIfExists('nf_statistic_movements');
    }
}
