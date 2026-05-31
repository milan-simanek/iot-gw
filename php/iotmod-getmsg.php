<?php

function getMessages() {
  header('Content-Type: text/plain');
  $msgs=0;
  while (($json=getNextMessage())) {
//    error_log("fetch message {".$json."}\n");
    Msg::fromJsonExecute($json, 'MBOX');
    if ($msgs++>20) {
      echo "interrupted, maximum message number reached.\n";
      return;
    }
  }
  echo "OK\n";
}

function newGetNextMessage() {
  $ch = curl_init(MBOXURL.'?del=1&dst='.MYDST);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,  // force HTTP/1.1
    CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2,
    CURLOPT_TIMEOUT        => 10,
 ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $error    = curl_error($ch);
  curl_close($ch);
  if ($httpCode!=200) return '';
  if (substr($response,0,4)==="null") return '';
  return $response;
}
function oldGetNextMessage() {     // get next message from a message queue
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
function getNextMessage() {
  return newGetNextMessage();
}
Msg::registerLocalAction('getmsg', 'getMessages');
