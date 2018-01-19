<?php
/**
 * Created by PhpStorm.
 * User: DrekTop
 * Date: 19/01/2018
 * Time: 10:52 AM
 */

namespace Josebefree\Container\Fecades;
use Dreksoft\Container;
use Exception;

abstract class Fecade
{
    protected static $container;

    public static function setContainer(Container $container) {
        static::$container = $container;
    }

    public static function getContainer() {
        return static::$container;
    }

    public static function getAccessor() {
        throw new Exception('Please defined a getAccessor method');
    }

    public static function getInstance() {
        return static::getContainer()->make(static::getAccessor());
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([static::getInstance(), $method], $args);
    }
}