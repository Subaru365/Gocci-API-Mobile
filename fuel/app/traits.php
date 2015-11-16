<?php

trait SingletonTrait
{
    private static $instance;
 
    private function __construct() { }
 
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
