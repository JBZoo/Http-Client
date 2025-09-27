# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

JBZoo Http-Client is a PHP library that provides a simple wrapper around HTTP clients like Guzzle and rmccue/requests. It abstracts HTTP operations with a clean, consistent API for making single and parallel HTTP requests.

## Development Commands

### Dependencies and Updates
```bash
make update          # Install/update all composer dependencies
composer update      # Alternative to make update
```

### Testing
```bash
make test-all        # Run all tests and code style checks
make test            # Run PHPUnit tests only
make codestyle       # Run code style checks only
phpunit              # Run tests directly with PHPUnit
```

### Mock Server for Testing
```bash
make start-mock-server  # Start Docker httpbin server on port 8087
```

## Architecture Overview

### Core Classes
- **HttpClient**: Main entry point for making HTTP requests. Supports both single and parallel requests via `multiRequest()` method.
- **Request**: Encapsulates HTTP request details (URL, method, headers, body, options).
- **Response**: Encapsulates HTTP response with convenient access methods for body, headers, and status codes.
- **Options**: Manages client configuration (timeouts, auth, SSL verification, redirects, etc.).

### Driver Pattern
The library uses a driver pattern to support multiple HTTP backends:
- **AbstractDriver**: Base class defining the interface for HTTP drivers
- **Auto**: Automatically selects the best available driver (Guzzle preferred, falls back to Rmccue)
- **Guzzle**: Uses GuzzleHttp for HTTP operations
- **Rmccue**: Uses rmccue/requests as fallback when Guzzle unavailable

### Event System
Uses JBZoo/Event for triggering events during HTTP operations:
- `request.before`: Fired before making a request
- `request.after`: Fired after receiving a response

### Key Design Patterns
- **Fluent Interface**: Request and Response objects provide fluent method chaining
- **Multi-format Access**: Response data accessible via methods, properties, or array access
- **Automatic Driver Selection**: Chooses the best available HTTP client automatically

## File Structure
```
src/
├── HttpClient.php      # Main client class
├── Request.php         # Request wrapper
├── Response.php        # Response wrapper with JSON support
├── Options.php         # Configuration management
├── Exception.php       # Custom exceptions
├── HttpCodes.php       # HTTP status code constants
└── Driver/
    ├── AbstractDriver.php
    ├── Auto.php        # Auto-selecting driver
    ├── Guzzle.php      # Guzzle implementation
    └── Rmccue.php      # Rmccue/requests implementation
```

## Dependencies
- **Required**: PHP 8.2+, jbzoo/data, jbzoo/utils, jbzoo/event
- **Suggested**: guzzlehttp/guzzle (recommended) or rmccue/requests (fallback)
- **Dev Dependencies**: jbzoo/toolbox-dev for code quality tools

## Testing Notes
- Tests use PHPUnit with XML configuration in `phpunit.xml.dist`
- Mock server available via Docker for integration testing
- Some tests may be flaky and are marked to skip in the codebase
- Test files follow `*Test.php` naming convention in the `tests/` directory