<?php

namespace HideMe;

use Faker\Factory;

class Init
{
    protected $faker;
    public string $folder;
    protected bool $debug = false;

    public function __construct()
    {
        $this->setFolder();
        $this->faker = Factory::create('fr_FR');
    }

    private function getModels()
    {
        if (!file_exists($this->folder)) {
            throw new \Exception('Model doesn\'t exist');
        }
        return $this->folder;
    }

    public function setFolder(string $path = null)
    {
        if (!$path) {
            $this->folder = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Models';
        } else {
            $this->folder = $path;
        }

        if (!file_exists($this->folder)) {
            mkdir($this->folder, 0777, true);
        }
        return $this;
    }

}
