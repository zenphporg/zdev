![Zen Foundation](https://raw.githubusercontent.com/zenphporg/.github/main/img/zenphp.png)

<p align="center">
<a href="https://github.com/zenphporg/zdev/blob/main/clover.xml"><img src="https://img.shields.io/badge/dynamic/xml?color=success&label=coverage&query=round%28%2F%2Fcoverage%2Fproject%2Fmetrics%2F%40coveredelements%20div%20%2F%2Fcoverage%2Fproject%2Fmetrics%2F%40elements%20%2A%20100%29&suffix=%25&url=https%3A%2F%2Fraw.githubusercontent.com%2Fzenphporg%2Fzdev%2Fmain%2Fclover.xml" alt="Coverage"></a>
<a href="https://github.com/zenphporg/zdev/actions"><img src="https://img.shields.io/github/actions/workflow/status/zenphporg/zdev/test.yml" alt="Build Status"></a>
<a href="https://packagist.org/packages/zenphp/zdev"><img src="https://img.shields.io/packagist/dt/zenphp/zdev" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/zenphp/zdev"><img src="https://img.shields.io/packagist/v/zenphp/zdev" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/zenphp/zdev"><img src="https://img.shields.io/packagist/l/zenphp/zdev" alt="License"></a>
</p>

# About zdev

A command-line tool for scaffolding new Composer packages and projects.

## Installation

```bash
curl -sL https://github.com/zenphporg/zdev/releases/latest/download/zdev -o /usr/local/bin/zdev && chmod +x /usr/local/bin/zdev
```

Or if you need sudo:

```bash
curl -sL https://github.com/zenphporg/zdev/releases/latest/download/zdev -o zdev && chmod +x zdev && sudo mv zdev /usr/local/bin/
```

### Updating

```bash
zdev self-update
```

## Usage

### Create a new package

```bash
zdev new --package
```

You'll be prompted for the package name in `vendor/package` format.

Or pass the name directly:

```bash
zdev new zenphp/my-package --package
```

This will:

- Create a new directory with the package name
- Copy the package stub files
- Update `composer.json` with your package name

### Next steps after creating a package

```bash
cd my-package
composer install
```

## Available Stubs

| Option      | Description                   |
| ----------- | ----------------------------- |
| `--package` | Create a new Composer package |

## Requirements

- PHP 8.4+

## License

MIT
