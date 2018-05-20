<?php
require_once 'src/telethon.php';
$client = new Telethon('session', 6, 'eb06d4abfb49dc3eeb1aeb98ae0f581e');
$a = $client->send_code_request('numero di telefono');
print_r($a);
