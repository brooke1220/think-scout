<?php
declare(strict_types=1);

namespace brooke\scout;

use think\App;
use think\Request;
use Brooke\Supports\ServiceProvider;

class ScoutServiceProvider extends ServiceProvider
{
    public static function register(App $app, Request $request)
    {
        $provider = static::getInstance();

        $provider->loadConfig(__DIR__.'/config/scout.php', 'scout');

        $app->bindTo(EngineManager::class, function () use ($app){
            return new EngineManager($app);
        });

        $provider->addCommands([
            'scout:import' => commands\ImportCommand::class,
            'scout:flush' => commands\FlushCommand::class
        ]);
    }
}
