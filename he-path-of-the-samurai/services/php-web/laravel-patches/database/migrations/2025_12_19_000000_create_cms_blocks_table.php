<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cms_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['slug', 'is_active']);
        });
        
        // Добавляем тестовые данные
        DB::table('cms_blocks')->insert([
            [
                'slug' => 'dashboard_experiment',
                'title' => 'Демо блок для дашборда',
                'content' => '<div class="alert alert-info"><h5>Блок из БД работает!</h5><p>Это контент из таблицы cms_blocks. Безопасно загружен через контроллер.</p></div>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'welcome',
                'title' => 'Добро пожаловать',
                'content' => '<h3>Демо контент</h3><p>Этот текст безопасно хранится в БД</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_blocks');
    }
};