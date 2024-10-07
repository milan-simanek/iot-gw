<?php

abstract class MsgSetInteger extends MsgSet {
  const MAX=65535;		// max value
//  const DATACLASS='';		// persistent class name
//  const SMTS='';		// MTS field in object of DATACLASS
//  const MCUCMD='?';		// MCU command
//  const DEV='c';		// RS-link device address

  public $value=0;  
  function apply($O, $mts) {
    $v=intval($this->value);
    if ($v>static::MAX) return FALSE;
    if ($v<0) return FALSE;
    return $this->applyMCU($v);
  }
}
