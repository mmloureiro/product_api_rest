# Product API

API RESTful para gestión de productos desarrollada con Symfony 6.4 y PHP 8.3, siguiendo arquitectura hexagonal y principios de Clean Architecture.

## 🚀 Características

- ✅ CRUD completo de productos
- ✅ Arquitectura Hexagonal (Ports & Adapters)
- ✅ Validación de datos con DTOs
- ✅ Documentación OpenAPI/Swagger
- ✅ Tests unitarios e integración
- ✅ Base de datos PostgreSQL

## 📋 Requisitos Previos

Para ejecutar este proyecto de forma rápida y aislada, solo necesitas tener instalados:
- Docker y Docker Compose
- Git

(No es necesario tener PHP, Composer o bases de datos instalados en tu máquina local; todo se ejecuta dentro del contenedor).

## 🔧 Instalación

### Instalación Rápida con Makefile

El proyecto incluye un Makefile que automatiza toda la instalación:

```bash
# Instalación completa (dependencias + base de datos + datos de prueba)
make init
```

Para ver todos los comandos disponibles:
```bash
make help
```

## 🏃 Ejecución


```bash
# Iniciar contenedores
make up

# Detener contenedores
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

Ver reporte en: `var/coverage/index.html`

### Tests con formato detallado
```bash
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
- **Base de datos**: PostgreSQL
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

### Error al conectar con la base de datos
Asegúrate de que los contenedores están corriendo y el servicio de base de datos está saludable:
```bash
docker-compose ps
```

### Resetear el entorno completo
Si necesitas limpiar todo y empezar de cero:
```bash
make init
```

### Permisos en carpeta var/
El Dockerfile ya gestiona los permisos mediante ACL, pero si tuvieses problemas:
```bash
make shell
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

