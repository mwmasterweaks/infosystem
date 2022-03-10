<?php

namespace App\Helpers;

class SSH2
{
    var $ssh;
    var $stream;

    function __construct($host, $port = 22)
    {
        if (!$this->ssh = ssh2_connect($host, $port)) {
            return false;
        }
    }

    function auth($username, $auth, $private = null, $secret = null)
    {
        if (is_file($auth) && is_readable($auth) && isset($private)) {
            // If $auth is a file, and $private is set, try pubkey auth
            if (!ssh2_auth_pubkey_file($this->ssh, $username, $auth, $private, $secret)) {
                return false;
            }
        } else {

            // If not pubkey auth, auth with password
            if (!ssh2_auth_password($this->ssh, $username, $auth)) {
                return false;
            }
        }

        return true;
    }

    function send($local, $remote, $perm)
    {
        if (!ssh2_scp_send($this->ssh, $local, $remote, $perm)) {
            return false;
        }

        return true;
    }

    function get($remote, $local)
    {
        if (ssh2_scp_recv($this->ssh, $remote, $local)) {
            return false;
        }

        return true;
    }

    function cmd($cmd, $blocking = true)
    {
        $this->stream = ssh2_exec($this->ssh, $cmd);
        stream_set_blocking($this->stream, $blocking);
    }

    // Just an aliasfunction for $this->cmd
    function exec($cmd, $blocking = true)
    {
        $this->cmd($cmd, $blocking = true);
    }

    function output()
    {
        return stream_get_contents($this->stream);
    }

    function getTable()
    {
        $retval = [];
        $cols = [];
        $x = 0;
        foreach (preg_split("/((\r?\n)|(\r\n?))/", stream_get_contents($this->stream)) as $line) {

            if ($x == 2) {
                $cols = explode("\t", $line);
            }
            if ($x > 2) {
                // if (strpos($line, '\t') !== false) {
                if (str_contains($line, "\t")) {
                    //column data

                    $c1 = collect();
                    $data = explode("\t", $line);
                    $i = 0;
                    for ($i = 0; $i < count($data); $i++) {
                        $c1->put(str_replace(" ", "", $cols[$i]), $data[$i]);
                    }

                    array_push($retval, $c1);
                }
            }

            $x++;
        }
        return $retval;
    }

    public function test()
    {
        $connection = ssh2_connect('hosting-new.dctechmicro.com', 22);
        if (!$connection) {
            return "nect";
        } else {
            ssh2_auth_password($connection, 'root', '8tnhbaa0');

            $stream = ssh2_exec($connection, 'php -m');


            stream_set_blocking($stream, true);
            $data = "";
            while ($buf = fread($stream, 4096)) {
                $data .= $buf;
            }
            fclose($stream);


            return $data;
            //return strval($stream);
        }
    }

    public function disconnect()
    {
        $this->ssh = null;
        unset($this->ssh);
        return "discon";
    }

    public function test1()
    {
        return $this->ssh;
    }
}
