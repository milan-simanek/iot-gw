<?php

abstract class MsgSetInterval extends MsgSetInstant {
//  const MAXAFTER=3600*24*90;	// max after [s] from now
  const MAXDURATION=31*24*3600;	// max interval duration
//  const DATACLASS='';		// persistent class name
//  const SMTS='';		// MTS field in object of DATACLASS
//  const MCUCMD='?';		// MCU command
//  const DEV='c';
  
  public $duration=0;
  function apply($O, $mts) {
    $delay=$duration=0;
    if ($this->value) {
      $delay=$this->value-time();
      if ($delay<0 || $delay>static::MAXAFTER) $delay=0;
    };
    if ($this->duration) $duration=intval($this->duration);
    if ($duration<=0) $duration=$delay=0;
    return $this->applyMCU($duration.','.$delay);
  }
}
