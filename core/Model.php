<?php

/**
 * Class Model
 */
class Model
{
    /**
     * @var false|int|string|null
     */
    protected $app = null;

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
     * @param int   $code
     * @param array $data
     * @param bool  $exit
     */
    public static function generateAnswer(int $code = 200, array $data = array(), bool $exit = true)
    {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($exit) exit();
    }
}