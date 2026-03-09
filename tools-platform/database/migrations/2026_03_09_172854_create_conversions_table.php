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
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->string('group_key');
            $table->string('from_unit_ar');
            $table->string('from_slug_ar');
            $table->string('to_unit_ar');
            $table->string('to_slug_ar');
            $table->decimal('ratio', 20, 10);
            $table->timestamps();

            $table->unique(['from_slug_ar', 'to_slug_ar']);
            $table->index(['group_key', 'from_slug_ar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};
