<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {

            $table->id();            
            $table->bigInteger('user_id')->unsigned();           
            $table->string('name');          
            $table->string('phone')->nullable();           
            $table->string('locality')->nullable();        
            $table->text('address')->nullable();         
            $table->string('city')->nullable();         
            $table->string('state');        
            $table->string('country');       
            $table->string('landmark')->nullable();       
            $table->string('zip')->nullable();        
            $table->string('type')->default('home')->nullable();    
            $table->boolean('isdefault')->default(false)->nullable();     
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');    
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
