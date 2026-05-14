<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('hapus:storage')]
#[Description('Menghapus isi folder storage/app/public dengan aman')]
class ClearPublicStorage extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai penghapusan isi folder storage/app/public...');

        $disk = Storage::disk('public');
        
        // Ambil semua file dan folder di dalam disk public
        $files = $disk->allFiles();
        $directories = $disk->allDirectories();

        $deletedFiles = 0;
        $deletedFolders = 0;

        // Hapus file, kecualikan .gitignore
        foreach ($files as $file) {
            if (basename($file) !== '.gitignore') {
                $disk->delete($file);
                $deletedFiles++;
            }
        }

        // Hapus semua sub-folder beserta isinya
        foreach ($directories as $directory) {
            $disk->deleteDirectory($directory);
            $deletedFolders++;
        }

        $this->info("Berhasil dihapus: {$deletedFiles} file dan {$deletedFolders} folder.");
        $this->info('Folder storage/app/public berhasil dibersihkan dengan aman (file .gitignore tetap dipertahankan).');
    }
}
