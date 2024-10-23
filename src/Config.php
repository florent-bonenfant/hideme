<?php

namespace HideMe;

class Config
{
    private $basePath;
    public $folder;
    protected $host = 'localhost';
    protected $port = '3306';
    protected $user = 'user';
    protected $pwd = 'password';
    protected $name = 'test';
    protected $adapter = 'mysql';
    protected $charset = 'utf8';
    protected $collation = 'utf8_unicode_ci';

    /**
     * Get the value of folder
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * Set the value of folder
     */
    public function setFolder(string $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * [__construct description]
     *
     * @param     [array]    $params    [host, port, user, pwd, name, adapter, charset, collation]
     *
     * @return    []                   [return description]
     */
    public function __construct($params = [])
    {
        $this->basePath = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR;

        if (is_array($params) && empty($params)) {
            $this->searchConfig();
        } else {
            $this->setHost($params['host']);
            $this->setPort($params['port']);
            $this->setUser($params['user']);
            $this->setPwd($params['pwd']);
            $this->setName($params['name']);
            $this->setAdapter($params['adapter']);
            $this->setCharset($params['charset']);
            $this->setCollation($params['collation']);
        }
    }

    private function searchConfig()
    {
        $folders = scandir($this->getBasePath());
        if (in_array('back', $folders)) {
            $this->basePath .= DIRECTORY_SEPARATOR . 'back' . DIRECTORY_SEPARATOR;
            $folders = scandir($this->getBasePath());
        }
        if (in_array('server', $folders)) {
            $this->basePath .= DIRECTORY_SEPARATOR . 'server' . DIRECTORY_SEPARATOR;
            $folders = scandir($this->getBasePath());
        }

        require_once $this->basePath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        if ($this->getPhinx($folders)) {
            return true;
        }
        if ($this->getEnv($folders)) {
            return true;
        }
        return false;
    }

    private function getPhinx($folders)
    {
        if (!in_array('phinx.php', $folders)) {
            return false;
        }

        $config = file_get_contents($this->getBasePath() . 'phinx.php');
        $config = substr($config, strpos($config, 'default_database'));
        $pattern = '/(array\s*\(|\[\s*)(.*?(?:(?1).*?)?)(\s*\)\s*|\s*\]\s*)/s';
        preg_match($pattern, $config, $matches);
        $config = eval("return " . trim($matches[0]) . ";");

        $this->setHost($config['host']);
        $this->setPort($config['port']);
        $this->setUser($config['user']);
        $this->setPwd($config['pass']);
        $this->setName($config['name']);
        $this->setAdapter($config['adapter']);
        $this->setCharset($config['charset']);
        return true;
    }

    private function getEnv($folders)
    {
        if (!in_array('.env', $folders)) {
            return false;
        }

        $config = parse_ini_file($this->getBasePath() . '.env', false, INI_SCANNER_RAW);
        if (!$config) {
            return false;
        }
        if (!array_key_exists('APP_KEY', $config)) {
            // Detection laravel
            // return false;
        }
        $this->setHost($config['DB_HOST']);
        $this->setPort($config['DB_PORT']);
        $this->setUser($config['DB_USERNAME']);
        $this->setPwd($config['DB_PASSWORD']);
        $this->setName($config['DB_DATABASE']);
        return true;
    }

    /**
     * Get the value of basePath
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Set the value of basePath
     */
    public function setBasePath($basePath): self
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * Get the value of host
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Set the value of host
     */
    public function setHost(string $host): self
    {
        if (!empty($host)) {
            $this->host = $host;
        }

        return $this;
    }

    /**
     * Get the value of port
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * Set the value of port
     */
    public function setPort(string $port): self
    {
        if (!empty($port)) {
            $this->port = $port;
        }

        return $this;
    }

    /**
     * Get the value of user
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * Set the value of user
     */
    public function setUser(string $user): self
    {
        if (!empty($user)) {
            $this->user = $user;
        }
        return $this;
    }

    /**
     * Get the value of pwd
     */
    public function getPwd(): string
    {
        return $this->pwd;
    }

    /**
     * Set the value of pwd
     */
    public function setPwd(string $pwd): self
    {
        $this->pwd = $pwd;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName(string $name): self
    {
        if (!empty($name)) {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * Get the value of adapter
     */
    public function getAdapter(): string
    {
        return $this->adapter;
    }

    /**
     * Set the value of adapter
     */
    public function setAdapter(string $adapter): self
    {
        if (!empty($adapter)) {
            $this->adapter = $adapter;
        }

        return $this;
    }

    /**
     * Get the value of charset
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * Set the value of charset
     */
    public function setCharset(string $charset): self
    {
        if (!empty($charset)) {
            $this->charset = $charset;
        }

        return $this;
    }

    /**
     * Get the value of collation
     */
    public function getCollation(): string
    {
        return $this->collation;
    }

    /**
     * Set the value of collation
     */
    public function setCollation(string $collation): self
    {
        if (!empty($collation)) {
            $this->collation = $collation;
        }

        return $this;
    }
}
