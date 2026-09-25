<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Console\Command;

class RegenerateMedia extends Command
{
    protected $signature = 'media:regenerate {--missing : Only images without generated variants} {--id=* : Specific media ids}';

    protected $description = 'Rebuild responsive image variants using the current media settings';

    public function handle(MediaService $service): int
    {
        $query = Media::images()
            ->when($this->option('missing'), fn ($q) => $q->whereNull('variants'))
            ->when($this->option('id'), fn ($q, $ids) => $q->whereIn('id', $ids));

        $bar = $this->output->createProgressBar($query->count());

        $query->orderBy('id')->each(function (Media $media) use ($service, $bar) {
            try {
                $service->generateVariants($media);
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("#{$media->id} {$media->path}: {$e->getMessage()}");
            }
            $bar->advance();
        });

        $bar->finish();
        $this->newLine();

        return self::SUCCESS;
    }
}
