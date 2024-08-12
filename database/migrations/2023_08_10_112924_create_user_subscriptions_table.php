<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0);
            $table->string('subscription_name');
            $table->string('subscription_type');
            $table->string('subscription_price');
            $table->date('subscription_start_date');
            $table->date('subscription_end_date');
            $table->boolean('subscription_status')->default(1)->comment('0==>In active ,1==>Active');
            $table->string('payment_id')->nullable();
            $table->longText('payment_detail')->nullable();
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
        Schema::dropIfExists('user_subscriptions');
    }
}
