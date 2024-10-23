
# Exemple d'intégration avec Robo

```
    public function anonymize(string $model = null)
    {
        $pathModels = __DIR__ . DIRECTORY_SEPARATOR . 'back/anonymize';

        if ($model) {
            $model = ucfirst($model);
            if (!file_exists($pathModels . DIRECTORY_SEPARATOR . $model . '.php')) {
                throw new \Exception("La classe d'anonymisation est introuvable");
            }
            require_once $pathModels . DIRECTORY_SEPARATOR . $model . '.php';
            $currentModel = new $model();
            $currentModel->run();
            return;
        }

        $models = scandir($pathModels);

        foreach ($models as $model) {
            if (\in_array($model, ['.', '..']) || strpos($model, '.php') === false) {
                continue;
            }
            sleep(5);
            require_once $pathModels . DIRECTORY_SEPARATOR . $model;

            $currentModel = strstr($model, '.php', true);
            $currentModel = new $currentModel();
            echo "Model en cours d'exécution : $model" . PHP_EOL;
            $currentModel->run();
        }
    }
```