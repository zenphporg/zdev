<?php

use Illuminate\Support\Facades\File;

beforeEach(function (): void {
  $this->testDir = sys_get_temp_dir().'/zendev-test-'.uniqid();
  mkdir($this->testDir);
  chdir($this->testDir);
});

afterEach(function (): void {
  chdir(base_path());
  if (File::isDirectory($this->testDir)) {
    File::deleteDirectory($this->testDir);
  }
});

it('requires a stub type option', function (): void {
  $this->artisan('new', ['name' => 'vendor/package'])
    ->expectsOutput('Please specify a stub type. Available options: --package')
    ->assertExitCode(1);
});

it('validates package name format', function (): void {
  $this->artisan('new', ['name' => 'invalid-name', '--package' => true])
    ->expectsOutput('Invalid package name. Must be in format: vendor/package')
    ->assertExitCode(1);
});

it('rejects invalid package names', function (string $name): void {
  $this->artisan('new', ['name' => $name, '--package' => true])
    ->expectsOutput('Invalid package name. Must be in format: vendor/package')
    ->assertExitCode(1);
})->with([
  'no-slash',
  'UPPERCASE/package',
  'vendor/UPPERCASE',
  '/package',
  'vendor/',
  'vendor//package',
]);

it('fails when directory already exists', function (): void {
  mkdir($this->testDir.'/my-package');

  $this->artisan('new', ['name' => 'vendor/my-package', '--package' => true])
    ->expectsOutput("Directory 'my-package' already exists.")
    ->assertExitCode(1);
});

it('fails when stub directory does not exist', function (): void {
  $stubPath = base_path('stubs/package');
  $backupPath = base_path('stubs/package-backup');

  File::moveDirectory($stubPath, $backupPath);

  try {
    $this->artisan('new', ['name' => 'vendor/my-package', '--package' => true])
      ->expectsOutput("Stub directory not found: {$stubPath}")
      ->assertExitCode(1);
  } finally {
    File::moveDirectory($backupPath, $stubPath);
  }
});

it('creates a new package from stub', function (): void {
  $this->artisan('new', ['name' => 'acme/my-package', '--package' => true])
    ->assertExitCode(0);

  expect($this->testDir.'/my-package')->toBeDirectory();
  expect($this->testDir.'/my-package/composer.json')->toBeFile();
});

it('updates composer.json with package name', function (): void {
  $this->artisan('new', ['name' => 'acme/my-package', '--package' => true])
    ->assertExitCode(0);

  $composer = json_decode(file_get_contents($this->testDir.'/my-package/composer.json'), true);

  expect($composer['name'])->toBe('acme/my-package');
});

it('prompts for package name when not provided', function (): void {
  $this->artisan('new', ['--package' => true])
    ->expectsQuestion('Package name (vendor/package)', 'acme/prompted-package')
    ->assertExitCode(0);

  expect($this->testDir.'/prompted-package')->toBeDirectory();
});

it('accepts valid package names', function (string $name, string $expectedDir): void {
  $this->artisan('new', ['name' => $name, '--package' => true])
    ->assertExitCode(0);

  expect($this->testDir.'/'.$expectedDir)->toBeDirectory();
})->with([
  ['vendor/package', 'package'],
  ['my-vendor/my-package', 'my-package'],
  ['vendor123/package456', 'package456'],
  ['vendor/package-name', 'package-name'],
  ['vendor/package_name', 'package_name'],
  ['vendor/package.name', 'package.name'],
]);
