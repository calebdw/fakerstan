<?php

declare(strict_types=1);

namespace CalebDW\Fakerstan\Tests\Fixtures\PsrContainerFakerProviderTest;

use Exception;
use Faker\Generator;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

class ContainerClass implements ContainerInterface
{
    public function get(string $id): mixed
    {
        return match ($id) {
            'generatorId' => new Generator(),
            'notGeneratorId' => new stdClass(),
            default => throw new class() extends Exception implements NotFoundExceptionInterface {
            },
        };
    }

    public function has(string $id): bool
    {
        return match ($id) {
            'generatorId', 'notGeneratorId' => true,
            default => false,
        };
    }
}
