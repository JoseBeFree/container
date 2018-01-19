<?php
/**
 * Created by PhpStorm.
 * User: DrekTop
 * Date: 19/01/2018
 * Time: 12:09 PM
 */

namespace Josebefree\Container\Provider;


use Josebefree\Container\Container;

abstract class Provider
{

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    abstract public function register();

}