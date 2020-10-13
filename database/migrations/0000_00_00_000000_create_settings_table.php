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
            // TODO remove group and create in a different issue.
            $table->string('group')->nullable();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['model', 'group', 'key']);
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
