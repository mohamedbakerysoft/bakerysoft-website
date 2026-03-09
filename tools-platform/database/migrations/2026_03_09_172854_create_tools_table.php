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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('slug_ar');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->string('tool_type');
            $table->string('meta_title');
            $table->string('meta_description', 320);
            $table->json('schema')->nullable();
            $table->json('settings')->nullable();
            $table->json('content')->nullable();
            $table->json('faq')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->unique(['category_id', 'slug_ar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
