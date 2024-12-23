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
    Schema::create('product_specifications', function (Blueprint $table) {
        $table->id();
        $table->bigInteger('product_id')->unsigned();
        $table->string('description');  // الوصف الخاص بالخاصية
        $table->string('image'); // الصورة الخاصة بالخاصية
        $table->timestamps();

        // تعريف العلاقة بين المنتجات والمواصفات
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
