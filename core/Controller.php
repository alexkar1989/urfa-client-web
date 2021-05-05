<?php

/**
 * Class Controller
 */
class Controller
{
    /**
     * @var array
     */
    private $api_key = [];

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var false|int|string|null
     */
    protected $app = null;

    /**
     * Controller constructor.
     */
    public function __construct($api_key = [])
    {
        $this->api_key = array_merge($this->api_key, $api_key);
        if (isset($_SERVER['HTTP_APIKEY'])) {
            if (($this->app = array_search($_SERVER['HTTP_APIKEY'],
                    $this->api_key)) === false) Model::generateAnswer(403);
        }
        if (isset($_REQUEST['api_key'])) {
            if (($this->app = array_search($_REQUEST['api_key'], $this->api_key)) === false) Model::generateAnswer(403);
        }
        $this->model = self::loadModel(strtolower(substr(get_class($this), 1)), $this->app);
    }

    /**
     * @param $name
     * @param $app
     * @return Model
     */
    static function loadModel($name, $app): Model
    {
        try {
            $modelName = 'M' . ucfirst($name);
            $model = new $modelName($app);
        } catch (Exception $e) {
            $model = new Model($app);
        }
        return $model;
    }
}

/**
 * @param mixed ...$errs
 */
function dd(...$errs)
{
    if (call_user_func_array('dump', $errs)) exit();
}

/**
 * @param mixed ...$errs
 * @return bool
 */
function dump(...$errs): bool
{
    if (php_sapi_name() === 'cli') {
        foreach ($errs as $err) {
            if (is_array($err) || is_object($err)) {
                echo print_r($err, true);
            }
            else {
                echo print_r($err . "\n", true);
            }
        }
        return true;
    }
    else {
        if ((new Network(ADMIN_IPS))->checkIp($_SERVER['REMOTE_ADDR'])) {
            foreach ($errs as $err) {
                echo '<pre>' . print_r($err, true) . '</pre>';
            }
            return true;
        }
    }
    return false;
}

