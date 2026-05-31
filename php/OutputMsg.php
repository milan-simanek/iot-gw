<?php

abstract class OutputMsg extends Msg {
  function getData() { return $this; }
  abstract function Update();
  function GetContent() {
    $this->Update();
    return json_encode([ 'type' => static::TYPE, 'dst' => static::DST, 'mts' => $this->mts, 'data' => $this->getData()]);
  }
  function newPostMsg() {
      $ch = curl_init(MBOXURL);
      curl_setopt_array($ch, [
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POST           => true,
          CURLOPT_POSTFIELDS     => $this->GetContent(),
          CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
          CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
          CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2,
          CURLOPT_TIMEOUT        => 10,
      ]);

      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $error    = curl_error($ch);
      curl_close($ch);

      if ($response === false || $httpCode !== 200) {
          error_log("PostMsg curl error: $error (HTTP $httpCode)");
          die('Error posting a message');
      }

      $status = json_decode($response, false, 3);
      if (!$status || !isset($status->result) || $status->result !== "OK") {
          error_log("publishing message returned: $response");
          return false;
      }

      return true;
  }
  function oldPostMsg() {
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
  function PostMsg() {
    return $this->newPostMsg();
  }
  function GetMsg() { // we are answering to HTTP GET
    header("Content-Type: application/json\r\n");
    $m=$this->GetContent();
    echo $m."\n";
    return TRUE;
  }
}
