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
        Schema::create('order_items', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('product_id')->unsigned(); // مرجع إلى المنتج
            $table->bigInteger('order_id')->unsigned(); // مرجع إلى الطلب
            $table->string('product_name'); // اسم المنتج في الطلب (يمكن تغييره عند الطلب)
            $table->decimal('price', 10, 2); // سعر المنتج
            $table->integer('quantity'); // الكمية المطلوبة
            $table->text('custom_specifications')->nullable(); // مواصفات مخصصة بصيغة JSON
            $table->string('custom_image')->nullable(); // صورة مخصصة للمنتج أو المواصفات
            $table->text('additional_notes')->nullable(); // ملاحظات إضافية
            $table->longText('options')->nullable(); // خيارات إضافية (مثلاً: ألوان أو أحجام)
            $table->boolean('rstatus')->default(false); // حالة الإرجاع
            $table->timestamps();

            // العلاقات
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
