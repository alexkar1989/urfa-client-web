<?php

/**
 * Class Model
 */
class Model
{
    /**
     * @var $app
     */
    protected $app;

    /**
     * Model constructor.
     * @param $app
     */
    function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Генерация ответа
     *
     * @param int $code
     * @param array $data
     * @param bool $exit
     */
    public static function generateAnswer($code = 200, $data = array(), $exit = true)
    {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($exit) exit();
    }
}