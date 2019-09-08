<?php 
require 'peppelg/telethon/Process.php';
require 'peppelg/telethon/TelegramClient.php';

echo \peppelg\telethon\Process::new();



$client = new \peppelg\telethon\TelegramClient('session', 6, 'eb06d4abfb49dc3eeb1aeb98ae0f581e');
$client->connect();
$client->start();
$callback = function($event) {
    echo 'cosita';
};
$client->add_event_handler($callback);