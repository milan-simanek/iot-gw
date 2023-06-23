<?php

abstract class MsgSetTemperature extends MsgSet {
  const MAX=40*16;		// max value
//  const CLASS='';		// persistent class name
//  const SMTS='';		// MTS field in object of CLASS
//  const MCUCMD='?';		// MCU command
//  const DEV='c';
  
  public $value=0;
  function apply($O, $mts) {
    $val=intval($this->value*16.0);
    return $this->applyMCU($val);
  }
}