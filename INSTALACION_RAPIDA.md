# 🚀 Guía de Instalación Rápida

Esta guía te ayudará a descargar y configurar el proyecto en pocos minutos usando comandos Make.

## 📋 Requisitos Previos

Asegúrate de tener instalado:
- Docker
- PHP 8.3 o superior
- Composer
- Git

---

## 🐳 Instalación con Docker

```bash
# Paso 1: Descargar el proyecto
git clone https://github.com/mmloureiro/flat_101_product.git
cd flat_101_product

# Paso 2: Levantar y configurar todo (build + install + migrations + fixtures)
make rebuild

# La API estará disponible en http://localhost:8080
```
---

## 🆘 Solución de Problemas

### Error: "make: command not found"
Make no está instalado. Puedes instalarlo:
- **macOS**: `xcode-select --install`
- **Linux**: `sudo apt-get install build-essential`
- **Windows**: Usa WSL o ejecuta los comandos manualmente

### Error: "PHP not found"
Necesitas instalar PHP 8.3 o superior:
- **macOS**: `brew install php@8.3`
- **Linux**: `sudo apt-get install php8.3`

### Error: "composer: command not found"
Instala Composer desde: https://getcomposer.org/download/

### Los permisos fallan
```bash
chmod -R 777 var/
```

---

## 📖 Comandos Make Útiles

```bash
make help          # Ver todos los comandos disponibles
make test          # Ejecutar tests
make fixtures      # Recargar datos de prueba
make db-reset      # Resetear base de datos con datos de prueba
make cache-clear   # Limpiar caché
make status        # Ver estado del proyecto
```

---

## 🎉 ¡Listo!

Tu proyecto está configurado y funcionando en Docker. Para más información detallada, consulta el archivo [README.md](README.md).

### 🌐 URLs Importantes
- **Swagger UI**: http://localhost/api/doc
- **API Base**: http://localhost/api/products

### 📡 Endpoints Disponibles
- `GET /api/products` - Listar todos los productos
- `POST /api/products` - Crear un nuevo producto
- `GET /api/products/{id}` - Obtener un producto específico
- `PUT /api/products/{id}` - Actualizar un producto
- `DELETE /api/products/{id}` - Eliminar un producto
