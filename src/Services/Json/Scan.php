<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Scan
{
    public function handle(): array
    {
        $found = Collection::wrap($this->paths())
            ->flatMap(fn (array $path) => $this->scanPath($path))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $ignored = Collection::wrap($this->paths())
            ->flatMap(fn (array $path) => $this->ignoredFromPath($path))
            ->unique(fn (array $ignored) => "{$ignored['file']}:{$ignored['line']}:{$ignored['call']}")
            ->values();

        return [
            'found' => $found,
            'ignored' => $ignored,
        ];
    }

    private function scanPath(array $path): Collection
    {
        if (! File::isDirectory($path['path'])) {
            return Collection::empty();
        }

        return Collection::wrap(iterator_to_array($this->finder($path)))
            ->flatMap(fn (SplFileInfo $file) => $this->extractKeys($file));
    }

    private function ignoredFromPath(array $path): Collection
    {
        if (! File::isDirectory($path['path'])) {
            return Collection::empty();
        }

        return Collection::wrap(iterator_to_array($this->finder($path)))
            ->flatMap(fn (SplFileInfo $file) => $this->extractIgnored($file));
    }

    private function finder(array $path): Finder
    {
        $finder = Finder::create()
            ->files()
            ->ignoreUnreadableDirs()
            ->in($path['path'])
            ->name($this->files());

        Collection::wrap($path['exclude'] ?? [])
            ->each(fn (string $folder) => $finder->exclude($folder));

        return $finder;
    }

    private function extractKeys(SplFileInfo $file): Collection
    {
        $content = File::get($file->getPathname());

        return Collection::wrap($this->patterns())
            ->flatMap(function (string $pattern) use ($content): Collection {
                preg_match_all($pattern, $content, $matches);

                return Collection::wrap($matches[2] ?? [])
                    ->map(fn (string $key) => stripcslashes($key));
            });
    }

    private function extractIgnored(SplFileInfo $file): Collection
    {
        return Collection::wrap(explode(PHP_EOL, File::get($file->getPathname())))
            ->flatMap(fn (string $line, int $index) => $this->lineIgnored($file, $line, $index + 1));
    }

    private function lineIgnored(SplFileInfo $file, string $line, int $lineNumber): Collection
    {
        preg_match_all('/(@lang|__|trans|i18n)\(\s*(.*?)\)/', $line, $matches, PREG_SET_ORDER);

        return Collection::wrap($matches)
            ->reject(fn (array $match) => preg_match('/^\s*([\'"])((?:\\\\.|(?!\1).)*?)\1\s*$/s', $match[2]) === 1)
            ->map(fn (array $match) => [
                'file' => $file->getPathname(),
                'line' => $lineNumber,
                'call' => trim($match[0]),
            ]);
    }

    private function paths(): array
    {
        return Config::get('enso.localisation.scan.paths');
    }

    private function files(): array
    {
        return Config::get('enso.localisation.scan.files');
    }

    private function patterns(): array
    {
        return Config::get('enso.localisation.scan.patterns');
    }
}
