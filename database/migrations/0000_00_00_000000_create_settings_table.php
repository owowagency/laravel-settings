<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('laravel-settings.table_name'), function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('model');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['model_id', 'model_type', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
}
