<?php

declare(strict_types=1);

namespace brooke\scout;

use think\App;
use think\Request;
use Brooke\Supports\ServiceProvider;
use InvalidArgumentException;
use brooke\scout\EngineManager;
use TeamTNT\TNTSearch\Support\TokenizerInterface;
use TeamTNT\TNTSearch\TNTSearch;
use brooke\scout\engines\TNTSearchEngine;
use think\facade\Config;
use think\Db;

class TNTSearchScoutServiceProvider extends ServiceProvider
{
    public static function register(App $app, Request $request)
    {
        $provider = static::getInstance();

        include __DIR__ . '/helper.php';

        $provider->loadConfig(__DIR__.'/config/tntsearch.php', 'tntsearch');

        $app->bindTo(TokenizerInterface::class, $provider->getConfig()['tokenizer']);

        $app->make(EngineManager::class)->extend('tntsearch', function () use ($provider) {
            $tnt = new TNTSearch;
            $tnt->loadConfig($provider->getConfig());
            $tnt->setDatabaseHandle(Db::connect()->getConnection()->getPdo() ?: Db::connect()->getConnection()->connect());
            return new TNTSearchEngine($tnt);
        });

        Config::set('scout.driver', 'tntsearch');
    }

    protected function getConfig()
    {
        $database_config = config("database.");
        $database_config['driver'] = $database_config['type'];
        $database_config['host'] = $database_config['hostname'];

        $config = config('tntsearch.') + \Arr::only($database_config, ['driver', 'host', 'username', 'password']);

        if (! array_key_exists($config['default'], $config['tokenizers'])) {
            throw new InvalidArgumentException("Tokenizer [{$config['default']}] is not defined.");
        }
        
        return array_merge($config, ['tokenizer' => $config['tokenizers'][$config['default']]['driver']]);
    }
}
