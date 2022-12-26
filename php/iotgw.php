<?php

session_id('IoT-Gateway');
session_start();	// must be executed after all classes declared or autoloader activated

include_once 'config.php';

spl_autoload_register(function ($class_name) {
    require_once $class_name.'.php';
});


function mTS() {	// milli time-stamp
  // "0.53042800 1647443466"
  return preg_replace('/^0[.]([0-9]{3})[0-9]{5} ([0-9]*)$/', '\2\1', microtime(FALSE));
}

function sec2ts($s) {   // converts remaining seconds to timestamp, or 0 if not set
  return $s ? $s + time() : 0;
}


function loadPersistentObject($name, $class) {
  $GLOBALS[$name]=null;
  if (isset($_SESSION[$name])) $GLOBALS[$name]=$_SESSION[$name];
  if ($GLOBALS[$name]==null || get_class($GLOBALS[$name])!==$class) { 
    $GLOBALS[$name]=new $class();
    error_log("$name: new $class initialized.");
    $_SESSION[$name]=$GLOBALS[$name];
  }
}

function addModule($modfile,		// module definition file (just filename) located in $subdir
                   $path='',		// full path to the module directory
                   $isnew=FALSE) {	// should this new path be regiestered
  if (!is_file($path.'/'.$modfile)) return FALSE;
  if ($isnew) set_include_path(get_include_path().":".$path);
  require_once $modfile;
  return TRUE;
}

foreach(explode(':', get_include_path()) as $dir) {
  if (!is_dir($dir)) continue;
  $dh=opendir($dir);
  if (!$dh) continue;
  while (($entry = readdir($dh)) !== FALSE) {
    if (preg_match('/^iotmod-[^.]*$/', $entry)) {
       if (addModule($entry.'.php', $dir.'/'.$entry, TRUE)) continue;
       if (addModule('iotmod.php', $dir.'/'.$entry, TRUE)) continue;
    }
    if (preg_match('/^iotmod-[^.]*[.]php$/', $entry)) addModule($entry, $dir);
  }
  closedir($dh);
}


Msg::handleRest();

OutputMsgPersistent::StoreInstances();
session_write_close();
exit(0);

