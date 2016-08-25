<?php
/**
 * Created by PhpStorm.
 * User: kz
 * Date: 8/19/16
 * Time: 4:04 PM
 */

namespace Mongopper;


use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MongoUserProvider;

class MongopperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('mongodb.php')
        ], 'mongo');

        \Auth::provider('mongo', function (Application $app, array $config) {
            return new MongoUserProvider($app[DocumentManager::class], $app['hash'], $config['model']);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DocumentManager::class, function (Application $app) {

            $config = config('mongodb');
            $server = "mongodb://" . $config['host'] . ":" . $config['port'];
            $options = array_only($config, ['username', 'password']);

            $connection = new Connection($server, $options);
            $configuration = new Configuration();

            $configuration->setProxyDir(storage_path('cache/MongoDbProxies'));
            $configuration->setProxyNamespace('MongoDbProxy');
            $configuration->setHydratorDir(storage_path('cache/MongoDbHydrators'));
            $configuration->setHydratorNamespace('MongoDbHydrator');
            $configuration->setDefaultDB(config('mongodb.database', 'laravel'));
            // Request whatever mapping driver is bound to the interface.
            $configuration->setMetadataDriverImpl(AnnotationDriver::create(app_path(config('mongodb.documentsPath'))));

            return DocumentManager::create($connection, $configuration);
        });
    }

}