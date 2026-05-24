<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use LaravelEnso\Enums\Contracts\Select;
use LaravelEnso\Enums\Services\Enums as FrontendEnums;
use LaravelEnso\Localisation\Contracts\TranslatableAttributes;
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
            ->merge($this->modelKeys())
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
            })
            ->merge($this->exceptionKeys($content));
    }

    private function exceptionKeys(string $content): Collection
    {
        return Collection::wrap($this->exceptionPatterns($content))
            ->flatMap(function (string $pattern) use ($content): Collection {
                preg_match_all($pattern, $content, $matches);

                return Collection::wrap($matches[2] ?? [])
                    ->map(fn (string $key) => stripcslashes($key));
            });
    }

    private function exceptionPatterns(string $content): array
    {
        $patterns = [
            '/new\s+(?:\\\\?LaravelEnso\\\\Helpers\\\\Exceptions\\\\)?EnsoException\s*\(\s*([\'"])((?:\\\\.|(?!\1).)*?)\1\s*(?:,|\))/s',
        ];

        if (preg_match('/extends\s+(?:\\\\?LaravelEnso\\\\Helpers\\\\Exceptions\\\\)?EnsoException\b/', $content)) {
            $patterns[] = '/new\s+(?:self|static)\s*\(\s*([\'"])((?:\\\\.|(?!\1).)*?)\1\s*(?:,|\))/s';
        }

        return $patterns;
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
            ->reject(fn (array $match) => $this->hasLiteralFirstArgument($match[2]))
            ->map(fn (array $match) => [
                'file' => $file->getPathname(),
                'line' => $lineNumber,
                'call' => trim($match[0]),
            ]);
    }

    private function hasLiteralFirstArgument(string $arguments): bool
    {
        return preg_match('/^\s*([\'"])((?:\\\\.|(?!\1).)*?)\1\s*(?:,|$)/s', $arguments) === 1;
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

    private function modelKeys(): Collection
    {
        return Collection::wrap(Config::get('enso.localisation.scan.models', []))
            ->unique()
            ->flatMap(fn (string $model) => $this->translatableValues($model))
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->values();
    }

    private function translatableValues(string $model): Collection
    {
        $instance = App::make($model);

        if (! $instance instanceof Model || ! $instance instanceof TranslatableAttributes) {
            throw new InvalidArgumentException(
                "{$model} must be an Eloquent model implementing ".TranslatableAttributes::class
            );
        }

        return Collection::wrap($instance->translatableAttributes())
            ->filter(fn ($attribute) => is_string($attribute) && $attribute !== '')
            ->unique()
            ->flatMap(fn (string $attribute) => $this->attributeValues($instance, $attribute));
    }

    private function attributeValues(Model $model, string $attribute): Collection
    {
        if (! Schema::connection($model->getConnectionName())->hasColumn($model->getTable(), $attribute)) {
            throw new InvalidArgumentException(
                "{$attribute} is not a valid translatable attribute for ".get_class($model)
            );
        }

        return $model->newQuery()
            ->select($attribute)
            ->whereNotNull($attribute)
            ->distinct()
            ->pluck($attribute);
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
