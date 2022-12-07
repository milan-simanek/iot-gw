<?php

class IotDev {
  const SockPath = "/run/iothub-con.sock";
  const MaxAnswerLen = 100;
  private $sock;
  private $peer;
  
  
  public function __construct($dev) {
    $this->peer=$dev;
    if (FALSE === ($this->sock = socket_create(AF_UNIX, SOCK_STREAM, 0))) die("cannot open socket");
    if (!socket_bind($this->sock, "")) die("FAIL to bind!\n");
    if (!socket_connect($this->sock, static::SockPath)) die("cannot connect to UX socket");
    socket_send($this->sock, $this->peer."\n", 2, 0);
  }
  public function __destruct() {
    if ($this->sock) socket_close($this->sock);
    $this->sock=NULL;
  }
  public function send($rq) {
    $n=socket_send($this->sock, $rq."\n", strlen($rq)+1, 0);
  }
  public function recv($pattern) {
    socket_set_option($this->sock,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>10,"usec"=>0));
    socket_recv($this->sock, $ans, static::MaxAnswerLen, 0 );
    if (preg_match($pattern, $ans, $m)) return $m;
    return NULL;
  } 
}
