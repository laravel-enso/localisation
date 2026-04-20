<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class AuditDuplicates
{
    private const EntryPattern = '/"((?:\\\\.|[^"\\\\])*)"\s*:\s*(null|"(?:\\\\.|[^"\\\\])*")/u';

    public function __construct(
        private readonly array $locales,
        private readonly bool $deduplicate = false
    ) {
    }

    public function handle(): array
    {
        $same = new Collection();
        $conflicting = new Collection();
        $deduplicated = 0;

        Collection::wrap($this->locales)
            ->reject(fn (string $locale) => $locale === 'en')
            ->each(function (string $locale) use (&$same, &$conflicting, &$deduplicated): void {
                [
                    'same' => $fileSame,
                    'conflicting' => $fileConflicting,
                    'deduplicated' => $fileDeduplicated
                ] = $this->auditLocale($locale);

                $same = $same->merge($fileSame);
                $conflicting = $conflicting->merge($fileConflicting);
                $deduplicated += $fileDeduplicated;
            });

        return compact('same', 'conflicting', 'deduplicated');
    }

    private function auditLocale(string $locale): array
    {
        $path = App::langPath("{$locale}.json");

        if (! File::exists($path)) {
            return [
                'same' => Collection::empty(),
                'conflicting' => Collection::empty(),
                'deduplicated' => 0,
            ];
        }

        $entries = $this->entries($path);

        $duplicates = $entries->groupBy('key')
            ->filter(fn (Collection $group) => $group->count() > 1);

        $same = $duplicates->filter(fn (Collection $group) => $group
            ->pluck('valueSignature')->unique()->count() === 1);

        $conflicting = $duplicates->reject(fn (Collection $group) => $group
            ->pluck('valueSignature')->unique()->count() === 1);

        if ($this->deduplicate && $same->isNotEmpty()) {
            $this->saveDeduplicated($path, $entries, $same->keys());
        }

        return [
            'same' => $same->map(fn (Collection $group, string $key) => [
                'locale' => $locale,
                'file' => $path,
                'key' => $key,
                'translation' => $group->first()['valueLabel'],
                'duplicates' => $group->count(),
            ])->values(),
            'conflicting' => $conflicting->map(fn (Collection $group, string $key) => [
                'locale' => $locale,
                'file' => $path,
                'key' => $key,
                'translations' => $group->pluck('valueLabel')->unique()->implode(' | '),
                'duplicates' => $group->count(),
            ])->values(),
            'deduplicated' => $same->count(),
        ];
    }

    private function entries(string $path): Collection
    {
        preg_match_all(self::EntryPattern, File::get($path), $matches, PREG_SET_ORDER);

        return Collection::wrap($matches)
            ->map(fn (array $match) => $this->entry($match));
    }

    private function entry(array $match): array
    {
        $key = json_decode("\"{$match[1]}\"", true, 512, JSON_THROW_ON_ERROR);

        $value = $match[2] === 'null'
            ? null
            : json_decode($match[2], true, 512, JSON_THROW_ON_ERROR);

        return [
            'key' => $key,
            'value' => $value,
            'valueLabel' => $value ?? 'null',
            'valueSignature' => $match[2],
        ];
    }

    private function saveDeduplicated(string $path, Collection $entries, Collection $sameKeys): void
    {
        $seen = [];

        $deduplicated = $entries->reject(function (array $entry) use (&$seen, $sameKeys): bool {
            if (! $sameKeys->contains($entry['key'])) {
                return false;
            }

            $seen[$entry['key']] = ($seen[$entry['key']] ?? 0) + 1;

            return $seen[$entry['key']] > 1;
        })->values();

        File::put($path, $this->toJson($deduplicated));
    }

    private function toJson(Collection $entries): string
    {
        if ($entries->isEmpty()) {
            return '{}';
        }

        return sprintf("{\n%s\n}", $entries
            ->map(function (array $entry): string {
                $key = json_encode($entry['key'], JSON_UNESCAPED_UNICODE);
                $value = $entry['value'] === null
                    ? 'null'
                    : json_encode($entry['value'], JSON_UNESCAPED_UNICODE);

                return "    {$key}: {$value}";
            })->implode(",\n"));
    }
}
