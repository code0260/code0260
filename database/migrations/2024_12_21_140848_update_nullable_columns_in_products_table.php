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
        Schema::table('products', function (Blueprint $table) {
            // جعل الحقول قابلة لأن تكون فارغة (nullable)
            $table->text('description')->nullable()->change();
            $table->decimal('regular_price')->nullable()->change();
            $table->string('SKU')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // التراجع عن التغيير في حالة العودة عن الهجرة
            $table->text('description')->nullable(false)->change();
            $table->decimal('regular_price')->nullable(false)->change();
            $table->string('SKU')->nullable(false)->change();
        });
    }
    
};
