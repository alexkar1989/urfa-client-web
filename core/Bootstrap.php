<?php

// запрет прямого обращения
//defined('JEXEC') or header('HTTP/1.0 403 Forbidden');

/**
 * Class Bootstrap
 */
class Bootstrap
{
    /**
     * @var string Активные контроллер
     */
    private $module = 'Urfa';

    /**
     * @var string Активный метод
     */
    private $action = 'index';

    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->Route();
        $this->module = 'C' . ucfirst($this->module);

        if (!class_exists($this->module)) Model::generateAnswer(404);
        $controller = new $this->module();
        $action = $this->action;

        if (method_exists($controller, $action)) $controller->$action();
        else Model::generateAnswer(404);
    }

    /**
     * Разбор строки запроса
     */
    private function Route()
    {
        $params = array();
        if ($_SERVER['REQUEST_URI'] != '/') {
            try {
                $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $uri_parts = explode('/', trim($url_path, ' /'));
                if (count($uri_parts) > 2 && count($uri_parts) % 2) {
                    throw new Exception();
                }
                $inputJSON = file_get_contents('php://input');
                $post = json_decode($inputJSON, TRUE);
                $post = is_array($post) ? $post : array();
                $_POST = array_merge($_POST, $post);
                $_REQUEST = array_merge($_REQUEST, $params, $post);
            } catch (Exception $e) {
                Model::generateAnswer(404);
            }
        }
    }
}
