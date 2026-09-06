<?php

declare(strict_types=1);

namespace Differ\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private function fixture(string $name): string
    {
        return __DIR__ . '/fixtures/' . $name;
    }

    private function expected(string $name): string
    {
        return trim((string) file_get_contents($this->fixture($name)));
    }

    // The "tests and CI" step asks for tests on flat json files specifically:
    // comparison starts there, and key ordering breaks there first.
    public function testFlatJson(): void
    {
        $actual = genDiff($this->fixture('flat1.json'), $this->fixture('flat2.json'));

        $this->assertSame($this->expected('flat.stylish'), $actual);
    }

    public function testFlatJsonKeysAreSortedAlphabetically(): void
    {
        $actual = genDiff($this->fixture('flat1.json'), $this->fixture('flat2.json'));

        $keys = [];
        foreach (explode("\n", $actual) as $line) {
            if (preg_match('/^\s*[+-]?\s*(\w+):/', $line, $matches) === 1) {
                $keys[] = $matches[1];
            }
        }

        $sorted = $keys;
        sort($sorted);

        $this->assertSame($sorted, $keys);
    }

    public function testFlatJsonAgainstItselfHasNoChanges(): void
    {
        $actual = genDiff($this->fixture('flat1.json'), $this->fixture('flat1.json'));

        $this->assertStringNotContainsString('- ', $actual);
        $this->assertStringNotContainsString('+ ', $actual);
    }

    /**
     * @return list<array{0: string, 1: string, 2: string}>
     */
    public static function nestedProvider(): array
    {
        return [
            'json, stylish' => ['file1.json', 'file2.json', 'stylish'],
            'yaml, stylish' => ['file1.yaml', 'file2.yaml', 'stylish'],
            'json, plain' => ['file1.json', 'file2.json', 'plain'],
            'yaml, plain' => ['file1.yaml', 'file2.yaml', 'plain'],
            'json, json' => ['file1.json', 'file2.json', 'json'],
            'yaml, json' => ['file1.yaml', 'file2.yaml', 'json'],
        ];
    }

    #[DataProvider('nestedProvider')]
    public function testNested(string $first, string $second, string $format): void
    {
        $actual = genDiff($this->fixture($first), $this->fixture($second), $format);

        // For the json format the structure matters, not the text: indentation
        // decides nothing, so compare the decoded data.
        if ($format === 'json') {
            $this->assertEquals(
                json_decode($this->expected('diff.json'), true, 512, JSON_THROW_ON_ERROR),
                json_decode($actual, true, 512, JSON_THROW_ON_ERROR),
            );

            return;
        }

        $this->assertSame($this->expected('diff.' . $format), $actual);
    }

    public function testDefaultFormatIsStylish(): void
    {
        $withoutFormat = genDiff($this->fixture('file1.json'), $this->fixture('file2.json'));
        $withStylish = genDiff($this->fixture('file1.json'), $this->fixture('file2.json'), 'stylish');

        $this->assertSame($withStylish, $withoutFormat);
    }
}
