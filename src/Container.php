<?php
/**
 * Created by PhpStorm.
 * User: DrekTop
 * Date: 18/01/2018
 * Time: 10:39 AM
 */

namespace Josebefree\Container;
use Closure;
use MongoDB\Driver\Exception\ConnectionException;
use ReflectionClass;
use ReflectionException;

class Container
{

    protected $bindings = [];
    protected $shared = [];
    protected static $instance;

    public static function getInstance() {
        if(static::$instance == null) {
            static::$instance = new Container();
        }
        return static::$instance;
    }

    public static function setInstance(Container $container) {
        static::$instance = $container;
    }

    public function bind($name, $resolver, $shared = false) {

        $this->bindings[$name] = [
            'resolver' => $resolver,
            'shared' => $shared
        ];

    }

    public function make($name, array $args = []) {
        if(isset($this->shared[$name])) {
            return $this->shared[$name];
        }

        if(isset($this->bindings[$name]['resolver'])) {
            $resolver = $this->bindings[$name]['resolver'];
            $shared = $this->bindings[$name]['shared'];
        }
        else {
            $resolver = $name;
            $shared = false;
        }

        if($resolver instanceof Closure) {
            $object = $resolver($this);
        }
        else {
            $object = $this->build($resolver, $args);
        }

        if($shared) {
            $this->shared[$name] = $object;
        }

        return $object;
    }

    public function instance($name, $object) {
        $this->shared[$name] = $object;
    }

    public function singleton($name, $resolver) {
        $this->bind($name, $resolver, true);
    }

    public function build($name, array $args = []) {
        try {
            $reflection = new ReflectionClass($name);
        } catch (ReflectionException $e) {
            throw new ContainerException('Class ['.$name.'] do not exist');
        }

        if(!$reflection->isInstantiable()) {
            throw new ContainerException($name.' no es instantiable');
        }

        $constructor = $reflection->getConstructor();
        if(is_null($constructor)) {
            return new $name;
        }

        $constructorParms = $constructor->getParameters();
        $dependencias = [];
        foreach($constructorParms as $constructorParm) {

            $ParmName = $constructorParm->getName();

            if(isset($args[$ParmName])) {
                $dependencias[] = $args[$ParmName];
                continue;
            }

            if($constructorParm->isDefaultValueAvailable()) {
                $dependencias[] = $constructorParm->getDefaultValue();
                continue;
            }

            try {
                $parmClass = $constructorParm->getClass();
            } catch (ReflectionException $e) {
                throw new ContainerException('Unable to Build ['.$name.']: '. $e->getMessage());
            }

            if(!is_null($parmClass)) {
                $ParmClassName = $parmClass->getName();
                $dependencias[] = $this->build($ParmClassName);
            }
            else {
                throw new ContainerException('Please Write te Parm '.$ParmName.' in class ['.$name.']');
            }


        }

        return $reflection->newInstanceArgs($dependencias);
    }


}