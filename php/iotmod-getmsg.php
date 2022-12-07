<?php

function getMessages() {
  header('Content-Type: text/plain');
  $msgs=0;
  while (($json=getNextMessage())) {
//    error_log("fetch message {".$json."}\n");
    Msg::fromJsonExecute($json);
    if ($msgs++>20) {
      echo "OK, maximum message number reached.\n";
      return;
    }
  }
  echo "OK\n";
}

function getNextMessage() {     // get next message from a message queue
  // Create the context for the request
  $context = stream_context_create(array(
    'http' => array(
        'method' => 'GET',
        'timeout' => 10         // seconds
    )
  ));
  $response = file_get_contents(MBOXURL.'?del=1&dst='.MYDST, FALSE, $context);  // send the request
  if (substr($response,0,4)==="null") $response='';
  return $response;
}

Msg::registerLocalAction('getmsg', 'getMessages');
