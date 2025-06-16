# D'oh! — PHP Throwable Formatter

![Packagist Version](https://img.shields.io/packagist/v/theovdml/doh)
![GitHub Tag](https://img.shields.io/github/v/tag/theo-vdml/doh-php-error-formatter)
[![Total Downloads](https://img.shields.io/packagist/dt/theovdml/doh.svg)](https://packagist.org/packages/theovdml/doh)
[![License](https://img.shields.io/packagist/l/theovdml/doh.svg)](https://packagist.org/packages/theovdml/doh)

**D'oh!** is a lightweight, flexible PHP library for formatting exceptions and errors (`\Throwable`)  
into various human- and machine-readable formats: HTML, JSON, JSON:API, plain text, and XML.

It simplifies transforming PHP exceptions into structured, beautifully formatted outputs,  
perfect for error pages, API error responses, logs, or CLI tools.

Doh was originally designed as part of the Potager framework to provide elegant throwable formatting.
However, it is fully decoupled and works perfectly as a standalone package in any PHP project.
It’s not as ambitious or feature-rich as libraries like Whoops, and it’s still a work in progress with limited testing.
Use with caution and feel free to contribute!

---

## Features

-   Formats throwables with rich details including stack traces and source excerpts.
-   Supports multiple output formats:
    -   **HTML**: Syntax-highlighted error pages for web display
    -   **JSON**: API-friendly error data serialization
    -   **JSON:API**: Compliant error document for standardized API responses
    -   **Plain text**: Readable text format for CLI or logging
    -   **XML**: Well-formed XML error representation
-   Easy integration with any PHP app, framework-agnostic.
-   PSR-4 autoloaded and Composer ready.

---

## Installation

Install via Composer:

```bash
composer require theovdml/doh
```

---

## Usage

```php
use DohFormatting\Doh\Doh;

try {
    // Some code that throws an exception
    throw new RuntimeException("Something went wrong!");
} catch (\Throwable $e) {
    $formatter = new Doh($e);

    // Get HTML formatted error page (for web display)
    echo $formatter->toHtml();

    // Get JSON for API response or logging
    $json = $formatter->toJson();

    // Get JSON:API compliant error document
    $jsonApi = $formatter->toJsonApi();

    // Plain text (for CLI or logs)
    $text = $formatter->toPlainText();

    // XML error representation
    $xml = $formatter->toXml();
}
```

---

## API Documentation

### `Doh::__construct(\Throwable $throwable)`

Create a new Doh formatter instance for the given throwable.

-   **\$throwable**: The exception or error to format.

### `Doh::buildTrace(): Trace`

Builds a detailed `Trace` object representing the throwable's stack trace,
including a virtual top frame for the exact throw location.

### `Doh::getThrowable(): \Throwable`

Returns the original throwable instance.

### `Doh::getClass(): string`

Returns the fully qualified class name of the throwable.

### `Doh::getMessage(): string`

Returns the throwable's message string.

### Formatters

All return a formatted string representing the throwable.

-   `toHtml(): string` — HTML error page with syntax-highlighted source excerpts and stack trace.
-   `toJson(): string` — JSON string with error details and stack trace array.
-   `toJsonApi(): string` — JSON\:API compliant error document string.
-   `toPlainText(): string` — Plain text representation suitable for CLI or logs.
-   `toXml(): string` — XML error document describing the error and trace.

---

## License

MIT License © theo_vdml

---

## Contributing

Feel free to submit issues or pull requests on GitHub.
Please include tests and documentation for any new features.

---

## Acknowledgments

This package uses [scrivo/highlight.php](https://github.com/scrivo/highlight.php) for code syntax highlighting.
