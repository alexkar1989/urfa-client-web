<?php

/**
 * Class MUrfa
 */
class MUrfa extends Model
{
    private Urfa $urfa;

    /**
     * MUrfa constructor.
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->app = $app;
    }

    /**
     * @param string $host
     * @param string $urfaPath
     * @param string $function
     * @param string $login
     * @param string $password
     * @param array  $params
     * @param bool   $byUser
     * @throws Exception
     */
    public function urfaQuery(string $host, string $urfaPath, string $function, string $login, string $password,
                              array $params = [], bool $byUser = false)
    {
        $this->urfa = new Urfa($host, $urfaPath, $this->app, $login, $password);
        if (isset($params['debug']) && $params['debug'] == true) $this->urfa->debug = true;
        if (!empty($params)) $this->createData($params);
        $this->urfa->execute($function, $byUser);
        if (!$this->urfa->status && $this->urfa->error) {
            Model::writeLogs($this->urfa->error);
            Model::generateAnswer(500, $this->urfa->error);
        }
        elseif ($this->urfa->status || $this->urfa->error) {
            Model::writeLogs($this->urfa->error);
            Model::generateAnswer($this->codeError($this->urfa->status), $this->urfa->error);
        }
        elseif (isset($this->urfa->result[$function]) && empty($this->urfa->result[$function])) Model::generateAnswer(204, $this->urfa->status);
        else Model::generateAnswer(200, $this->urfa->result);
    }

    /**
     * @param $params
     */
    private function createData($params)
    {
        if (isset($params['parameters']) && !empty($params['parameters'])) {
            foreach ($params['parameters'] as $parameter) {
                foreach ($parameter as $key => $value) {
                    $this->urfa->addParameter($key, $value);
                }
            }
            unset($params['parameters']);
        }
        foreach ($params as $param => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $this->urfa->addParameter($param, $item);
                }
            }
            else$this->urfa->addParameter($param, $value);
        }
    }

    /**
     * @param $code
     * @return int
     */
    private function codeError($code): int
    {
        $error = [
            2 => 500,
            10 => 500,
            13 => 401,
            20 => 500,
            100 => 400,
        ];
        return $error[$code];
    }
}
