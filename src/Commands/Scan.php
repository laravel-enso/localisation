<?php

namespace LaravelEnso\Localisation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use LaravelEnso\Helpers\Services\JsonReader;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Json\AddKey;
use LaravelEnso\Localisation\Services\Json\AuditDuplicates;
use LaravelEnso\Localisation\Services\Json\Scan as Service;

class Scan extends Command
{
    protected $signature = 'enso:localisation:scan {--dry-run} {--ignored=10}';

    protected $description = 'Scans the configured project sources and adds missing translation keys to local JSON files';

    public function handle(): int
    {
        ['found' => $found, 'ignored' => $ignored] = (new Service())->handle();

        $locales = Language::extra()->pluck('name');

        $duplicateAudit = new AuditDuplicates($locales->all(), ! $this->option('dry-run'));

        [
            'same' => $duplicates,
            'conflicting' => $conflictingDuplicates,
            'deduplicated' => $deduplicated
        ] = $duplicateAudit->handle();

        $existing = $found->filter(fn (string $key) => $this
            ->existsInAllLocales($key, $locales))->values();

        $new = $found->diff($existing)->values();

        if (! $this->option('dry-run')) {
            $this->add($found, $locales);
        }

        $this->info("Found keys: {$found->count()}");
        $this->info("New keys: {$new->count()}");
        $this->info("Existing keys: {$existing->count()}");
        $this->info("Ignored non-literal calls: {$ignored->count()}");
        $this->info("Duplicate keys with same translations: {$duplicates->count()}");
        $this->info("Duplicate keys with conflicting translations: {$conflictingDuplicates->count()}");

        if (! $this->option('dry-run')) {
            $this->info("Deduplicated duplicate keys with same translations: {$deduplicated}");
        }

        $this->ignored($ignored);
        $this->duplicates($duplicates);
        $this->conflictingDuplicate($conflictingDuplicates);

        return self::SUCCESS;
    }

    private function add(Collection $found, Collection $locales): void
    {
        (new AddKey(
            $found->mapWithKeys(fn (string $key) => [$key => null])->all(),
            $locales->all()
        ))->handle();
    }

    private function existsInAllLocales(string $key, Collection $locales): bool
    {
        return $locales->every(function (string $locale) use ($key): bool {
            $path = lang_path("{$locale}.json");

            return file_exists($path)
                ? array_key_exists($key, (new JsonReader($path))->array())
                : false;
        });
    }

    private function ignored(Collection $ignored): void
    {
        $limit = (int) $this->option('ignored');

        if ($limit < 1 || $ignored->isEmpty()) {
            return;
        }

        $map = fn (array $ignored) => [
            $ignored['file'], $ignored['line'], $ignored['call'],
        ];

        $this->table(['File', 'Line', 'Call'], $ignored->take($limit)
            ->map($map)->toArray());
    }

    private function duplicates(Collection $duplicates): void
    {
        if ($duplicates->isEmpty()) {
            return;
        }

        $this->table(
            ['Locale', 'File', 'Key', 'Translation', 'Duplicates'],
            $duplicates->map(fn (array $duplicate) => [
                $duplicate['locale'],
                $duplicate['file'],
                $duplicate['key'],
                $duplicate['translation'],
                $duplicate['duplicates'],
            ])->toArray()
        );
    }

    private function conflictingDuplicate(Collection $duplicates): void
    {
        if ($duplicates->isEmpty()) {
            return;
        }

        $this->table(
            ['Locale', 'File', 'Key', 'Translations', 'Duplicates'],
            $duplicates->map(fn (array $duplicate) => [
                $duplicate['locale'],
                $duplicate['file'],
                $duplicate['key'],
                $duplicate['translations'],
                $duplicate['duplicates'],
            ])->toArray()
        );
    }
}
