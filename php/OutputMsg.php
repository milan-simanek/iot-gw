<?php

abstract class OutputMsg extends Msg {
  function getData() { return $this; }
  abstract function Update();
  function GetContent() {
    $this->Update();
    return json_encode([ 'type' => static::TYPE, 'dst' => static::DST, 'mts' => $this->mts, 'data' => $this->getData()]);
  }
  function PostMsg() {
    $context = stream_context_create(array(
      'http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => $this->GetContent()]
    ));
    $response = file_get_contents(MBOXURL, FALSE, $context);	// send the request
    if($response === FALSE) die('Error posting a message');
    $status=json_decode($response, false, 3);
    if (!$status || !isset($status->result) || $status->result!="OK") {
      error_log("publishing message returned: $response");
      return FALSE;
    }
    return TRUE;
  }
  function GetMsg() { // we are answering to HTTP GET
    header("Content-Type: application/json\r\n");
    $m=$this->GetContent();
    echo $m."\n";
    return TRUE;
  }
}
