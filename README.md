# Upload Abstraction for PHP

_A SOLID file upload layer with support for AWS S3, LocalStack and local filesystem._

![PHP Version](https://img.shields.io/badge/php-^8.1-blue)
![License](https://img.shields.io/github/license/yourvendor/upload-abstraction)

## 🤔 Why?

Uploading files across different environments often requires switching between storage backends. This library provides a clean, testable abstraction over multiple drivers using SOLID principles, making your code easier to maintain and extend.

## 🚀 Features

-   Upload to Amazon S3, LocalStack, or local filesystem
-   List buckets and files
-   Create buckets (repositories)
-   Interface-driven architecture (SOLID principles)
-   Compatible with PHPUnit for automated testing
-   Easily extendable with custom drivers

## 📦 Installation via Composer

### Local

```json
"repositories": [
  {
    "type": "path",
    "url": "../upload-abstraction-php"
  }
],
"require": {
  "yourvendor/upload-abstraction": "*"
}
```

### Packagist

```bash
composer require chrisdjst/upload-abstraction
```

## ✅ Usage Example

```php
use UploadAbstraction\UploaderFile;
use UploadAbstraction\Enums\UploaderDriver;

$upload = new UploaderFile(UploaderDriver::S3);
$upload->createRepository('my-bucket');
$upload->upload('my-bucket', 'file.txt', '/tmp/file.txt');
$files = $upload->listObjects('my-bucket');
```

## ⚙️ Configuration (Optional)

Create a `.env` file if needed:

```dotenv
UPLOAD_DRIVER=s3
AWS_REGION=us-east-1
AWS_ENDPOINT=http://localstack:4566
AWS_ACCESS_KEY_ID=test
AWS_SECRET_ACCESS_KEY=test
```

## 🧪 Running Tests

```bash
composer install
vendor/bin/phpunit
```

Generate code coverage:

```bash
vendor/bin/phpunit --coverage-html coverage/
```

## 🐳 LocalStack Support (Docker)

```yaml
version: "3.8"
services:
    localstack:
        image: localstack/localstack
        ports:
            - "4566:4566"
        environment:
            - SERVICES=s3
            - AWS_DEFAULT_REGION=us-east-1
```

## 📚 Extendable Architecture

You can add custom storage drivers by implementing the `UploadAbstraction\Contracts\UploaderDriverInterface` interface. This allows seamless integration with other providers (e.g. Google Cloud Storage, Azure Blob Storage).

## 📁 Project Structure

-   `src/` – Source code
-   `tests/` – Unit tests

---

Licensed under MIT. Contributions are welcome!
