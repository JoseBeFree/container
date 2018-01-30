<?php
/**
 * Created by PhpStorm.
 * User: DrekTop
 * Date: 19/01/2018
 * Time: 12:15 PM
 */

namespace Josebefree\Container;
use Josebefree\Container\Container;

class Aplication
{

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function registerProviders(array $providers) {

        foreach ($providers as $provider) {
            $start = new $provider($this->container);
            $start->register();
        }

    }


}