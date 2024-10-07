<?php

abstract class MsgSetInstant extends MsgSet {
  const MAXAFTER=3600*24*90;	// max after [s] from now
//  const DATACLASS='';		// persistent class name
//  const SMTS='';		// MTS field in object of DATACLASS
//  const MCUCMD='?';		// MCU command
//  const DEV='c';
  
  public $value=0;
  function apply($O, $mts) {
    if ($this->value) {
      $sec=$this->value-time();
      if ($sec<0 || $sec>static::MAXAFTER) $sec=0;
    } else $sec=0;
    return $this->applyMCU($sec);
  }
}
