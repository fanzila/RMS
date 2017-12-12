<?php 

# Stolen from 
# https://stackoverflow.com/questions/1342583/manipulate-a-string-that-is-30-million-characters-long/1342760#1342760

Class Camera_proxy {
  
  function stream_open($path, $mode, $options, &$opened_path)
  {
    // Has to be declared, it seems...
    return true;
  }
  
  public function stream_write($data)
  {
    echo $data;

    return strlen($data);
  }
  
}

?>