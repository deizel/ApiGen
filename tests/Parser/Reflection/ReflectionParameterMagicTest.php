<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicParameterReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

class ReflectionParameterMagicTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    /**
     * @var MagicParameterReflectionInterface
     */
    private $reflectionParameterMagic;


    protected function setUp()
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
        $reflectionMethodMagic = $this->reflectionClass->getMagicMethods()['doAnOperation'];
        $this->reflectionParameterMagic = $reflectionMethodMagic->getParameters()['data'];
        $this->reflectionParameterMagicDefaultValue = $reflectionMethodMagic->getParameters()['type'];
    }


    public function testInstance()
    {
        $this->assertInstanceOf(MagicParameterReflectionInterface::class, $this->reflectionParameterMagic);
    }


    public function testGetName()
    {
        $this->assertSame('data', $this->reflectionParameterMagic->getName());
    }


    public function testGetTypeHint()
    {
        $this->assertSame('\stdClass', $this->reflectionParameterMagic->getTypeHint());
    }


    public function testGetFileName()
    {
        $this->assertStringEndsWith('ReflectionMethod.php', $this->reflectionParameterMagic->getFileName());
    }


    public function testIsTokenized()
    {
        $this->assertTrue($this->reflectionParameterMagic->isTokenized());
    }


    public function testGetPrettyName()
    {
        $this->assertSame(
            'Project\ReflectionMethod::doAnOperation($data)',
            $this->reflectionParameterMagic->getPrettyName()
        );
    }


    public function testGetDeclaringClass()
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionParameterMagic->getDeclaringClass());
    }


    public function testGetDeclaringClassName()
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionParameterMagic->getDeclaringClassName());
    }


    public function testGetDeclaringFunction()
    {
        $this->assertInstanceOf(
            MagicMethodReflectionInterface::class,
            $this->reflectionParameterMagic->getDeclaringFunction()
        );
    }


    public function testGetDeclaringFunctionName()
    {
        $this->assertSame('doAnOperation', $this->reflectionParameterMagic->getDeclaringFunctionName());
    }


    public function testStartLine()
    {
        $this->assertSame(16, $this->reflectionParameterMagic->getStartLine());
    }


    public function testEndLine()
    {
        $this->assertSame(16, $this->reflectionParameterMagic->getEndLine());
    }


    public function testGetDocComment()
    {
        $this->assertSame('', $this->reflectionParameterMagic->getDocComment());
    }


    public function testIsDefaultValueAvailable()
    {
        $this->assertFalse($this->reflectionParameterMagic->isDefaultValueAvailable());
        $this->assertTrue($this->reflectionParameterMagicDefaultValue->isDefaultValueAvailable());
    }


    public function testGetDefaultValueDefinition()
    {
        $this->assertSame('default', $this->reflectionParameterMagicDefaultValue->getDefaultValueDefinition());
    }


    public function testGetPosition()
    {
        $this->assertSame(0, $this->reflectionParameterMagic->getPosition());
    }


    public function testIsArray()
    {
        $this->assertFalse($this->reflectionParameterMagic->isArray());
    }


    public function testIsCallable()
    {
        $this->assertFalse($this->reflectionParameterMagic->isCallable());
    }


    public function testGetClass()
    {
        $this->assertNull($this->reflectionParameterMagic->getClass());
    }


    public function testGetClassName()
    {
        $this->assertNull($this->reflectionParameterMagic->getClassName());
    }


    public function testAllowsNull()
    {
        $this->assertFalse($this->reflectionParameterMagic->allowsNull());
    }


    public function testIsOptional()
    {
        $this->assertFalse($this->reflectionParameterMagic->isOptional());
    }


    public function testIsPassedByReference()
    {
        $this->assertFalse($this->reflectionParameterMagic->isPassedByReference());
    }


    public function testCanBePassedByValue()
    {
        $this->assertFalse($this->reflectionParameterMagic->canBePassedByValue());
    }


    public function testIsUnlimited()
    {
        $this->assertFalse($this->reflectionParameterMagic->isUnlimited());
    }


    /**
     * @return Mockery\MockInterface
     */
    private function getReflectionFactory()
    {
        $parserStorageMock = Mockery::mock(ParserStorageInterface::class);
        $parserStorageMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
            if ($arg) {
                return ['Project\ReflectionMethod' => $this->reflectionClass];
            }
        });
        $configurationMock = Mockery::mock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
            'isInternalDocumented' => false,
            'isPhpCoreDocumented' => true
        ]);
        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}
