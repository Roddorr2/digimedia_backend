<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;

class FlushCacheCommand extends Command
{
    protected $signature = 'cache:smart-flush {--all}';
    
    protected $description = 'Limpiar caché de forma inteligente (solo datos sensibles por defecto)';

    public function handle()
    {
        if ($this->option('all')) {
            cache()->flush();
            $this->info('✅ Caché completamente limpiado');
            return;
        }

        $this->info('🧹 Limpiando caché de datos sensibles...');
        
        CacheService::clearSensitiveData();
        
        $this->info('✅ Caché de roles y empleados limpiado');
        $this->info('ℹ️  Uso: php artisan cache:smart-flush --all (para limpiar todo)');
    }
}
