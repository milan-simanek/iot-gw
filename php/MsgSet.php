<?php

abstract class MsgSet extends MsgInput {
  const CLASS='';	// persistent class name
  const SMTS='';	// MTS field in object of CLASS
  const MCUCMD='?';	// MCU command
  const DEV=NULL;	// RS-link device address
  
  abstract function apply($O, $mts);
  function applyMCU($text) {
    if (static::DEV) (new IotDev(static::DEV))->send(static::MCUCMD.$data);
    return TRUE;
  }
  function execute($mts) {
    $class=static::CLASS;
    $O=$class::LoadInstance();
    if ($O->{static::SMTS}>=$mts) return TRUE;
    if ($this->apply($O, $mts)!==TRUE) return FALSE;
    $O->{static::SMTS}=$mts;
    return TRUE;
  }
}
