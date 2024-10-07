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
    $sec=$duration=0;
    if ($this->value) {
      $sec=$this->value-time();
      if ($sec<0 || $sec>static::MAXAFTER) $sec=0;
    };
    if ($this->duration) $duration=intval($this->duration);
    if ($duration<=0) $duration=$sec=0;
    return $this->applyMCU($sec.','.$duration);
  }
}
