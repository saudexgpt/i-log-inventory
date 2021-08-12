<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->double('amount_due', 10, 2);
            $table->enum('type', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', ['active', 'non-active'])->default('non-active');
            $table->timestamps();
        });
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->double('amount_due', 10, 2);
            $table->double('paid', 10, 2)->default(0);
            $table->string('transaction')->nullable();
            $table->string('message')->default('pending');
            $table->string('status')->default('pending');
            $table->date('due_date');
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
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_payments');
    }
}
