<?php

/**
 * Class Network Проверка ип адреса на принадлежность сети
 *
 * Using:
 * Allow arguments:  $nets = array(10.0.0.0/8, 10.0.0.1, 10.0.0.0 - 10.255.255.255)
 * $network = new Network($nets);
 * $network->checkIp('172.24.130.150');
 */
class Network
{
    /**
     * @var
     */
    public $ip;
    /**
     * @var array
     */
    private $nets = array();

    /**
     * Network constructor.
     */
    function __construct()
    {
        foreach (func_get_args() as $arg) {
            $this->addNetworks($arg);
        }
    }

    /**
     * Сама проверка
     * @param $ip
     * @return bool
     */
    public function checkIp($ip)
    {
        $ip = ip2long($ip);
        foreach ($this->nets as $nets) {
            if (preg_match('/(\d{1,3})(\.(?1)){3}(\/(\d{1,2}))?$/', $nets, $matches)) {
                @list($net, $mask) = explode('/', $nets);
                $mask = empty($mask) ? 32 : $mask;
                $net = ip2long($net);
                $mask = pow(2, 32 - $mask) - 1;
                $net = $net & ~$mask;
                if (!(($ip ^ $net) & ~$mask)) return true;
            } elseif (preg_match('/((\d{1,3})(\.(?2)){3})\s?\-\s?(?1)$/', $nets, $matches)) {
                list($net_d, $net_u) = explode('-', $nets);
                if ($ip >= ip2long(trim($net_d)) && $ip <= ip2long(trim($net_u))) return true;
            }
        }
        return false;
    }

    /**
     * Отчистка массива сетей
     */
    public function clearNetworks()
    {
        $this->nets = array();
    }

    /**
     * Посмотреть какие сети в данные момент в массиве
     */
    public function getNetworks()
    {
        return $this->nets;
    }

    /**
     * Отчистка массива сетей и создание новой
     */
    public function changeNetworks()
    {
        $this->clearNetworks();
        foreach (func_get_args() as $arg) {
            $this->addNetworks($arg);
        }
    }

    /**
     *Добавление сети к массиву
     */
    public function addNetworks()
    {
        foreach (func_get_args() as $arg) {
            $arg = $this->validateArg($arg);
            if (is_array($arg)) $this->nets = array_merge($this->nets, $arg);
            else $this->nets[] = $arg;
        }
    }

    /**
     *Удаление сети из массива
     */
    public function excludeNetworks()
    {
        foreach (func_get_args() as $arg) {
            $arg = $this->validateArg($arg);
            if (is_array($arg)) $this->nets = array_diff($this->nets, $arg);
            else {
                if (($key = array_search($arg, $this->nets)) !== false) unset($this->nets[$key]);
            }
        }
    }

    /**
     * Валидация входных параметров
     *
     * @param $arg
     * @return array
     */
    private function validateArg($arg)
    {
        $res = array();
        if (is_array($arg)) {
            foreach ($arg as $item) {
                if (preg_match('/^((2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?2)){3})\s?\-\s?(?1)$/', $item, $matches)) $res[] = $matches[0];
                if (preg_match('/^(2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?1)){3}(\/(\d{1,2}))?$/', $item, $matches)) $res[] = $matches[0];
            }
        } else {
            if (preg_match('/^((2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?2)){3})\s?\-\s?(?1)$/', $arg, $matches)) $res = $matches[0];
            if (preg_match('/^(2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?1)){3}(\/(\d{1,2}))?$/', $arg, $matches)) $res = $matches[0];
        }

        return $res;
    }
}

