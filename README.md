# Product API - Prueba Técnica

API RESTful para gestión de productos desarrollada con Symfony 6.4 y PHP 8.3, siguiendo arquitectura hexagonal y principios de Clean Architecture.

## 🚀 Características

- ✅ CRUD completo de productos
- ✅ Arquitectura Hexagonal (Ports & Adapters)
- ✅ Validación de datos con DTOs
- ✅ Documentación OpenAPI/Swagger
- ✅ Tests unitarios e integración
- ✅ Base de datos SQLite

## 📋 Requisitos Previos

- PHP 8.3 o superior
- Composer
- Extensiones PHP: ctype, iconv, sqlite3

## 🔧 Instalación

### Instalación Rápida con Makefile (Recomendado)

El proyecto incluye un Makefile que automatiza toda la instalación:

```bash
# Instalación completa (dependencias + base de datos + datos de prueba)
make setup

# Ver servidor en http://localhost:8000
make serve
```

Para ver todos los comandos disponibles:
```bash
make help
```

### Instalación Manual

**Si usas Docker**, primero accede al contenedor:
```bash
make shell
# O manualmente:
docker-compose exec php bash
```
1. Clonar el repositorio:
```bash
git clone https://github.com/mmloureiro/fla# 
composer install

# O fuera de Docker (solo si no usas Docker):
make install-localt_101_product.git
cd flat_101_product
```

2. Instalar dependencias:
```bash
# Dentro del contenedor Docker:
composer install
# O usando Make:
make install-local
```

3. Crear la base de datos y ejecutar migraciones:
```bash
# Dentro del contenedor Docker:
php bin/console doctrine:migrations:migrate --no-interaction
# O usando Make:
make setup-db
```

4. (Opcional) Cargar datos de prueba:
```bash
# Dentro del contenedor Docker:
php bin/console doctrine:fixtures:load --no-interaction
# O usando Make:
make fixtures
```

## 🏃 Ejecución

### Servidor de desarrollo

Con Makefile (recomendado):
```bash
make serve
```

Con Symfony CLI:
```bash
symfony server:start
# O usando Make:
make serve-symfony
```

Con PHP built-in server:
```bash
php -S localhost:8000 -t public
```

La API estará disponible en: `http://localhost:8000`

### Con Docker

```bash
# Iniciar contenedores
make docker-up
# O simplemente:
make up

# Detener contenedores
make docker-down
# O:
make down
```

## 📚 Documentación API

### Swagger UI
Accede a la documentación interactiva en:
```
http://localhost:8000/api/doc
```

### Endpoints disponibles

#### Listar todos los productos
```http
GET /api/products
```

#### Obtener un producto
```http
GET /api/products/{id}
```

#### Crear un producto
```http
POST /api/products
Content-Type: application/json

{
  "name": "Producto Ejemplo",
  "price": 29.99
}
```

#### Actualizar un producto
```http
PUT /api/products/{id}
Content-Type: application/json

{
  "name": "Producto Actualizado",
  "price": 39.99
}
```

#### Eliminar un producto
```http
DELETE /api/products/{id}
```

## 🧪 Tests

### Ejecutar todos los tests

Con Makefile (recomendado):
```bash
make test
```

O manualmente (dentro del contenedor si usas Docker):
```bash
php bin/phpunit
```

### Tests con cobertura

Con Makefile:
```bash
make test-coverage
```

O manualmente (dentro del contenedor si usas Docker):
```bash
php bin/phpunit --coverage-html var/coverage
```

Ver reporte en: `var/coverage/index.html`

### Tests con formato detallado
```bash
php bin/phpunit --testdox
# O:
make test
```

### Tests por tipo

```bash
# Solo tests unitarios
make test-unit

# Solo tests de integración
make test-integration

# Tests rápidos (sin coverage)
make test-quick
```

### Tests con Docker

```bash
make docker-test
make docker-test-coverage
```

## 🏗️ Estructura del Proyecto

```
src/Product/
├── Application/          # Casos de uso
│   ├── Create/
│   ├── Delete/
│   ├── Find/
│   ├── List/
│   └── Update/
├── Domain/              # Entidades y contratos
│   ├── Entity/
│   └── Repository/
└── Infrastructure/      # Implementaciones
    ├── Controller/
    ├── Dto/
    ├── Persistence/
    └── Repository/
```

## 🎯 Validaciones

### Nombre del producto
- ✅ Requerido
- ✅ Mínimo 3 caracteres

### Precio
- ✅ Requerido
- ✅ Debe ser un número válido
- ✅ Debe ser mayor que 0

## 🛠️ Tecnologías Utilizadas

- **Framework**: Symfony 6.4
- **PHP**: 8.3
- **ORM**: Doctrine ORM 3.6
- **Base de datos**: SQLite
- **Testing**: PHPUnit
- **Documentación API**: Nelmio API Doc Bundle (OpenAPI/Swagger)

## 📝 Decisiones de Diseño

### Arquitectura Hexagonal
El proyecto sigue el patrón de arquitectura hexagonal para:
- Separar la lógica de negocio de la infraestructura
- Facilitar el testing
- Permitir cambios en la infraestructura sin afectar el dominio

### Capas

1. **Domain**: Contiene las entidades y las interfaces de repositorio (puertos)
2. **Application**: Casos de uso que orquestan la lógica de negocio
3. **Infrastructure**: Implementaciones concretas (adaptadores) como controladores, repositorios Doctrine, DTOs

### Validación
Se implementa validación en dos niveles:
- En el DTO (ProductRequestDto) usando constraints de Symfony
- En la entidad de dominio (Product) con validación de negocio

## 🐛 Solución de Problemas

### Error: "No such file or directory" al ejecutar migraciones
```bash
mkdir -p var
php bin/console doctrine:migrations:migrate
# O usando Make:
make setup-db
```

### Los tests fallan por permisos
```bash
chmod -R 777 var/
```

## 🛠️ Comandos del Makefile

El proyecto incluye un Makefile completo con comandos para facilitar el desarrollo. Para ver todos los comandos disponibles:

```bash
make help
```

### Comandos principales

**Instalación y Setup:**
- `make setup` - Instalación completa (local, sin Docker)
- `make install-local` - Instalar dependencias
- `make setup-db` - Configurar base de datos
- `make setup-test-db` - Configurar base de datos de tests

**Testing:**
- `make test` - Ejecutar todos los tests
- `make test-unit` - Solo tests unitarios
- `make test-integration` - Solo tests de integración
- `make test-coverage` - Tests con reporte de cobertura
- `make test-quick` - Tests sin cobertura (más rápido)

**Base de Datos:**
- `make fixtures` - Cargar datos de prueba
- `make migrate` - Ejecutar migraciones
- `make migration-generate` - Generar nueva migración
- `make migration-status` - Ver estado de migraciones
- `make db-reset` - Resetear base de datos (drop + migrate + fixtures)
- `make db-drop` - Eliminar base de datos

**Docker:**
- `make up` o `make docker-up` - Iniciar contenedores
- `make down` o `make docker-down` - Detener contenedores
- `make rebuild` o `make docker-rebuild` - Reconstruir desde cero
- `make shell` o `make docker-shell` - Acceder al contenedor PHP
- `make docker-test` - Ejecutar tests en Docker
- `make docker-fixtures` - Cargar fixtures en Docker

**Desarrollo:**
- `make serve` - Iniciar servidor local (puerto 8000)
- `make serve-symfony` - Iniciar servidor Symfony CLI
- `make cache-clear` - Limpiar caché
- `make clean` - Limpiar caché, logs y archivos temporales
- `make lint` - Verificar sintaxis PHP
- `make check` - Ejecutar todas las verificaciones (lint + tests)
- `make routes` - Listar todas las rutas
- `make status` - Ver estado del proyecto
- `make info` - Ver información del proyecto y URLs
- `make api-doc` - Mostrar URLs de documentación API

### Ejemplos de uso

```bash
# Setup completo del proyecto
make setup

# Iniciar servidor y ejecutar tests
make serve &
make test

# Resetear base de datos con datos frescos
make db-reset

# Ver estado del proyecto
make status

# Ejecutar tests con cobertura
make test-coverage

# Docker: setup completo
make docker-rebuild
make docker-test
```

