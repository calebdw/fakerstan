<?php

declare(strict_types=1);

namespace CalebDW\Fakerstan;

use Faker\Generator;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class PsrContainerFakerProvider implements FakerProvider
{
    private ?Generator $generatorFromContainer = null;

    public function __construct(
        private string $phpContainerPath,
        private ?string $setsVariable,
        private string $containerFakerId,
    ) {
    }

    public function getFaker(): Generator
    {
        if (is_null($this->generatorFromContainer)) {
            $this->generatorFromContainer = $this->getGeneratorFromContainer();
        }

        return $this->generatorFromContainer;
    }

    private function getGeneratorFromContainer(): Generator
    {
        if (! is_readable($this->phpContainerPath)) {
            throw new RuntimeException('Could not read container PHP file');
        }

        // Include the container file and store the return value, just in case.
        $maybeContainer = require_once $this->phpContainerPath;

        // If the code in that file returns nothing, the require_once will return 1.
        if (is_null($this->setsVariable) && ($maybeContainer === 1)) {
            throw new RuntimeException('Container file was expected to return the container, but it returned nothing');
        }

        // If we didn't expect it to be returned, check the variable that we said it would be in.
        if (is_string($this->setsVariable)) {
            $definedVariables = get_defined_vars();
            if (! array_key_exists($this->setsVariable, $definedVariables)) {
                throw new RuntimeException('Container file does not set variable '.$this->setsVariable);
            }

            $maybeContainer = $definedVariables[$this->setsVariable];
        }

        // Check that we got something that looks like a container.
        if (! $maybeContainer instanceof ContainerInterface) {
            throw new RuntimeException('Retrieved container is not a '.ContainerInterface::class);
        }

        // Does the container have something with the expected Faker Generator ID?
        if (! $maybeContainer->has($this->containerFakerId)) {
            throw new RuntimeException('Container does not have entry with ID '.$this->containerFakerId);
        }

        // Make sure that we retrieved an actual Generator.
        $containerFaker = $maybeContainer->get($this->containerFakerId);
        if (! $containerFaker instanceof Generator) {
            throw new RuntimeException('Container entry with ID '.$this->containerFakerId.' is not a '.Generator::class);
        }

        return $containerFaker;
    }
}
