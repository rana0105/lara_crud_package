<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LaraCrudPackageService;

class GenerateCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-crud {modelName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CRUD for a model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('modelName');

        // Generate CRUD components for the specified model
        $this->info("Generating CRUD for model: $modelName");

        // Call your CRUD generation logic here
        $createLaraCrudPackage = new LaraCrudPackageService();
        $createLaraCrudPackage->createLaraCrudPackage($modelName);

        $this->info("CRUD for model $modelName generated successfully!");
    }
}
