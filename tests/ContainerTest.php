<?php


use Josebefree\Container\Container;
use Josebefree\Container\ContainerException;

class ContainerTest extends PHPUnit\Framework\TestCase {

    public function test_makefunction() {
        $container = new Container();
        $container->bind('key', function () {
            return 'Object';
        });

        $this->assertSame('Object', $container->make('key'));
    }

    public function test_instance() {

        $container = new Container();
        $stdClass = new stdClass();
        $container->instance('key', $stdClass);
        $this->assertSame($stdClass, $container->make('key'));

    }

    public function test_bind_instance() {

        $container = new Container();
        $container->bind('key', 'StdClass');
        $this->assertInstanceOf('StdClass', $container->make('key'));

    }

    public function test_bind_auto_resolver() {

        $container = new Container();
        $container->bind('key', 'Foo');
        $this->assertInstanceOf('Foo', $container->make('key'));
    }

    public function test_esperar_exception() {

        $this->expectException(
            ContainerException::class
        );

        $container = new Container();
        $container->bind('qux', 'Qux');
        $this->assertInstanceOf('Qux', $container->make('qux'));

    }

    public function test_bindwithout_class() {

        $this->expectException(
          ContainerException::class
        );

        $container = new Container();
        $container->make('norf');

    }

    public function test_bind_arguments() {

        $container = new Container();

        $this->assertInstanceOf('MailDummy', $container->make('MailDummy', ['url' => 'dreksoft.com', 'key' => 'secret']));

    }

    public function test_with_default_vars() {
        $container = new Container();
        $this->assertInstanceOf('MailDummy', $container->make('MailDummy', ['url' => 'dreksoft.com']));
    }

    public function test_singleton() {

        $container = new Container();
        $container->singleton('foo', 'Foo');
        $this->assertSame(
            $container->make('foo'),
            $container->make('foo')
        );

    }

}

class MailDummy {

    protected $url;
    protected $key;

    public function __construct($url, $key = 'secret')
    {
        $this->url = $url;
        $this->key = $key;
    }
}

class Foo {
    public function __construct(Bar $bar, Baz $baz)
    {

    }
}

class Bar {
    public function __construct(FooBar $foobar)
    {
    }
}
class FooBar {

}

class Baz {

}

class Qux {
    public function __construct(Norf $norf)
    {
    }
}