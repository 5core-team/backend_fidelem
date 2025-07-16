<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundingRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('funding_requests', function (Blueprint $table) {
            $table->id();
            $table->string('companyName');
            $table->string('email');
            $table->string('phone');
            $table->text('mission');
            $table->text('vision');
            $table->string('sector');
            $table->text('productDescription');
            $table->string('productStatus');
            $table->decimal('amountRequested', 10, 2);
            $table->text('useOfFunds');
            $table->string('businessPlan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('funding_requests');
    }
}
