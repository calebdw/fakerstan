<?php

declare(strict_types=1);

namespace CalebDW\Fakerstan;

use Faker\Generator;
use RuntimeException;

class PsrContainerFakerProviderFactory
{
    public function __construct(
        private ?string $phpContainerPath = null,
        private ?string $setsVariable = null,
        private string $containerFakerId = Generator::class,
    ) {
    }

    public function create(): FakerProvider
    {
        if (is_null($this->phpContainerPath)) {
            throw new RuntimeException(self::class.' requires a value for the "fakerstan.psrProvider.phpContainerPath" parameter');
        }

        return new PsrContainerFakerProvider(
            $this->phpContainerPath,
            $this->setsVariable,
            $this->containerFakerId,
        );
    }
}
