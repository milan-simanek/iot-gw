<?php

$MODS=[];

if (is_file('config-default.php')) require_once 'config-default.php';
if (is_file('config.php')) require_once 'config.php';

spl_autoload_register(function ($class_name) {
    foreach($MODS as $M) if (is_file($M.'/'.$class_name) 
      return require_once $M.'/'.$class_name;
    require_once $class_name . '.php';
});


function mTS() {	// milli time-stamp
  // "0.53042800 1647443466"
  return preg_replace('/^0[.]([0-9]{3})[0-9]{5} ([0-9]*)$/', '\2\1', microtime(FALSE));
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

function addModule($subdir,	// a directory (relative) where module files are located
                   $modfile,	// module definition file (just filename) located in $subdir
                   $path='') {	// full path to the module directory (hope it could be removed)
  // $moddef=$path.'/'.$modfile;
  if ($subdir===NULL) {
    $moddef=$modfile;
  } else {
    $moddef=$subdir.'/'.$modfile;
    if (!is_dir($subdir)) return FALSE;
    $MODS[]=$subdir;
  }
  if (!is_file($moddef)) return FALSE;
  require_once $moddef;
  return TRUE;
}

foreach(explode(':', get_include_path()) as $dir) {
  if (!is_dir($dir)) continue;
  $dh=opendir($dir);
  if (!$dh) continue;
  while (($entry = readdir($dh)) !== FALSE) {
    if (preg_match('/^iotmod-[^.]*$/', $entry)) {
       if (addModule($entry, $entry.'.php', $dir.'/'.$entry)) continue;
       if (addModule($entry, 'iotmod.php', $dir.'/'.$entry)) continue;
    }
    if (preg_match('/^iotmod-[^.]*[.]php$/', $entry)) 
       addModule(NULL, $entry, $dir.'/'.$entry, )) continue;
    }  
  }
  closedir($dh);
}

session_id('IoT gateway');
session_start();	// must be executed after all classes declared or autoloader activated

Msg::handleRest();

session_write_close();
exit(0);

