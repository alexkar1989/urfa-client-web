<?php

/**
 * Class Urfa
 */
class Urfa
{
    public $status;
    public $result = [];
    public $params;
    public $error;
    public $debug = false;
    private $command;
    private $login;
    private $password;
    private $app;
    private $host;
    private $urfaPath;

    /**
     * Urfa constructor.
     * @param $host
     * @param $urfaPath
     * @param $app
     * @param $login
     * @param $password
     */
    public function __construct($host, $urfaPath, $app, $login, $password)
    {
        $this->host = $host;
        $this->urfaPath = $urfaPath;
        $this->app = $app;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @param      $key
     * @param      $value
     * @param bool $add
     */
    public function addParameter($key, $value, bool $add = true)
    {
        if ($add) $this->params .= " -$key" . " '" . $value . "'";
        else $this->params = " -$key" . " '" . $value . "'";
    }

    /**
     * @param      $method
     * @param bool $byUser
     * @return bool
     * @throws Exception
     */
    public function execute($method, bool $byUser = false)
    {
        $this->command = $this->urfaPath . "/bin/utm5_urfaclient" . " -c " . $this->urfaPath . "/utm5_urfaclient.cfg" . " -h " . $this->host . " -a " . $this->app . "/" . $method . ($byUser ? " -u " : "") . " -l " . $this->login . " -P " . $this->password . $this->params . ($this->debug ? " -debug " : "");
		Model::writeLogs($this->command);
        $result = $this->execCommand();
        if ($this->debug) dump($this->error, $result);
        $this->status = preg_replace("/[^\d]/", "", stristr($this->error, 'code'));
        $this->error = strtok(stristr($this->error, "ERROR:"), ".");
        if (preg_match("/code/", $this->error)) $this->status = preg_replace("/[^\d]/", "",
            stristr($this->error, 'code'));
        if ($result) $this->XmlToArray(new SimpleXMLElement($result));
    }

    /**
     * @param $xml
     */
    private function XmlToArray($xml)
    {
        $data = [];
        if (isset($xml->call)) {
            foreach ($xml->call as $fResult) {
                $fName = (string)$fResult['function'];
                $data[$fName] = $this->toArray($fResult->output[0]);
            }
        }
        else {
            for ($i = 0; $i < count($xml->array); $i++) {
                foreach ($xml->array[$i]->dim as $dim) {
                    $data[(string)$xml->array[$i]['name']][] = (string)$dim;
                }
            }
        }
        $this->result = $data;
    }

    /**
     * @param $xml
     * @return array
     */
    private function toArray($xml): array
    {
        $data = [];
        $tName = "";
        $tType = "";
        foreach ($xml as $type => $out) {
            switch ($type) {
                case 'integer':
                    $data[(string)$out['name']] = (int)$out['value'];
                    $tName = (string)$out['name'];
                    break;
                case 'double':
                    $data[(string)$out['name']] = (float)$out['value'];
                    break;
                case 'array':
                    if ($tType != 'integer') $tName = (string)$out['name'];

                    $data[$tName] = [];

                    foreach ($out->item as $item) {
                        $data[$tName][] = $this->toArray($item);
                    }
                    break;
                default:
                    $data[(string)$out['name']] = (string)$out['value'];
                    break;
            }
            $tType = $type;
        }

        return $data;
    }

    /**
     * @return bool|string
     */
    private function execCommand()
    {
        $process = proc_open($this->command, [["pipe", "r"], ["pipe", "w"], ["pipe", "w"]], $pipes);
        if (!is_resource($process)) return false;
        $result = stream_get_contents($pipes[1]);
        $this->error = stream_get_contents($pipes[2]);
        proc_close($process);
        return $result;
    }
}
