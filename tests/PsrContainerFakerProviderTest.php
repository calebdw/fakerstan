<?php

declare(strict_types=1);

namespace CalebDW\Fakerstan\Tests;

use CalebDW\Fakerstan\PsrContainerFakerProvider;
use Faker\Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(PsrContainerFakerProvider::class)]
class PsrContainerFakerProviderTest extends TestCase
{
    #[Test]
    public function itUsesContainerReturnedFromFile()
    {
        $containerFilename = __DIR__.'/Fixtures/PsrContainerFakerProviderTest/ReturningContainerFile.php';
        $sut = new PsrContainerFakerProvider($containerFilename, null, 'generatorId');
        $faker = $sut->getFaker();

        $this->assertInstanceOf(Generator::class, $faker);
    }

    #[Test]
    public function itUsesContainerSetInVariable()
    {
        $containerFilename = __DIR__.'/Fixtures/PsrContainerFakerProviderTest/VariableSettingContainerFile.php';
        $sut = new PsrContainerFakerProvider($containerFilename, 'containerVariable', 'generatorId');
        $faker = $sut->getFaker();

        $this->assertInstanceOf(Generator::class, $faker);
    }

    #[Test]
    public function itThrowsExceptionWhenUnableToReadContainerFile()
    {
        $this->expectException(RuntimeException::class);

        $containerFilename = __DIR__.'/a-file-that-does-not-exist';
        $sut = new PsrContainerFakerProvider($containerFilename, null, 'generatorId');
        $sut->getFaker();
    }

    #[Test]
    public function itThrowsExceptionWhenTheContainerFileIsExpectedToReturnContainerButDoesNot()
    {
        $this->expectException(RuntimeException::class);

        $containerFilename = __DIR__.'/Fixtures/PsrContainerFakerProviderTest/EmptyContainerFile.php';
        $sut = new PsrContainerFakerProvider($containerFilename, null, 'generatorId');
        $sut->getFaker();
    }

    #[Test]
    public function itThrowsExceptionWhenTheContainerFileIsExpectedToSetAVariableButDoesNot()
    {
        $this->expectException(RuntimeException::class);

        $containerFilename = __DIR__.'/Fixtures/PsrContainerFakerProviderTest/EmptyContainerFile.php';
        $sut = new PsrContainerFakerProvider($containerFilename, 'containerVariable', 'generatorId');
        $sut->getFaker();
    }

    #[Test]
    public function itThrowsExceptionWhenTheDeterminedContainerIsNotActuallyAContainer()
    {
        $this->expectException(RuntimeException::class);

        $containerFilename = __DIR__.'/Fixtures/PsrContainerFakerProviderTest/VariableSettingContainerFile.php';
        $sut = new PsrContainerFakerProvider($containerFilename, 'nonContainerVariable', 'generatorId');
        $sut->getFaker();
    }

    #[Test]
    public function itThrowsExceptionWhenTheContainerDoesNotHaveServiceWithId()
    {
        $this->expectException(RuntimeException::class);

        $containerFilename = __DIR__.'/Fixtures/PsrContainerFakerProviderTest/ReturningContainerFile.php';
        $sut = new PsrContainerFakerProvider($containerFilename, null, 'id-not-in-container');
        $sut->getFaker();
    }

    #[Test]
    public function itThrowsExceptionWhenTheNamedServiceIsNotAGenerator()
    {
        $this->expectException(RuntimeException::class);

        $containerFilename = __DIR__.'/Fixtures/PsrContainerFakerProviderTest/ReturningContainerFile.php';
        $sut = new PsrContainerFakerProvider($containerFilename, null, 'notGeneratorId');
        $sut->getFaker();
    }
}
