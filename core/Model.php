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
     * @param $data
     * @param bool  $exit
     */
    public static function generateAnswer(int $code = 200, $data = [], bool $exit = true)
    {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($exit) exit();
    }

    public static function writeLogs($text, $file = LOGS)
    {
        $file = fopen($file, "a+");
        fwrite($file, date("d.m.Y H:i:s") . "; Ip: " . USER_IP . "; " . $text . "\n");
        fclose($file);
    }

}
