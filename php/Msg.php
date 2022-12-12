<?php

abstract class Msg {
  const GETMAXLEN = 4096;
  const DSTMAXLEN = 30;
  const TYPEMAXLEN = 30;
  private static $allLocal=[];
  private static $allGet=[];
  private static $allPost=[];
  private static $recvMap=[];
  public static function registerLocalAction($action, $func)	{ self::$allLocal[$action]=$func; }
  public static function registerGetAction($dst, $func)		{ self::$allGet[$dst]=$func; }
  public static function registerPostAction($dst, $type, $func)	{ self::$allPost[$dst.":".$type]=$func; }
  public static function registerReceive($type, $class)		{ self::$recvMap[$type]=$class; }
  public static function allowReceive()				{ self::$recvMap[static::TYPE]=get_called_class(); }
  public static function registerGetPersistent($dst, $class, $inst=0) { 
    self::registerGetAction($dst, function() use($class, $inst) { 
      return $class::LoadInstance($inst)->GetMsg(); 
    }); 
  }
  public static function registerPublish($dst, $class, $inst=0) {
    self::registerLocalAction($dst, function() use($class, $inst) {
      return $class::LoadInstance($inst)->PostMsg();
    });
  }
  
  public static function fromJsonExecute(string $json) {
    $obj=json_decode($json, false, 10);
    if ($obj===true || $obj===false || $obj===null||!isset($obj->type)) return;
//    if ($obj===true || $obj===false || $obj===null||!isset($obj->type)||!isset($obj->dst)) return;
    if (!array_key_exists($obj->type, self::$recvMap)) {
      error_log("Unknown incoming message '".$obj->type."'");
      error_log("  supported messages: ".implode(',', array_keys(self::$recvMap)));
      die("error");
    }
    $class=self::$recvMap[$obj->type];
//    if ($class::DST!=$obj->dst) return;
    if (!$class) return;
    $msg=new $class();
    foreach(get_object_vars($msg) as $var=>$val) if (isset($obj->data->$var)) {
      $msg->$var=$obj->data->$var;
    }
    if ($msg->execute($obj->mts)) {
      $ts=mTS();
      echo '{ "result": "OK", "mts": '.$ts." }\n";
    } else error_log("execute message failed TYPE=".$msg->TYPE);
  }    
  public static function handleRest() {
    if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') foreach(self::$allLocal as $a=>$f) if ($a==$_SERVER['QUERY_STRING']) return $f();
    if ($_SERVER['REQUEST_METHOD']=='POST') {
      header('Content-Type: text/plain');
      $json = file_get_contents('php://input', false, null, 0, self::GETMAXLEN);
      static::fromJsonExecute($json);
    } else if ($_SERVER['REQUEST_METHOD']=='GET') {
      header('Content-Type: text/plain');
      if ($_SERVER['QUERY_STRING']=='ping') {
        $msg = array('mts' => mTS(), 'result' => "OK");
        header("Content-Type: application/json\r\n");
        echo json_encode($msg)."\n";
        return TRUE;
      }
      if (!isset($_GET['dst'])) die('bad request');
      $dst=substr($_GET['dst'], 0, self::DSTMAXLEN);
      if (isset(self::$allGet[$dst])) {
        $f=self::$allGet[$dst];
        return $f();
      }
      die('bad request');
    }
  }

  const DST='';
  const TYPE='';
}
