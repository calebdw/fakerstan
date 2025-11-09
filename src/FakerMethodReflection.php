<?php

declare(strict_types=1);

namespace CalebDW\Fakerstan;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;

final class FakerMethodReflection implements MethodReflection
{
    public function __construct(
        private string $name,
        private ?string $docComment,
        private ClassReflection $declaringClass,
        private ClassMemberReflection $prototype,
        /** @var list<ParametersAcceptor> */
        private array $variants,
        private TrinaryLogic $isDeprecated,
        private ?string $deprecatedDescription,
        private TrinaryLogic $isFinal,
        private TrinaryLogic $isInternal,
        private ?Type $throwType,
        private TrinaryLogic $hasSideEffects,
    ) {
    }

    public static function fromReflection(MethodReflection $methodReflection): self
    {
        return new self(
            $methodReflection->getName(),
            $methodReflection->getDocComment(),
            $methodReflection->getDeclaringClass(),
            $methodReflection->getPrototype(),
            $methodReflection->getVariants(),
            $methodReflection->isDeprecated(),
            $methodReflection->getDeprecatedDescription(),
            $methodReflection->isFinal(),
            $methodReflection->isInternal(),
            $methodReflection->getThrowType(),
            $methodReflection->hasSideEffects(),
        );
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClass;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getDocComment(): ?string
    {
        return $this->docComment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this->prototype;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

    public function isDeprecated(): TrinaryLogic
    {
        return $this->isDeprecated;
    }

    public function getDeprecatedDescription(): ?string
    {
        return $this->deprecatedDescription;
    }

    public function isFinal(): TrinaryLogic
    {
        return $this->isFinal;
    }

    public function isInternal(): TrinaryLogic
    {
        return $this->isInternal;
    }

    public function getThrowType(): ?Type
    {
        return $this->throwType;
    }

    public function hasSideEffects(): TrinaryLogic
    {
        return $this->hasSideEffects;
    }
}
