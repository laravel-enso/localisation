<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelEnso\Enums\Contracts\Select;
use LaravelEnso\Enums\Services\Enums as FrontendEnums;
use ReflectionEnum;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Scan
{
    public function handle(): array
    {
        $found = Collection::wrap($this->paths())
            ->flatMap(fn (array $path) => $this->scanPath($path))
            ->merge($this->enumKeys())
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

    private function enumKeys(): Collection
    {
        if (! Config::get('enso.localisation.scan.enums', true)) {
            return Collection::empty();
        }

        return Collection::wrap($this->legacyEnums())
            ->merge($this->nativeEnums())
            ->merge($this->selectEnumKeys())
            ->flatMap(fn ($enum) => Collection::wrap($enum)->values())
            ->filter(fn ($value) => is_string($value) && $value !== '')
            ->values();
    }

    private function legacyEnums(): array
    {
        return App::bound('legacyEnums')
            ? App::make('legacyEnums')->all()
            : [];
    }

    private function nativeEnums(): array
    {
        return class_exists(FrontendEnums::class)
            ? (new FrontendEnums())->handle()
            : [];
    }

    private function selectEnumKeys(): Collection
    {
        if (! interface_exists(Select::class)) {
            return Collection::empty();
        }

        return $this->enumSources()
            ->flatMap(fn (string $source) => $this->selectEnums($source))
            ->unique()
            ->flatMap(fn (string $enum) => Collection::wrap($enum::select())->pluck('name'));
    }

    private function enumSources(): Collection
    {
        return Collection::wrap(Config::get('enso.enums.vendors', ['laravel-enso']))
            ->map(fn (string $vendor) => base_path("vendor/{$vendor}"))
            ->filter(fn (string $vendor) => File::isDirectory($vendor))
            ->flatMap(fn (string $vendor) => File::directories($vendor))
            ->push(base_path());
    }

    private function selectEnums(string $source): Collection
    {
        if (! File::isFile("{$source}/composer.json")) {
            return Collection::empty();
        }

        return Collection::wrap($this->psr4($source))
            ->flatMap(fn (string|array $folders, string $namespace) => Collection::wrap($folders)
                ->flatMap(fn (string $folder) => $this->selectEnumsFromPsr4($source, $namespace, $folder)));
    }

    private function selectEnumsFromPsr4(string $source, string $namespace, string $folder): Collection
    {
        $path = Collection::wrap([
            $source,
            rtrim($folder, DIRECTORY_SEPARATOR),
            'Enums',
        ])->implode(DIRECTORY_SEPARATOR);

        if (! File::isDirectory($path)) {
            return Collection::empty();
        }

        return Collection::wrap(File::allFiles($path))
            ->map(fn (SplFileInfo $file) => $this->enumClass($file, $namespace))
            ->filter(fn (string $class) => $this->isSelectEnum($class));
    }

    private function enumClass(SplFileInfo $file, string $namespace): string
    {
        return Collection::wrap([
            rtrim($namespace, '\\'),
            'Enums',
            ...explode('/', $file->getRelativePath()),
            $file->getFilenameWithoutExtension(),
        ])->filter()->implode('\\');
    }

    private function isSelectEnum(string $class): bool
    {
        return enum_exists($class)
            && (new ReflectionEnum($class))->implementsInterface(Select::class);
    }

    private function psr4(string $source): array
    {
        return json_decode(File::get("{$source}/composer.json"), true)['autoload']['psr-4'] ?? [];
    }
}
