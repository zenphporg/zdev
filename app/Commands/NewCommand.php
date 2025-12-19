<?php

declare(strict_types=1);

namespace App\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

use function Termwind\render;

class NewCommand extends Command
{
  /**
   * The signature of the command.
   *
   * @var string
   */
  protected $signature = 'new
    {name? : The package name (vendor/package)}
    {--package : Create a new Composer package}';

  /**
   * The description of the command.
   *
   * @var string
   */
  protected $description = 'Scaffold a new project from a stub';

  /**
   * Execute the console command.
   */
  public function handle(): int
  {
    if ($this->option('package')) {
      return $this->createPackage();
    }

    $this->error('Please specify a stub type. Available options: --package');

    return self::FAILURE;
  }

  /**
   * Create a new Composer package.
   */
  private function createPackage(): int
  {
    /** @var string $name */
    $name = $this->argument('name') ?? $this->ask('Package name (vendor/package)');

    if (! $this->isValidPackageName($name)) {
      $this->error('Invalid package name. Must be in format: vendor/package');

      return self::FAILURE;
    }

    $directory = $this->getDirectoryName($name);
    $targetPath = getcwd().'/'.$directory;

    if (File::exists($targetPath)) {
      $this->error("Directory '{$directory}' already exists.");

      return self::FAILURE;
    }

    $stubPath = base_path('stubs/package');

    if (! File::isDirectory($stubPath)) {
      $this->error("Stub directory not found: {$stubPath}");

      return self::FAILURE;
    }

    File::makeDirectory($targetPath, 0755, true);
    File::copyDirectory($stubPath, $targetPath);

    $this->updateComposerJson($targetPath, $name);

    render(<<<HTML
      <div class="py-1 ml-2">
        <div class="px-1 bg-green-300 text-black">Package Created</div>
        <div class="ml-1 mt-1">
          <span class="text-green-500">&#10003;</span> Created new package: <span class="font-bold">{$name}</span>
        </div>
        <div class="ml-1">
          <span class="text-gray-500">Location:</span> {$targetPath}
        </div>
        <div class="ml-1 mt-1 text-gray-400">
          Next steps:
        </div>
        <div class="ml-3 text-gray-400">
          cd {$directory} && composer install
        </div>
      </div>
    HTML);

    return self::SUCCESS;
  }

  /**
   * Validate the package name follows Composer naming convention.
   */
  private function isValidPackageName(string $name): bool
  {
    return (bool) preg_match('/^[a-z0-9]([_.-]?[a-z0-9]+)*\/[a-z0-9]([_.-]?[a-z0-9]+)*$/', $name);
  }

  /**
   * Extract the directory name from the package name.
   */
  private function getDirectoryName(string $name): string
  {
    $parts = explode('/', $name);

    return end($parts);
  }

  /**
   * Update the composer.json file with the package name.
   */
  private function updateComposerJson(string $targetPath, string $name): void
  {
    $composerPath = $targetPath.'/composer.json';
    /** @var array{name: string} $composer */
    $composer = json_decode(File::get($composerPath), true);

    $composer['name'] = $name;

    File::put(
      $composerPath,
      json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n"
    );
  }
}
