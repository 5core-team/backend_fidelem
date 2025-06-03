<?php

// database/migrations/YYYY_MM_DD_create_credit_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('credit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->integer('duration');
            $table->string('purpose');
            $table->text('additional_details')->nullable();
            $table->string('status')->default('En attente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_requests');
    }
}
