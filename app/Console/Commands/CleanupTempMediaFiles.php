<?php

namespace App\Console\Commands;

use App\Models\MediaFile;
use Illuminate\Console\Command;

class CleanupTempMediaFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:cleanup {--hours=24 : Hours after which temp files are considered old}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old temporary media files that were never finalized';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hours = $this->option('hours');
        
        $this->info("Cleaning up temporary files older than {$hours} hours...");
        
        $count = MediaFile::cleanupOldTempFiles();
        
        if ($count > 0) {
            $this->info("✓ Successfully cleaned up {$count} temporary file(s).");
        } else {
            $this->info("✓ No temporary files to clean up.");
        }
        
        return Command::SUCCESS;
    }
}
