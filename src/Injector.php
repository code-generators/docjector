<?php
namespace hisorange\Docjector;

// PHP SPL.
use Reflector;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

// Package related components.
use hisorange\Docjector\Contracts\InjectionInterface;

class Injector
{
    /**
     * Reflection.
     *
     * @var \Reflector
     */
    protected $reflector;

    /**
     * Injection.
     *
     * @var \hisorange\Docjector\Contracts\InjectionInterface
     */
    protected $injection;

    /**
     * Initiaze the injector.
     *
     * @throws \hisorange\Docjector\Exceptions\InvalidArgumentException
     *
     * @param Reflector          $reflector
     * @param InjectionInterface $injection
     */
    public function __construct(Reflector $reflector, InjectionInterface $injection)
    {
        $this->setReflection($reflector);
        $this->injection  = $injection;
    }

    /**
     * Validate the reflector for doc comment accessibility.
     *
     * @throws \hisorange\Docjector\Exceptions\ReflectionException
     *
     * @param  \Reflector $reflector
     * @return void
     */
    public function setReflection(Reflector $reflector)
    {
        if ( ! method_exists($reflector, 'getDocComment')) {
            throw new Exceptions\ReflectionException(sprintf('Reflector object "%s" is not supported, getDocComment method is not defined.', $reflector->getName()));
        }

        $this->reflector = $reflector;
    }

    /**
     * @return \Reflector
     */
    public function getReflection()
    {
        return $this->reflector;
    }

    /**
     * Get the local IO path for the reflector's class file.
     *
     * @throws \hisorange\Docjector\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    protected function resolvePath()
    {
        if ($this->reflector instanceof ReflectionClass) {
            if ($this->reflector->getFilename() === false) {
                throw new Exceptions\InvalidArgumentException(sprintf('ReflectionClass\'s subject "%s" is a PHP core class, cannot modify the source.', $reflector->getName()));
            }

            return $this->reflector->getFilename();
        }
    }

    /**
     * Load the class source into memory.
     *
     * @throws \hisorange\Docjector\Exceptions\SourceReaderException
     *
     * @param  string $path Path to the class file.
     * @return string
     */
    protected function readSource($path)
    {
        if (file_exists($path)) {
            if (is_file($path)) {
                if (is_readable($path)) {
                    $source = file_get_contents($path);
                } else {
                    throw new Exceptions\SourceReaderException(sprintf('Class file "%s" is not readable.', $path));
                }
            } else {
                throw new Exceptions\SourceReaderException(sprintf('Class file "%s" is not a file.', $path));
            }
        } else {
            throw new Exceptions\SourceReaderException(sprintf('Class file "%s" does not exists.', $path));
        }

        return $source;
    }

    /**
     * Get the doc comment string from the reflector.
     *
     * @return string
     */
    protected function getDocComment()
    {
        return $this->reflector->getDocComment();
    }

    /**
     * Find the needle in the haystack, but also get the last byte too.
     *
     * @param  string $haystack
     * @param  string $needle
     * @return array
     */
    protected function find($haystack, $needle)
    {
        $firstByte = strpos($haystack, $needle);
        $lastByte  = $firstByte + strlen($needle);

        return [$firstByte, $lastByte];
    }

    /**
     * Get the byte pointer where the doc comment starts.
     *
     * @param  string $source
     * @param  string $comment
     * @return int
     */
    protected function cutout($source, $firstByte, $lastByte)
    {
        return substr($source, 0, $firstByte) . substr($source, $lastByte);
    }

    /**
     * Write the content into the original source file.
     *
     * @param  boolean $dry Indicate a dry run, no changes are saved to disk.
     * @return void
     */
    public function execute($dry = false)
    {
        $path      = $this->resolvePath();
        $source    = $this->readSource($path);
        $comment   = $this->getDocComment();

        list($firstByte, $lastByte) = $this->find($source, $comment);

        $source    = $this->cutout($source, $firstByte, $lastByte);
    }
}
