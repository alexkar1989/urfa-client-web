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
     * @var array|string
     */
    private $nets = [];

    /**
     * @var array
     */
    private $excludes = [];

    /**
     * Network constructor.
     */
    function __construct()
    {
        foreach (func_get_args() as $arg) {
            $this->addNetworks($arg);
        }
        return $this;
    }

    /**
     * @return Network
     */
    static function init(): Network
    {
        $tt = new Network();
        foreach (func_get_args() as $arg) {
            $tt->addNetworks($arg);
        }
        return $tt;
    }

    /**
     * Сама проверка
     * @param $ip
     * @return bool
     */
    public function checkIp($ip): bool
    {
        return $this->check($this->nets, $ip) && !$this->check($this->excludes, $ip);
    }

    /**
     *Добавление сети к массиву
     * @return $this
     */
    public function addNetworks(): Network
    {
        foreach (func_get_args() as $arg) {
            $arg = $this->validateArg($arg);
            if (is_array($arg)) $this->nets = array_merge($this->nets, $arg);
            else $this->nets[] = $arg;
        }
        return $this;
    }

    /**
     * Посмотреть какие сети в данные момент в массиве
     */
    public function getNetworks(): array
    {
        return $this->nets;
    }

    /**
     * Отчистка массива сетей и создание новой
     * @return $this
     */
    public function changeNetworks(): Network
    {
        $this->clearNetworks();
        foreach (func_get_args() as $arg) {
            $this->addNetworks($arg);
        }
        return $this;
    }

    /**
     * Удаление сети из массива
     * @return $this
     */
    public function deleteNetworks(): Network
    {
        foreach (func_get_args() as $arg) {
            $arg = $this->validateArg($arg);
            if (is_array($arg)) $this->nets = array_diff($this->nets, $arg);
            else {
                if (($key = array_search($arg, $this->nets)) !== false) unset($this->nets[$key]);
            }
        }
        return $this;
    }

    /**
     * Отчистка массива сетей
     * @return $this
     */
    public function clearNetworks(): Network
    {
        $this->nets = [];
        return $this;
    }

    /**
     * @return $this
     */
    public function addExclude(): Network
    {
        foreach (func_get_args() as $arg) {
            $arg = $this->validateArg($arg);
            if (is_array($arg)) $this->excludes = array_merge($this->excludes, $arg);
            elseif (!in_array($arg, $this->excludes)) $this->excludes[] = $arg;
        }
        return $this;
    }

    /**
     *
     */
    public function getExclude(): array
    {
        return $this->excludes;
    }

    /**
     * @return $this
     */
    public function changeExclude(): Network
    {
        $this->clearExclude();
        foreach (func_get_args() as $arg) {
            $this->addExclude($arg);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteExclude(): Network
    {
        foreach (func_get_args() as $arg) {
            $arg = $this->validateArg($arg);
            if (is_array($arg)) $this->excludes = array_diff($this->excludes, $arg);
            else {
                if (($key = array_search($arg, $this->excludes)) !== false) unset($this->excludes[$key]);
            }
        }
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function clearExclude(): Network
    {
        $this->excludes = [];
        return $this;
    }

    /**
     * @param $networks
     * @param $ip
     * @return bool
     */
    private function check($networks, $ip): bool
    {
        $ip = ip2long($ip);
        foreach ($networks as $nets) {
            if (preg_match('/(\d{1,3})(\.(?1)){3}(\/(\d{1,2}))?$/', $nets, $matches)) {
                @list($net, $mask) = explode('/', $nets);
                $mask = empty($mask) ? 32 : $mask;
                $net = ip2long($net);
                $mask = pow(2, 32 - $mask) - 1;
                $net = $net & ~$mask;
                if (!(($ip ^ $net) & ~$mask)) return true;
            }
            elseif (preg_match('/((\d{1,3})(\.(?2)){3})\s?\-\s?(?1)$/', $nets, $matches)) {
                [$net_d, $net_u] = explode('-', $nets);
                if ($ip >= ip2long(trim($net_d)) && $ip <= ip2long(trim($net_u))) return true;
            }
        }
        return false;
    }

    /**
     * Валидация входных параметров
     * @param $arg
     * @return array|string
     */
    private function validateArg($arg)
    {
        $res = [];
        if (is_array($arg)) {
            foreach ($arg as $item) {
                if (preg_match('/^((2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?2)){3})\s?\-\s?(?1)$/', $item, $matches)) $res[] = $matches[0];
                if (preg_match('/^(2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?1)){3}(\/(\d{1,2}))?([!](2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?1)){3})+$/', $item, $matches)) {
                    $excludes = explode('!', $matches[0]);
                    $res = $excludes[0];
                    array_shift($excludes);
                    $this->excludes = array_merge($this->excludes, $excludes);
                }
                if (preg_match('/^(2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?1)){3}(\/(\d{1,2}))?$/', $item, $matches)) $res[] = $matches[0];
            }
        }
        else {
            if (preg_match('/^((2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?2)){3})\s?\-\s?(?1)$/', $arg, $matches)) $res = $matches[0];
            if (preg_match('/^(2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?1)){3}(\/(\d{1,2}))?([!](2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?1)){3})+$/', $arg, $matches)) {
                $excludes = explode('!', $matches[0]);
                $res = $excludes[0];
                array_shift($excludes);
                $this->excludes = array_merge($this->excludes, $excludes);
            }
            if (preg_match('/^(2[0-4]\d|25[0-5]|1\d\d|\d\d?)(\.(?1)){3}(\/(\d{1,2}))?$/', $arg, $matches)) $res = $matches[0];
        }

        return $res;
    }
}

