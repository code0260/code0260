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
        Schema::create('product_order_specifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->json('paragraphs')->nullable(); // مصفوفة تعبر عن الجمل الموصوفة
            $table->json('images')->nullable(); // مصفوفة الصور الخاصة بالخاصية
            $table->bigInteger('order_item_id')->unsigned(); // ربط المواصفات مع عنصر الطلب
            $table->bigInteger('product_id')->unsigned(); // إضافة product_id لربط المنتج
            $table->timestamps();

            // تعريف العلاقة بين عنصر الطلب والمواصفات
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); // ربط المنتج
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_order_specifications');
    }
};
