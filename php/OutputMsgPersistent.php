<?php

abstract class OutputMsgPersistent extends OutputMsg {
  static $pendingStore = [];

  static function LoadInstance($i='0') {
    $name=static::TYPE.'|'.$i;
    if (array_key_exists($name, self::$pendingStore)) return static::$pendingStore[$name];
    $class=get_called_class();
    if (isset($_SESSION['INST']) && isset($_SESSION['INST'][$name])) {
      $s=$_SESSION['INST'][$name];
      if ($s) {
        $obj=unserialize($s);
        if (get_class($obj)==$class) {
           self::$pendingStore[$name]=$obj;
           return $obj;
        }
        error_log("Persistant object load error: loaded class ".get_class($obj).", expected class ".get_called_class());
      }
    }
    $obj=new $class();
    error_log("$name: new $class initialized.");
    self::$pendingStore[$name]=$obj;
    return $obj;
  }
  static function StoreInstances() {
    if (!isset($_SESSION['INST'])) $_SESSION['INST']=[];
    foreach(self::$pendingStore as $name=>$obj) {
      $_SESSION['INST'][$name]=serialize($obj);
    }
  }
}

