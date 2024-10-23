<?php

namespace HideMe;

use Illuminate\Database\Capsule\Manager as Capsule;
use HideMe\Config;

class Database
{
    public $eloquent;

    public function __construct($configDb = [])
    {
        // @todo automatiser la recherche d'information de connexion robo / laravel
        $config = new Config($configDb);
        $this->setOrm($config);
    }

    private function setOrm($configDb)
    {
        $this->eloquent = new Capsule;

        $this->eloquent->addConnection([
            'driver' => $configDb->getAdapter(),
            'host' => $configDb->getHost(),
            'port' => $configDb->getPort(),
            'database' => $configDb->getName(),
            'username' => $configDb->getUser(),
            'password' => $configDb->getPwd(),
            'charset' => $configDb->getCharset(),
            'collation' => $configDb->getCollation(),
            'prefix' => '',
        ]);

        // Make this Capsule instance available globally via static methods
        $this->eloquent->setAsGlobal();

        // Setup the Eloquent ORM
        $this->eloquent->bootEloquent();
    }

    public function __destruct()
    {
        $this->eloquent->getConnection()->disconnect();
    }

    // Méthode magique pour intercepter les appels de méthodes
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->eloquent->getConnection(), $method], $arguments);
    }

    // Méthode magique pour intercepter les appels de méthodes statiques
    public static function __callStatic($method, $arguments)
    {
        $instance = new self();
        return call_user_func_array([$instance->eloquent->getConnection(), $method], $arguments);
    }

    public function get()
    {
        return $this;
    }

}
