<?php 
namespace peppelg\telethon;
use peppelg\telethon\Process;

class TelegramClient {
    public function __construct($name, $api_id, $api_hash) {
        Process::write(['type' => 'new TelegramClient', 'name' => $name, 'api_id' => $api_id, 'api_hash' => $api_hash]);
        return Process::read();
    }
    public function start() {
        if (!$this->is_user_authorized()) {
            $phone = readline('Please enter your phone number: ');
            $request = $this->sign_in($phone);
            $code = readline('Please enter the code you received: ');
            try {
                $request = $this->sign_in($phone, $code);
            } catch (SessionPasswordNeededError $e) {
                $password = readline('Please enter your password: ');
                $request = $this->sign_in(['phone' => $phone, 'password' => $password]);
            }
            if ($this->is_user_authorized()) {
                echo 'Signed in successfully' . PHP_EOL;
            }
            return $request;
        } else {
            return true;
        }
    }
    public function add_event_handler($callback) { #broken
        if (is_callable($callback)) {
            Process::write(['type' => 'new callback']);
            var_dump(Process::read());
        } else {
            throw new \peppelg\telethon\TelethonException('Invalid callback');
        }
    }
    public function __call($method, $args = null) {
        if (isset($args[0]) and is_array($args[0])) {
            $args = $args[0];
        }
        Process::write(['type' => 'TelegramClient', 'method' => $method, 'args' => $args]);
        $response = Process::read();
        if ($response['success']) {
            return $response['response'];
        }
    }
}