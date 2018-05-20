<?php
$telethonphp_process = array();
class Telethon {
  private $process;
  private $pipes;
  public function __construct($session, $api_id, $api_hash) {
    global $telethonphp_process;
    register_shutdown_function(function() {
      global $telethonphp_process;
      foreach ($telethonphp_process as $process) {
        proc_terminate($process); //non funziona ma lo metto lo stesso
        $pid = proc_get_status($process)['pid']+1; //proc_get_status mi dà il pid sbagliato,, per fixx devo aggiungere 1
        if (function_exists('posix_kill')) {
          posix_kill($pid, SIGTERM);
        } else {
          shell_exec('kill '.escapeshellarg($pid)); //se non esiste posix_kill lo killa con shell_exec
        }
      }
    });
    if (file_exists(__DIR__.'/php.py')) {
      $this->process = proc_open('python3 '.escapeshellarg(__DIR__.'/php.py'), array(0 => array('pipe', 'r'), 1 => array('pipe', 'w')), $this->pipes);
      if (is_resource($this->process)) {
        array_push($telethonphp_process, $this->process);
        fwrite($this->pipes[0], json_encode(array('action' => 'new_client', 'session' => $session,'api_id' => $api_id, 'api_hash' => $api_hash))."\n");
        $result = json_decode(fgets($this->pipes[1]), true);
        if ($result['result'] === 'error') throw new Exception($result['error']);
        else return $result['result'];
      } else {
        throw new Exception('Error');
      }
    } else {
      throw new Exception('Telethon not found');
    }
  }
  public function __call($method, $args=NULL) {
    fwrite($this->pipes[0], json_encode(array('action' => 'call', 'method' => $method, 'args' => $args))."\n");
    $result = json_decode(fgets($this->pipes[1]), true);
    if ($result['result'] === 'error') throw new Exception($result['error']);
    else return $result['result'];
  }
}
