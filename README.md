# Upload Abstraction

Abstração SOLID para upload de arquivos com suporte a múltiplos drivers como S3 (AWS ou LocalStack) e sistema de arquivos local.

## 🚀 Recursos

- Upload para Amazon S3 ou sistema local
- Listagem de buckets e arquivos
- Criação de buckets (repositórios)
- Arquitetura orientada a interfaces (SOLID)
- Compatível com PHPUnit para testes automatizados

## 📦 Instalação via Composer

### Local
```json
"repositories": [
  {
    "type": "path",
    "url": "../upload-abstraction-php"
  }
],
"require": {
  "seuvendor/upload-abstraction": "*"
}
```

### Packagist (exemplo futuro)
```bash
composer require seuvendor/upload-abstraction
```

## ✅ Exemplo de Uso

```php
use UploadAbstraction\UploadFile;
use UploadAbstraction\Enums\UploadDriver;

$upload = new UploadFile(UploadDriver::S3);
$upload->createRepository('meu-bucket');
$upload->upload('meu-bucket', 'arquivo.txt', '/tmp/arquivo.txt');
$arquivos = $upload->listObjects('meu-bucket');
```

## 🧪 Testes

```bash
composer install
vendor/bin/phpunit
```

## 🐳 Suporte a LocalStack (Docker)
```yaml
version: '3.8'
services:
  localstack:
    image: localstack/localstack
    ports:
      - "4566:4566"
    environment:
      - SERVICES=s3
      - AWS_DEFAULT_REGION=us-east-1
```

## 📁 Estrutura

- `src/` - Código fonte
- `tests/` - Testes unitários
