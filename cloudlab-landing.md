# CloudLab Latam: landing page (contenido y estructura)

Versión en Markdown del prototipo de landing page. Sirve como guía de contenido para el equipo que la implemente en Laravel.

## 1. Cabecera (se conserva la actual)

- **Logotipo:** `/images/logo-brand.png` + texto "CloudLab Latam" y lema "Automatización Inteligente"
- **Menú:** Inicio (`/`), Nosotros (`/about`), Tecnologías (`/technologies`), Proyectos (`/projects`), Blog (`/blogs`), Contacto (`/contact`)
- **Idioma:** ES (`/language/es`), EN (`/language/en`)
- **Botón:** Consulta Gratuita (`/contact`)

En móvil, el menú pasa a una fila deslizable debajo del logotipo.

## 2. Hero

**H1:** Automatización de procesos y analítica de datos con Microsoft Power Platform

**Texto:** Convertimos tareas manuales en flujos que se ejecutan solos y reportes en Excel en tableros que se actualizan solos. Para empresas de Latinoamérica.

**Botones:**
- Agendar consulta gratuita → `#contacto`
- Ver servicios → `#servicios` (idealmente, un índice de servicios propio)

**Datos de apoyo:** 2 a 4 semanas por entregable · Sin permanencia mínima

**Elemento visual (SVG animado):** Excel, Correos y Facturas PDF fluyen hacia Power Automate y terminan en un tablero de Power BI cuyas barras crecen al cargar la página. El texto del hero es HTML real, no imagen, para que Google lo lea. Se respeta `prefers-reduced-motion`.

## 3. Servicios (cuadrícula)

**H2:** Lo que automatizamos, medimos y ordenamos

**Introducción:** Trabajamos sobre el ecosistema Microsoft y Python. Cada servicio tendrá su propia página con casos y precios de referencia.

| Tarjeta | Tamaño | Texto |
|---|---|---|
| Automatización de procesos | Grande | Flujos con Power Automate y RPA que conectan tus aplicaciones y eliminan el trabajo repetitivo. Incluye: aprobaciones y notificaciones, conciliaciones y carga de facturas, sincronización entre sistemas. |
| Analítica con Power BI | Normal | Tableros claros que reemplazan los reportes armados a mano. |
| Ingeniería de datos | Normal | Pipelines y ETL en Microsoft Fabric para tener una sola versión de los datos. |
| Power Apps | Normal | Aplicaciones a la medida de tu operación, sin meses de desarrollo. |
| IA aplicada a tus datos | Grande | Modelos con Python y asistentes con Copilot que trabajan sobre la información de tu empresa. |

## 4. Calculadora de tiempo recuperado

**H2:** ¿Cuánto tiempo recuperarías?

**Controles:**
- Personas con tareas manuales: 1 a 50 (por defecto 5)
- Horas por persona a la semana: 1 a 30 (por defecto 6)
- Costo por hora en USD: 3 a 60 (por defecto 12)

**Resultados:**
- Horas liberadas al mes = personas × horas × 4,33 × 0,70
- USD al año = horas liberadas al mes × 12 × costo por hora

**Aviso visible:** Estimación referencial: supone que se automatiza el 70 % del tiempo manual. El análisis real se hace en la consulta gratuita.

## 5. Proceso (pestañas interactivas)

**H2:** Así trabajamos

1. **Descubrimiento:** Revisamos contigo los procesos que más tiempo consumen y elegimos uno con retorno rápido. Salimos con un alcance claro y un plazo.
2. **Diseño:** Dibujamos el flujo y los datos que intervienen. Validas el diseño antes de que escribamos una línea.
3. **Construcción:** Construimos en ciclos cortos. Ves avances funcionando cada una o dos semanas, no solo al final.
4. **Soporte:** Dejamos documentación, capacitamos a tu equipo y seguimos disponibles para ajustes y nuevos procesos.

## 6. Contacto

**H2:** Cuéntanos qué proceso te quita más tiempo

**Texto:** Te respondemos con una propuesta de por dónde empezar. Sin costo.

**Campos:** Nombre, Correo de trabajo, ¿Qué proceso quieres automatizar?

**Botón:** Agendar consulta gratuita

## 7. Pie de página

© 2026 CloudLab Latam.

## Notas para la implementación

- **Pendiente:** el prototipo no incluye logos de clientes, testimonios ni métricas de casos reales. Agregar al menos un resultado verificable del caso del taller de motocicletas.
- **Formulario:** en el prototipo es solo una demostración y no envía datos. Conectarlo a un controlador de Laravel con validación y protección CSRF.
- **Cifras de la calculadora:** son ilustrativas.
- **Ajuste de afirmaciones:** respaldar o retirar la cifra de "99.9% de disponibilidad" que aparece hoy en el sitio.
- **Tecnologías sugeridas:** Tailwind CSS, Alpine.js y Livewire para la calculadora y el formulario.
- **Prototipo visual:** https://claude.ai/artifact/RRPJzKpBt9kCHFVQqmiN3f
