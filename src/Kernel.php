<?php

namespace App;

use App\Infrastructure\Service\DataInitializer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    
    #[\Override]
    public function boot(): void
    {
        parent::boot();
        
        // Инициализация тестовых данных только в dev-окружении
        if ($this->environment === 'dev' && 
            $this->container !== null && 
            $this->container->has(DataInitializer::class)) {
            
            $initializer = $this->container->get(DataInitializer::class);
            if ($initializer instanceof DataInitializer) {
                $initializer->initialize();
            }
        }
    }
}
