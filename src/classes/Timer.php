<?php


class Timer
{
    private $time = null;
    public function __construct() {
        $this->time = time();
        error_log('Timer set');
    }

    public function __destruct() {
        error_log( 'Timer finished after ' . (time()-$this->time).' seconds.');
    }
}