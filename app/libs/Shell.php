<?php
namespace App\Libs;

class Shell
{
    private $log = array();

    public function __construct()
    {
        $this->cd();
    }

    public function cd($path = null)
    {
        return $this->exec('cd '.escapeshellarg($path ?: base_path()));
    }

    public function exec($cmd, $escape = false)
    {
        if ($escape) {
            $cmd = escapeshellcmd($cmd);
        }

        $error = storage_path().'/logs/'.md5($cmd.microtime()).'.err';

        if (is_file($error)) {
            unlink($error);
        }

        $response = shell_exec($cmd.' 2> '.$error);

        $this->log[] = [
            'command' => $cmd,
            'success' => ($response ? true : false),
            'response' => $response,
            'error' => file_get_contents($error)
        ];

        if (is_file($error)) {
            unlink($error);
        }

        return $response;
    }

    public function getLog($offset = 0, $length = null)
    {
        if ($offset || $length) {
            return array_slice($this->log, $offset, $length, true);
        }

        return $this->log;
    }

    public function exists($cmd)
    {
        return (strlen($cmd) && $this->exec('which '.escapeshellcmd($cmd)));
    }
}
