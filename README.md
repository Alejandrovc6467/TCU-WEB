# 📚 TCU-WEB

Sistema web desarrollado en PHP con arquitectura MVC para la gestión de actividades, herramientas, proyectos y noticias en el marco de un Trabajo Comunal Universitario (TCU).

---

## 🚀 Tecnologías utilizadas

- **PHP** (Vanilla, sin frameworks)
- **MySQL** (Base de datos)
- **jQuery** (Manipulación del DOM y AJAX)
- **CSS Vanilla** (sin frameworks externos)
- **HTML5**
- **PHPMailer** (envío de correos)

---

## 🧱 Estructura del proyecto

```
TCU-WEB/
│
├── controller/             # Controladores (lógica de negocio)
├── model/                  # Modelos (acceso a la base de datos)
├── view/                   # Vistas (interfaces HTML/PHP)
├── libs/                   # Clases auxiliares y configuración
├── public/                 # Recursos públicos (JS, CSS, imágenes)
│   ├── assets/
│   └── js/
├── uploads/                # Carpeta para archivos cargados por el usuario
├── script/                 # Script SQL y archivo de instalación
│   ├── leeme.txt
│   └── tcu.sql
├── index.php               # Punto de entrada al sistema
├── .htaccess               # Configuración de Apache
└── .gitignore
```

---

## ⚙️ Instalación

1. **Clonar el repositorio**

```bash
git clone https://github.com/tu-usuario/TCU-WEB.git
cd TCU-WEB
```

2. **Importar la base de datos**

- Crear una base de datos en MySQL (ej: `tcu_web`)
- Importar el archivo SQL ubicado en `script/tcu.sql`

3. **Configurar la conexión a la base de datos**

Editar el archivo:  
`libs/configuration.php`

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tcu_web');
define('DB_USER', 'root');
define('DB_PASS', '');
```

4. **Configurar el servidor web**

- Asegúrate de que Apache permita el uso de `.htaccess`
- Establece `TCU-WEB/` como raíz del servidor o usa virtual host

---

## 📌 Funcionalidades principales

- Gestión de **actividades** y **proyectos**
- Administración de **noticias**
- Módulo de **herramientas** (usuarios y admins)
- Autenticación de **usuarios**
- Envío de **correos electrónicos**
- Interfaz limpia con **HTML + CSS + jQuery**

---

## 📁 Dependencias

- PHP >= 7.0
- MySQL
- Apache (con `mod_rewrite`)
- PHPMailer (ya incluido en `/phpmailer`)

---

## 💡 Notas

- El sistema está diseñado para proyectos universitarios o académicos.
