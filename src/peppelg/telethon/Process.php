<?php

namespace peppelg\telethon;

class Process
{
    public static $process = null;
    public static $pipes = null;
    public static function new()
    {
        self::$process = proc_open('python ' . escapeshellarg(__DIR__ . '/php.py'), array(0 => array('pipe', 'r'), 1 => array('pipe', 'w')), self::$pipes);
        if (is_resource(self::$process)) {
            register_shutdown_function(function() {
                \peppelg\telethon\Process::write(['type' => 'exit']);
                \peppelg\telethon\Process::read();
            });
            return true;
        } else {
            throw new ProcessException('Cannot create python process.');
        }
    }
    public static function write($array)
    {
        return fwrite(self::$pipes[0], json_encode($array).PHP_EOL);
    }
    public static function read()
    {
        $response = json_decode(fgets(self::$pipes[1]), 1);
        if ($response['type'] === 'error') {
            $exn = '\peppelg\telethon\\' . $response['exception'];
            if (!class_exists($exn)) {
                eval('namespace peppelg\telethon; class ' . $response['exception'] . ' extends \peppelg\telethon\TelethonException { } '); //very bad
            }
            if (class_exists($exn)) {
                throw new $exn($response['error']);
            } else {
                throw new TelethonException($response['error']);
            }
        } else {
            return $response;
        }
    }
}

class ProcessException extends \Exception
{ }

class TelethonException extends \Exception
{ }
