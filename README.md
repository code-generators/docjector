# WORK IN PROGRESS PACKAGE!!!
***
Please note, this package is only in and early WIP stage, only uploaded as a draft.

# Docjector - PHP DocComment Injection!
***
This package is used to inject your custom doc comments into existing source code. Where to use it? When you wana edit or modify source code with annotations or custom generated documentations.

### Installation

With composer:

```sh
composer require hisorange/docjector
```

Or with git:

```sh
git clone https://github.com/hisorange/docjector.git
```

### How to use

Simply create a reflection of your subjected class, method or property:

```php
$reflection = new ReflectionClass(AppBundle\Entity\Spaceship::class);

$injection  = new Injection(['@test']);
$injector   = new Injector($reflection, $injection);

try {
    $injector->execute();
} catch (hisorange\Docjector\Exceptions\Exception $e) {
    print $e->getMessage();
}
```

Or with methods:

```php
$class      = new ReflectionClass(AppBundle\Entity\Spaceship::class);
$reflection = $class->getMethod('findAll');

$injection  = new Injection(['@ORM\Column(...)']);
$injector   = new Injector($reflection, $injection);

try {
    $injector->execute();
} catch (hisorange\Docjector\Exceptions\Exception $e) {
    print $e->getMessage();
}
```

Or with properties:

```php
$class      = new ReflectionClass(AppBundle\Entity\Spaceship::class);
$reflection = $class->getProperty('manufacturer');

$injection  = new Injection(['@deprecated("Property will be removed on version X")']);
$injector   = new Injector($reflection, $injection);

try {
    $injector->execute();
} catch (hisorange\Docjector\Exceptions\Exception $e) {
    print $e->getMessage();
}
```
