<?php
require 'peppelg/telethon/Process.php';
require 'peppelg/telethon/TelegramClient.php';

\peppelg\telethon\Process::new('python');



$client = new \peppelg\telethon\TelegramClient('session', 6, 'eb06d4abfb49dc3eeb1aeb98ae0f581e');
$client->connect();
$client->start();
$callback = function ($update) use ($client) {
    if (isset($update['message']['message'])) {
        $msg = $update['message']['message'];
        echo "Nuovo mex:: $msg";
    }
};
$client->add_event_handler($callback);
