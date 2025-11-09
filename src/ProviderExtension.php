<?php

declare(strict_types=1);

namespace CalebDW\Fakerstan;

use Faker\Generator;
use Faker\Provider\Base;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;

final class ProviderExtension implements MethodsClassReflectionExtension
{
    /** @var array<string, MethodReflection> */
    private array $methods = [];

    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private FakerProvider $fakerProvider,
    ) {
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (! $classReflection->is(Generator::class)) {
            return false;
        }

        $key = $this->getKey($classReflection, $methodName);

        if (isset($this->methods[$key])) {
            return true;
        }

        /** @var list<Base|object> $providers */
        $providers = $classReflection->getNativeReflection()
            ->getProperty('providers')
            ->getValue($this->fakerProvider->getFaker());

        foreach ($providers as $provider) {
            if (! method_exists($provider, $methodName)) {
                continue;
            }

            if (! $this->reflectionProvider->hasClass($provider::class)) {
                continue;
            }

            $methodReflection = $this->reflectionProvider->getClass($provider::class)
                ->getMethod($methodName, new OutOfClassScope());

            if (! $methodReflection->isPublic()) {
                continue;
            }

            $this->methods[$key] = FakerMethodReflection::fromReflection($methodReflection);

            return true;
        }

        return false;
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return $this->methods[$this->getKey($classReflection, $methodName)];
    }

    private function getKey(ClassReflection $classReflection, string $methodName): string
    {
        return $classReflection->getName().'::'.$methodName;
    }
}
