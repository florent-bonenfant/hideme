<?php

namespace HideMe;

use Faker\Factory;

class Model extends \Illuminate\Database\Eloquent\Model

{
    public $folder;
    public $chunk = 50;
    public $currentItem = 0;
    public $db;
    public $columns = [];

    protected $faker;
    protected $countElements = 0;
    protected $ignoreFields = [];
    protected $debug = false;

    private $dbColumns = [];

    public function __construct()
    {
        $this->setFolder();
        $this->faker = Factory::create('fr_FR');
        $this->db = new Database();
        $this->dbColumns = $this->getColumns();
        $this->checkColumnsExist();
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

    public function count()
    {
        $this->countElements = $this->db->table($this->table)->count();
        return $this;
    }

    public function run()
    {
        if (empty($this->columns)) {
            return true;
        }

        $this->orderBy($this->array_key_first($this->columns), 'DESC')->chunk($this->chunk, function ($items) {
            sleep(5);
            foreach ($items as $item) {
                $this->anonymizeModel($item);
            }
        });

    }

    private function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }

    /**
     * Anonymisation de l'élément
     *
     * @param     Model    $item    [$item description]
     *
     * @return    bool            [return description]
     */
    private function anonymizeModel($item): bool
    {
        try {
            foreach ($this->columns as $column => $fake) {
                if (is_string($fake)) {
                    try {
                        $item->$column = $this->faker->$fake();
                    } catch (\Exception $e) {
                        $item->$column = $fake;
                    }
                }
                if (is_callable($fake)) {
                    $item->$column = $fake();
                }
                if (is_int($fake)) {
                    $item->$column = $fake;
                }
            }
            return $item->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @todo on peut envisager une vérification de la taille du champ
     *
     * @param     [type]    $string    [$string description]
     *
     * @return    [type]               [return description]
     */
    private function canWriteData($string): bool
    {
        return is_string($string);
    }

    private function getColumns(): array {
        return $this->db::select("DESCRIBE " . $this->table);
    }

    /**
     * Supression des colonnes inconnues
     *
     * @todo Vérification du type de faker avec le type de colonne sql
     * @fixme voir pour externaliser le traitement, est-ce possible ? il serait dommange de mettre en place un système de cache
     * @return    array           [return description]
     */
    private function checkColumnsExist(): array
    {
        foreach ($this->columns as $modelColumn => $type) {
            $exist = false;
            foreach ($this->dbColumns as $dbColumn) {
                if ($dbColumn->Field === $modelColumn) {
                    $exist = true;
                    continue;
                }
            }
            if (array_key_exists($this->table, $this->ignoreFields) && in_array($modelColumn, $this->ignoreFields[$this->table])) {
                continue;
            }
            if (!$exist) {
                unset($this->columns[$modelColumn]);
                if (!array_key_exists($this->table, $this->ignoreFields) || !in_array($modelColumn, $this->ignoreFields[$this->table])) {
                    $this->ignoreFields[$this->table][] = $modelColumn;
                }
                // throw new \Exception("La colonne $modelColumn de la table $this->table n'existe pas.");
                // echo "La colonne $modelColumn de la table $this->table n'existe pas, elle sera ignorée.";
            }
        }

        return $this->ignoreFields;
    }

    public function getFaker()
    {
        return $this->faker;
    }
}
