<?php

/**
 * Indexable service pages. Slugs match the SEO plan and stay stable in both languages.
 * Body copy is intentionally long so each URL can rank on its own topic.
 */

$faq = static function (array $pairs): array {
    return array_map(static function (array $pair): array {
        return ['question' => $pair[0], 'answer' => $pair[1]];
    }, $pairs);
};

return [
    'pages' => [
        [
            'slug' => 'automatizacion-power-automate',
            'icon' => 'bi-lightning-charge-fill',
            'keywords' => ['Power Automate', 'Power Platform'],
            'es' => [
                'nav' => 'Power Automate',
                'h1' => 'Automatización de procesos con Microsoft Power Automate',
                'excerpt' => 'Flujos en la nube y de escritorio para quitar tareas repetitivas de finanzas, operaciones y ventas.',
                'meta_title' => 'Automatización con Power Automate | CloudLab Latam',
                'meta_description' => 'Diseñamos flujos de Power Automate conectados a Microsoft 365, Dataverse y tus sistemas de negocio. Solicita hoy una consulta gratuita.',
                'paragraphs' => [
                    'CloudLab Latam diseña e implementa automatizaciones con Microsoft Power Automate para empresas que todavía dependen de correos, hojas de cálculo y aprobaciones manuales. El servicio cubre flujos de nube, flujos de escritorio cuando el proceso sigue anclado a una aplicación local, y la conexión con Microsoft 365, SharePoint, Dataverse, Teams, Outlook, Dynamics 365 y APIs propias. No se trata de publicar un flujo aislado: el entregable es un proceso medible, con dueño, excepciones definidas y una forma clara de operarlo sin depender del consultor.',
                    'El trabajo empieza por el proceso, no por la herramienta. En las primeras sesiones identificamos el volumen, el tiempo que consume cada paso, los sistemas que intervienen y las reglas que hoy viven en la cabeza de una persona. Con eso se decide qué conviene automatizar de inmediato, qué debe simplificarse antes y qué no vale la pena tocar. Esa distinción evita el error más común: automatizar un proceso desordenado y terminar con un flujo frágil que nadie quiere mantener.',
                    'En finanzas vemos cuentas por pagar, conciliación de facturas, recordatorios de cobro y el paso de soportes entre el correo y el ERP. En operaciones aparecen solicitudes internas, altas de proveedores, seguimiento de pedidos y avisos cuando un indicador se sale de rango. En el área comercial, Power Automate puede crear el expediente del cliente, avisar al equipo en Teams y dejar el dato listo para Power BI. Cada flujo se documenta con el disparador, las condiciones, los conectores y el comportamiento cuando un sistema no responde.',
                    'La arquitectura se apoya en el ecosistema que la empresa ya paga. Si la información vive en SharePoint o Dataverse, el flujo escribe ahí y no en un archivo personal. Si hace falta un formulario, se evalúa Power Apps o una lista de SharePoint antes de construir una aplicación aparte. Las credenciales quedan en conectores administrados, no en cuentas de un usuario que puede salir de la compañía. Cuando el proceso cruza un sistema externo, se define el contrato de la API, los reintentos y la alerta que debe recibir el equipo si la integración falla.',
                    'Power Automate no reemplaza por sí solo un programa de RPA. Sirve muy bien cuando el proceso puede expresarse con conectores, aprobaciones y datos estructurados. Si el paso crítico ocurre dentro de una aplicación de escritorio sin API, el flujo de nube se complementa con un flujo de escritorio o con la práctica de automatización robótica que también ofrece CloudLab Latam. Esa frontera se acuerda al inicio para no prometer un conector donde hace falta un robot, ni un robot donde basta un flujo.',
                    'La entrega incluye el flujo en el ambiente acordado, pruebas con casos reales y de excepción, una guía corta para el equipo que lo va a operar y los indicadores que permiten ver si el proceso está corriendo. Esos indicadores pueden aterrizar en Power BI: solicitudes atendidas, tiempo de ciclo, tareas que volvieron a hacerse a mano y errores por conector. Sin esa medición, la automatización se vuelve invisible y es difícil justificar el siguiente proceso.',
                    'También dejamos reglas de gobierno mínimas: quién puede editar el flujo, cómo se pide un cambio, qué ambientes existen y cómo se nombra cada solución dentro de Power Platform. Las empresas que omiten este punto terminan con decenas de flujos personales, licencias mal asignadas y nadie responsable cuando un conector deja de funcionar. CloudLab Latam acompaña esa conversación con el área de tecnología y con el dueño del proceso de negocio.',
                    'Un proyecto típico recorre descubrimiento, diseño del flujo objetivo, construcción, pruebas con usuarios y salida a producción. El horizonte habitual de un primer proceso bien acotado es de dos a cuatro semanas, sin contratos de permanencia. Después se puede repetir el mismo método sobre el siguiente cuello de botella. Si quieres ver cómo se vería en tu operación, pide una consulta gratuita y revisamos un proceso concreto, no una lista genérica de beneficios.',
                ],
                'faqs' => $faq([
                    ['¿Power Automate sirve si no tenemos todo en Microsoft 365?', 'Sí. El flujo puede empezar en el correo o en una lista y hablar con otros sistemas por API. Si no hay API, evaluamos un flujo de escritorio o RPA para ese tramo.'],
                    ['¿Quién mantiene los flujos después de la entrega?', 'El equipo interno, con una guía de operación. Dejamos dueño, nombres claros y una forma de pedir cambios. También podemos acompañar los primeros ajustes.'],
                    ['¿En cuánto tiempo se ve un primer resultado?', 'Un proceso acotado suele diseñarse e implementarse en dos a cuatro semanas, después de elegir un caso con volumen y reglas claras.'],
                    ['¿Cómo se diferencia este servicio del de RPA?', 'Power Automate conecta sistemas y aprobaciones. RPA actúa sobre aplicaciones que no ofrecen una interfaz de integración. Muchos procesos usan los dos.'],
                ]),
            ],
            'en' => [
                'nav' => 'Power Automate',
                'h1' => 'Process automation with Microsoft Power Automate',
                'excerpt' => 'Cloud and desktop flows that remove repetitive work from finance, operations, and sales.',
                'meta_title' => 'Power Automate Automation | CloudLab Latam',
                'meta_description' => 'We design Power Automate flows connected to Microsoft 365, Dataverse, and your business systems. Request a free consultation today.',
                'paragraphs' => [
                    'CloudLab Latam designs and implements Microsoft Power Automate for companies that still rely on email, spreadsheets, and manual approvals. The service covers cloud flows, desktop flows when a step is trapped in a local application, and connections to Microsoft 365, SharePoint, Dataverse, Teams, Outlook, Dynamics 365, and custom APIs. The deliverable is not a lone flow. It is a measurable process with an owner, defined exceptions, and a way for the internal team to run it without calling the consultant every week.',
                    'Work starts with the process, not the tool. In the first sessions we look at volume, the time each step consumes, the systems involved, and the rules that currently live in one person head. That picture shows what should be automated now, what should be simplified first, and what should be left alone. Automating a messy process only produces a fragile flow that nobody wants to maintain.',
                    'In finance we see accounts payable, invoice matching, collection reminders, and the handoff of supporting documents between email and the ERP. In operations we see internal requests, supplier onboarding, order follow-up, and alerts when a metric leaves its range. In sales, Power Automate can open the customer file, notify the team in Teams, and leave the record ready for Power BI. Every flow is documented with its trigger, conditions, connectors, and the behavior when a system does not respond.',
                    'The design uses the Microsoft estate the company already pays for. If the data belongs in SharePoint or Dataverse, the flow writes there instead of a personal file. If a form is required, we look at Power Apps or a SharePoint list before building a separate application. Credentials stay in managed connectors, not in the account of an employee who may leave. When the process crosses an external system, we define the API contract, retries, and the alert the team must receive if the integration fails.',
                    'Power Automate does not replace an RPA program by itself. It is the right tool when the process can be expressed with connectors, approvals, and structured data. If the critical step happens inside a desktop application with no API, the cloud flow is paired with a desktop flow or with the robotic automation practice CloudLab Latam also delivers. That boundary is agreed at the start so we do not promise a connector where a bot is required, or a bot where a flow is enough.',
                    'Delivery includes the flow in the agreed environment, tests with real cases and exceptions, a short guide for the people who will operate it, and the indicators that show whether the process is actually running. Those indicators can land in Power BI: requests handled, cycle time, work that fell back to manual steps, and connector errors. Without that measurement, automation becomes invisible and the next process is hard to justify.',
                    'We also leave a minimum governance model: who can edit the flow, how a change is requested, which environments exist, and how each Power Platform solution is named. Companies that skip this end up with dozens of personal flows, poorly assigned licenses, and nobody accountable when a connector stops. CloudLab Latam holds that conversation with technology and with the business owner of the process.',
                    'A typical project moves through discovery, target-flow design, build, user testing, and production. A first, well-bounded process usually takes two to four weeks, with no lock-in contract. The same method can then be repeated on the next bottleneck. If you want to see what this looks like in your operation, request a free consultation and we will review one concrete process rather than a generic list of benefits.',
                ],
                'faqs' => $faq([
                    ['Does Power Automate work if we are not fully on Microsoft 365?', 'Yes. A flow can start in email or a list and call other systems through an API. If there is no API, we look at a desktop flow or RPA for that step.'],
                    ['Who maintains the flows after delivery?', 'Your internal team, with an operating guide. We leave an owner, clear names, and a way to request changes. We can also support the first adjustments.'],
                    ['How soon is a first result visible?', 'A bounded process is usually designed and implemented in two to four weeks, once a case with volume and clear rules is chosen.'],
                    ['How is this different from RPA?', 'Power Automate connects systems and approvals. RPA acts on applications that do not offer an integration interface. Many processes use both.'],
                ]),
            ],
        ],
        [
            'slug' => 'power-bi-analitica',
            'icon' => 'bi-bar-chart-fill',
            'keywords' => ['Power BI', 'Tableau'],
            'es' => [
                'nav' => 'Power BI',
                'h1' => 'Analítica de datos con Power BI',
                'excerpt' => 'Modelos, indicadores y tableros para que las áreas decidan con una sola versión de las cifras.',
                'meta_title' => 'Analítica y tableros con Power BI | CloudLab Latam',
                'meta_description' => 'Modelamos indicadores y tableros de Power BI para finanzas, ventas y operaciones de tu empresa. Solicita hoy una consulta gratuita.',
                'paragraphs' => [
                    'CloudLab Latam construye la capa analítica que la empresa necesita para dejar de discutir cuál archivo tiene la cifra correcta. El servicio de Power BI cubre el modelo de datos, las medidas de negocio, los tableros para cada audiencia y la publicación en un área de trabajo con permisos claros. Cuando el cliente ya visualiza en Tableau, el mismo criterio de modelo y de indicador se respeta para no crear dos verdades. La meta es que finanzas, operaciones y la dirección miren los mismos números, con el detalle que cada rol necesita.',
                    'Un tablero bonito sobre datos sucios no sirve. Por eso este servicio se conversa con la ingeniería de datos. Si la información ya está en un lakehouse de Microsoft Fabric, Power BI se conecta a ese modelo. Si todavía está repartida en Excel, en el ERP y en el CRM, definimos qué fuentes entran a la primera versión y qué transformaciones no pueden vivir ocultas dentro del archivo de Power BI. Las reglas de negocio importantes quedan documentadas: qué es una venta, cuándo una factura está vencida y cómo se trata una devolución.',
                    'El modelo se diseña para preguntas reales. No empezamos por todas las tablas del sistema. Empezamos por las decisiones de los próximos noventa días: margen por línea, cumplimiento de pedidos, pipeline comercial, costo operativo o uso de un canal. Cada indicador tiene dueño, fórmula, grano y frecuencia de actualización. Esa ficha evita que dos reportes usen el mismo nombre para cosas distintas, un problema que aparece en casi todas las empresas que crecieron a punta de hojas de cálculo.',
                    'La experiencia de uso importa tanto como el modelo. Un directivo necesita una página con pocos números y la tendencia. Un analista necesita filtrar por cliente, producto y periodo sin romper el cálculo. Diseñamos esas dos lecturas y probamos el informe con las personas que lo van a abrir el lunes, no solo con el equipo de tecnología. Los nombres de los campos se escriben en el idioma del negocio. Los filtros por defecto muestran el periodo que de verdad se revisa en la reunión.',
                    'También incorporamos analítica más avanzada cuando el dato ya es confiable. Con Python y técnicas de aprendizaje automático se pueden anticipar tendencias, segmentar clientes o estimar demanda, y el resultado se publica donde la gente ya mira: el tablero. No tiene sentido un modelo predictivo en un cuaderno que nadie abre. CloudLab Latam conecta ese resultado a Power BI para que la predicción se compare con lo que ocurrió y el negocio pueda decidir si el modelo aporta.',
                    'La seguridad no se deja para el final. Se define quién ve cifras consolidadas, quién ve solo su región y qué datos personales no deben aparecer en un informe compartido. La publicación usa áreas de trabajo, aplicaciones y, cuando hace falta, seguridad a nivel de fila. Así un gerente comercial no exporta la cartera completa por accidente. El mismo cuidado aplica si el informe se incrusta en Teams o en un portal interno.',
                    'La adopción se trabaja con una sesión corta de lectura del tablero, no con un manual de cincuenta páginas. Mostramos cómo filtrar, cómo llegar al detalle y qué no debe interpretarse del gráfico. Dejamos el inventario de indicadores y el contacto del dueño de cada uno. Si un número cambia de definición, esa ficha se actualiza antes de cambiar el visual, para que el histórico siga siendo comparable.',
                    'Un primer tablero útil, conectado a fuentes estables y con indicadores acordados, suele entregarse en dos a cuatro semanas. A partir de ahí se agregan páginas o áreas. Si hoy las decisiones salen de varios Excel, pide una consulta gratuita: revisamos qué pregunta de negocio conviene responder primero y qué dato ya está listo para Power BI.',
                ],
                'faqs' => $faq([
                    ['¿Podemos seguir usando Tableau?', 'Sí. El criterio de indicadores y de modelo se puede aplicar en Tableau. Si la plataforma objetivo es Microsoft, Power BI suele ser el lugar natural de publicación.'],
                    ['¿El tablero limpia los datos por sí solo?', 'No. Las reglas importantes deben vivir en el modelo o en la capa de ingeniería. Power BI muestra y calcula; no debe esconder la única copia de la lógica de negocio.'],
                    ['¿Qué se necesita para empezar?', 'Una pregunta de negocio concreta, acceso a las fuentes y una persona que confirme la definición de cada indicador. Con eso se puede acotar una primera versión.'],
                    ['¿Incluye pronósticos o solo reportes?', 'La base es el reporte confiable. Cuando el dato ya está estable, se pueden sumar segmentaciones y pronósticos con Python publicados en el mismo tablero.'],
                ]),
            ],
            'en' => [
                'nav' => 'Power BI',
                'h1' => 'Data analytics with Power BI',
                'excerpt' => 'Models, metrics, and dashboards so every team decides from one version of the numbers.',
                'meta_title' => 'Power BI Analytics and Dashboards | CloudLab Latam',
                'meta_description' => 'We model business metrics and Power BI dashboards for finance, sales, and operations. Request your free consultation today.',
                'paragraphs' => [
                    'CloudLab Latam builds the analytics layer a company needs in order to stop arguing about which file has the right number. The Power BI service covers the data model, business measures, dashboards for each audience, and publishing in a workspace with clear permissions. When a client already visualizes in Tableau, the same modeling and metric standard is kept so two truths are not created. The goal is that finance, operations, and leadership look at the same numbers, with the detail each role needs.',
                    'A handsome dashboard on dirty data does not help. This service is planned together with data engineering. If the information already lives in a Microsoft Fabric lakehouse, Power BI connects to that model. If it is still split across Excel, the ERP, and the CRM, we decide which sources enter the first version and which transformations must not hide inside the Power BI file. Important business rules are written down: what counts as a sale, when an invoice is overdue, and how a return is treated.',
                    'The model is designed for real questions. We do not start from every table in the system. We start from the decisions of the next ninety days: margin by line, order fulfillment, sales pipeline, operating cost, or channel usage. Each metric has an owner, a formula, a grain, and a refresh frequency. That card stops two reports from using the same name for different things, a problem that shows up in almost every company that grew on spreadsheets.',
                    'How people use the report matters as much as the model. An executive needs one page with few numbers and the trend. An analyst needs to filter by customer, product, and period without breaking the calculation. We design both readings and test the report with the people who will open it on Monday, not only with technology. Field names use the language of the business. Default filters show the period that is actually reviewed in the meeting.',
                    'More advanced analytics come in when the data is already trustworthy. With Python and machine learning, a team can anticipate trends, segment customers, or estimate demand, and the result is published where people already look: the dashboard. A predictive model inside a notebook nobody opens does not change a decision. CloudLab Latam connects that result to Power BI so the prediction can be compared with what happened and the business can judge whether the model helps.',
                    'Security is not left for the end. We define who sees consolidated figures, who sees only their region, and which personal data must not appear in a shared report. Publishing uses workspaces, apps, and row-level security when needed. A sales manager should not export the full portfolio by accident. The same care applies if the report is embedded in Teams or an internal portal.',
                    'Adoption is a short session on how to read the dashboard, not a fifty-page manual. We show how to filter, how to reach the detail, and what the chart should not be used to claim. We leave the metric inventory and the owner of each one. If a definition changes, the card is updated before the visual changes, so history stays comparable.',
                    'A first useful dashboard, connected to stable sources and agreed metrics, is usually delivered in two to four weeks. Pages and business areas are added after that. If decisions still come from several spreadsheets, request a free consultation: we will look at which business question should be answered first and which data is already ready for Power BI. Bring the report you do not trust and we will say whether a first version can stand on the data you already have.',
                ],
                'faqs' => $faq([
                    ['Can we keep using Tableau?', 'Yes. The same metric and modeling standard can be applied in Tableau. If the target platform is Microsoft, Power BI is usually the natural place to publish.'],
                    ['Does the dashboard clean the data by itself?', 'No. Important rules should live in the model or in the engineering layer. Power BI displays and calculates; it should not hide the only copy of business logic.'],
                    ['What do we need to start?', 'One concrete business question, access to the sources, and a person who can confirm each metric definition. That is enough to bound a first version.'],
                    ['Is this only reporting, or also forecasts?', 'The foundation is a trustworthy report. Once the data is stable, segmentation and Python forecasts can be published on the same dashboard.'],
                ]),
            ],
        ],
        [
            'slug' => 'microsoft-fabric-ingenieria-de-datos',
            'icon' => 'bi-grid-1x2-fill',
            'keywords' => ['Fabric', 'Microsoft Fabric'],
            'es' => [
                'nav' => 'Microsoft Fabric',
                'h1' => 'Ingeniería de datos con Microsoft Fabric',
                'excerpt' => 'Una plataforma para integrar, preparar y gobernar los datos que después alimentan Power BI.',
                'meta_title' => 'Ingeniería de datos con Microsoft Fabric | CloudLab Latam',
                'meta_description' => 'Integramos y preparamos datos en Microsoft Fabric para que Power BI y el negocio compartan una sola fuente. Pide una consulta gratuita.',
                'paragraphs' => [
                    'Microsoft Fabric es la base sobre la que CloudLab Latam unifica la información de la empresa. El servicio de ingeniería de datos diseña la ingesta, la transformación, el almacenamiento y las reglas para que ventas, finanzas y operaciones dejen de mantener copias privadas del mismo hecho. El resultado que el negocio ve suele ser un tablero de Power BI, pero la confiabilidad nace antes: en pipelines repetibles, en un modelo compartido y en una definición única de cliente, producto y periodo.',
                    'Muchas organizaciones llegan con extracciones manuales. Alguien descarga el ERP el viernes, otro pega columnas en Excel y un tercero corrige nombres de clientes a mano. Ese método no escala y no deja rastro cuando la cifra cambia. En Fabric reemplazamos esa cadena por un flujo que se puede volver a ejecutar, con registro de qué entró, qué se transformó y qué quedó rechazado. El equipo de datos deja de ser el cuello de botella de cada pedido de reporte.',
                    'El diseño parte de los usos. No se copia toda la base del ERP el primer día si la pregunta urgente es el margen y la cartera. Se eligen las entidades necesarias, se acuerda el grano y se documenta la calidad mínima: campos obligatorios, duplicados, fechas imposibles y códigos que no existen en el catálogo. Esas reglas se aplican en el pipeline, no en un comentario del analista. Lo que no cumple se aparta y se puede revisar, en lugar de mezclarse en silencio con el resto.',
                    'La integración contempla las fuentes que de verdad mueven la decisión: ERP, CRM, hojas que todavía son sistema de registro, archivos de bancos, APIs de proveedores y eventos operativos. Cada conector tiene horario, dueño y comportamiento ante un fallo. Si una fuente no llegó, el tablero no debe mostrar un cero que parece una caída real de ventas. Ese tipo de detalle operativo es el que separa un lago de datos abandonado de una plataforma que el negocio usa.',
                    'Fabric se conversa junto con Power BI y, cuando aplica, con Power Automate. El pipeline deja la tabla lista; el modelo semántico expone los indicadores; un flujo puede avisar si un control de calidad falla. Separar esas capas permite que un cambio en el origen no rompa diez reportes distintos. También permite que un analista de negocio cree una medida nueva sin reescribir la ingesta. CloudLab Latam deja esa frontera explícita en la documentación de la solución.',
                    'El gobierno de la información forma parte del entregable. Se define dónde viven los datos sensibles, quién puede leer el lakehouse, cómo se nombra cada tabla y cómo se pide acceso. La política de tratamiento de datos de la empresa, alineada con la normativa colombiana y con los estándares que el cliente ya deba cumplir, se traduce en decisiones técnicas concretas: qué se enmascara, qué se conserva y durante cuánto tiempo. No es un anexo jurídico desconectado de la arquitectura.',
                    'La transferencia de conocimiento es práctica. El equipo interno recorre un pipeline, entiende dónde cambiar una regla y sabe a quién avisar si una fuente cambia de estructura. No hace falta que toda la compañía sea ingeniera de datos, pero sí que exista más de una persona capaz de operar la plataforma. Dejamos diagramas, convenciones y un primer conjunto de controles que se pueden ampliar.',
                    'Un primer dominio de datos, con fuentes prioritarias y un camino claro hacia Power BI, se puede dejar funcionando en un ciclo de dos a cuatro semanas cuando el alcance está bien cortado. Si hoy cada área tiene su propia versión de la verdad, solicita una consulta gratuita y elegimos juntos el dominio que más duele: ventas, finanzas, inventario u otro que ya esté frenando decisiones.',
                ],
                'faqs' => $faq([
                    ['¿Fabric reemplaza a Power BI?', 'No. Fabric prepara y gobierna el dato. Power BI es la capa donde el negocio consulta. Se diseñan para trabajar juntos.'],
                    ['¿Hay que mover todos los sistemas el primer mes?', 'No. Se empieza por el dominio que sostiene las decisiones urgentes y se agregan fuentes cuando ese camino ya es estable.'],
                    ['¿Qué pasa si una fuente falla?', 'El pipeline lo registra y no convierte el fallo en un cero silencioso en el tablero. El equipo recibe una señal para corregir.'],
                    ['¿El equipo interno puede operarlo?', 'Sí. La entrega incluye el recorrido del pipeline, las convenciones y más de una persona capaz de operar la plataforma.'],
                ]),
            ],
            'en' => [
                'nav' => 'Microsoft Fabric',
                'h1' => 'Data engineering with Microsoft Fabric',
                'excerpt' => 'One platform to integrate, prepare, and govern the data that later feeds Power BI.',
                'meta_title' => 'Microsoft Fabric Data Engineering | CloudLab Latam',
                'meta_description' => 'We integrate and prepare company data on Microsoft Fabric so Power BI and the business share one trusted source. Request a free consultation.',
                'paragraphs' => [
                    'Microsoft Fabric is the foundation CloudLab Latam uses to unify company information. The data engineering service designs ingestion, transformation, storage, and the rules that stop sales, finance, and operations from keeping private copies of the same fact. What the business sees is often a Power BI dashboard, but reliability is created earlier: in repeatable pipelines, a shared model, and one definition of customer, product, and period.',
                    'Many organizations arrive with manual extracts. Someone downloads the ERP on Friday, someone else pastes columns into Excel, and a third person fixes customer names by hand. That method does not scale and leaves no trace when a figure changes. On Fabric we replace that chain with a flow that can be run again, with a record of what arrived, what was transformed, and what was rejected. The data team stops being the bottleneck of every report request.',
                    'The design starts from use. The entire ERP database is not copied on day one if the urgent question is margin and receivables. The necessary entities are chosen, the grain is agreed, and a minimum quality bar is written down: required fields, duplicates, impossible dates, and codes that do not exist in the catalog. Those rules run in the pipeline, not in an analyst comment. What fails is set aside and can be reviewed, instead of being mixed quietly into the rest.',
                    'Integration covers the sources that actually move decisions: ERP, CRM, spreadsheets that are still the system of record, bank files, supplier APIs, and operational events. Each connector has a schedule, an owner, and a behavior when it fails. If a source did not arrive, the dashboard must not show a zero that looks like a real drop in sales. That operational detail is what separates an abandoned data lake from a platform the business uses.',
                    'Fabric is planned together with Power BI and, when it applies, with Power Automate. The pipeline leaves the table ready; the semantic model exposes the metrics; a flow can warn if a quality check fails. Separating those layers means a change in the source does not break ten different reports. It also means a business analyst can add a measure without rewriting ingestion. CloudLab Latam makes that boundary explicit in the solution documentation.',
                    'Information governance is part of the deliverable. We define where sensitive data lives, who can read the lakehouse, how each table is named, and how access is requested. The company data-processing policy, aligned with Colombian regulation and with the standards the client must already meet, becomes concrete technical choices: what is masked, what is kept, and for how long. It is not a legal annex disconnected from the architecture.',
                    'Knowledge transfer is practical. The internal team walks through a pipeline, sees where to change a rule, and knows who to tell if a source changes shape. Not everyone in the company needs to be a data engineer, but more than one person must be able to operate the platform. We leave diagrams, conventions, and a first set of controls that can grow.',
                    'A first data domain, with priority sources and a clear path to Power BI, can be running in a two-to-four-week cycle when the scope is cut well. If every area still has its own version of the truth, request a free consultation and we will choose the domain that hurts most: sales, finance, inventory, or another that is already slowing decisions. Bring one painful report and its source. We will tell you whether Fabric is the right first step or whether a smaller Power BI model is enough for now. That keeps the first release small enough to operate.',
                ],
                'faqs' => $faq([
                    ['Does Fabric replace Power BI?', 'No. Fabric prepares and governs the data. Power BI is where the business looks it up. They are designed to work together.'],
                    ['Do we have to move every system in the first month?', 'No. We start with the domain that supports urgent decisions and add sources once that path is stable.'],
                    ['What happens if a source fails?', 'The pipeline records it and does not turn the failure into a silent zero on the dashboard. The team gets a signal to fix it.'],
                    ['Can the internal team operate it?', 'Yes. Delivery includes a walkthrough of the pipeline, the conventions, and more than one person able to operate the platform.'],
                ]),
            ],
        ],
        [
            'slug' => 'rpa',
            'icon' => 'bi-gear-fill',
            'keywords' => ['RPA', 'robot'],
            'es' => [
                'nav' => 'RPA',
                'h1' => 'Automatización robótica de procesos (RPA)',
                'excerpt' => 'Robots para los tramos que siguen en aplicaciones sin API, integrados con Power Automate cuando conviene.',
                'meta_title' => 'RPA y automatización de procesos | CloudLab Latam',
                'meta_description' => 'Implementamos RPA para tareas en aplicaciones sin API y lo combinamos con Power Automate y Power BI. Solicita una consulta gratuita.',
                'paragraphs' => [
                    'La automatización robótica de procesos entra cuando una tarea sigue ocurriendo en la pantalla de una aplicación que no ofrece una API estable. CloudLab Latam diseña robots para esos tramos: copiar datos entre un sistema legado y una hoja, descargar un reporte de un portal de proveedores, digitar movimientos que el ERP solo recibe por su interfaz, o completar un formulario interno que nadie va a reemplazar este año. El robot no es el destino de toda la operación. Es la pieza que cubre el hueco mientras el proceso se puede integrar de otra forma.',
                    'Antes de construir se mide. Cuántas veces al día ocurre la tarea, cuánto tarda, qué tan estables son las pantallas y qué pasa si un dato viene incompleto. Un proceso de diez minutos que ocurre dos veces al mes no justifica un robot. Un proceso de ocho minutos que ocurre doscientas veces por semana sí puede devolver tiempo real al equipo. Esa cuenta, hecha con el dueño del proceso, evita comprar automatización para un problema de volumen bajo o de reglas que cambian todos los días.',
                    'El diseño separa lo que el robot debe hacer de lo que una persona debe seguir decidiendo. Las excepciones no se esconden: si el portal cambió un botón, si el saldo no cuadra o si falta un soporte, el robot se detiene, deja evidencia y avisa. Un robot que adivina en silencio es peor que el proceso manual. Por eso las pruebas incluyen los casos felices y los casos en los que la pantalla o el dato no vienen como se esperaba. El equipo de negocio participa en esa prueba porque reconoce los errores que la tecnología no puede inventar.',
                    'RPA se combina con Power Automate cuando el proceso tiene las dos caras. El flujo de nube puede recibir el correo, guardar el archivo y pedir una aprobación. El robot entra solo en el sistema que no tiene conector. Al terminar, el flujo registra el resultado y Power BI puede mostrar cuántos casos resolvió el robot, cuántos volvieron a una persona y cuánto tiempo de ciclo se redujo. Esa combinación es la que CloudLab Latam recomienda cuando el cliente ya está en Microsoft Power Platform y no quiere una isla de robots desconectada del resto.',
                    'La operación del robot tiene que ser aburrida y predecible. Se define en qué máquina corre, con qué cuenta, en qué horario y quién lo reinicia. Las credenciales no quedan en un script suelto. Los cambios de pantalla se tratan como un cambio de sistema: se prueban antes de que el proceso diario se rompa. Dejamos una bitácora simple para que soporte sepa distinguir un fallo del robot de un fallo del sistema legado.',
                    'También se cuida el efecto en las personas. El objetivo declarado no es eliminar el equipo, sino quitar la digitación repetitiva para que ese tiempo pase a revisión, atención a clientes o cierre. Lo conversamos con el área desde el inicio. Un robot impuesto sin ese contexto se abandona en cuanto aparece el primer error. Un robot pedido por el equipo que sufre la tarea se defiende solo cuando hay que priorizar el mantenimiento.',
                    'La documentación incluye el mapa del proceso, los pasos del robot, las excepciones, los indicadores y la guía de operación. Con eso el cliente no queda atado a una sola persona de CloudLab Latam. Si más adelante el sistema legado gana una API, el tramo robótico se puede retirar y dejar el resto del proceso en Power Automate. Diseñamos con esa salida en mente para no convertir un parche temporal en una deuda permanente.',
                    'Un primer robot, sobre un proceso estable y con acceso a un ambiente de prueba, suele construirse en dos a cuatro semanas. Si tienes una tarea que solo se puede hacer frente a la pantalla, solicita una consulta gratuita. Revisamos si conviene RPA, un flujo de Power Automate o las dos cosas, y qué indicador va a demostrar que el tiempo volvió al equipo.',
                ],
                'faqs' => $faq([
                    ['¿Todo proceso debería ser un robot?', 'No. Si hay API o conector, Power Automate suele ser más simple de mantener. RPA cubre la pantalla que no se puede integrar de otra forma.'],
                    ['¿Qué pasa si la aplicación cambia de apariencia?', 'El robot se detiene y avisa. El cambio se prueba antes de volver a la operación, igual que un cambio de sistema.'],
                    ['¿Se puede medir el resultado?', 'Sí. Casos resueltos, casos devueltos a una persona y tiempo de ciclo pueden verse junto al resto de la operación, incluso en Power BI.'],
                    ['¿El equipo interno puede operarlo?', 'Sí. Dejamos horario, cuenta, bitácora y una guía para distinguir un fallo del robot de un fallo del sistema legado.'],
                ]),
            ],
            'en' => [
                'nav' => 'RPA',
                'h1' => 'Robotic process automation (RPA)',
                'excerpt' => 'Bots for the steps that still live in applications without an API, combined with Power Automate when it helps.',
                'meta_title' => 'RPA and Process Automation | CloudLab Latam',
                'meta_description' => 'We implement RPA for tasks in applications without an API and combine it with Power Automate and Power BI. Request a free consultation.',
                'paragraphs' => [
                    'Robotic process automation is used when a task still happens on the screen of an application that does not offer a stable API. CloudLab Latam designs bots for those stretches: copying data between a legacy system and a sheet, downloading a report from a supplier portal, typing movements the ERP only accepts through its interface, or completing an internal form nobody will replace this year. The bot is not the destination of the whole operation. It is the piece that covers the gap until the process can be integrated another way.',
                    'Before building, we measure. How many times a day the task happens, how long it takes, how stable the screens are, and what occurs if a field arrives incomplete. A ten-minute process that happens twice a month does not justify a bot. An eight-minute process that happens two hundred times a week can return real time to the team. That calculation, made with the process owner, avoids buying automation for a low-volume problem or for rules that change every day.',
                    'The design separates what the bot must do from what a person must still decide. Exceptions are not hidden: if the portal moved a button, if a balance does not match, or if a supporting document is missing, the bot stops, leaves evidence, and notifies someone. A bot that guesses in silence is worse than the manual process. Testing therefore includes the happy path and the cases where the screen or the data does not arrive as expected. The business team joins that test because they recognize errors technology cannot invent.',
                    'RPA is combined with Power Automate when the process has both faces. The cloud flow can receive the email, store the file, and request an approval. The bot enters only the system that has no connector. When it finishes, the flow records the result and Power BI can show how many cases the bot closed, how many returned to a person, and how much cycle time dropped. That combination is what CloudLab Latam recommends when the client is already on Microsoft Power Platform and does not want an island of bots disconnected from everything else.',
                    'Running the bot has to be boring and predictable. We define which machine it uses, which account, which schedule, and who restarts it. Credentials do not sit in a loose script. Screen changes are treated as a system change: they are tested before the daily process breaks. We leave a simple log so support can tell a bot failure from a failure of the legacy system.',
                    'The effect on people is part of the design. The stated goal is not to remove the team. It is to remove repetitive typing so that time moves to review, customer care, or closing. We talk about that with the area from the start. A bot imposed without that context is abandoned at the first error. A bot requested by the team that suffers the task defends its own maintenance when priorities compete.',
                    'Documentation includes the process map, the bot steps, the exceptions, the metrics, and the operating guide. The client is not tied to one person at CloudLab Latam. If the legacy system later gains an API, the robotic stretch can be removed and the rest of the process left on Power Automate. We design with that exit in mind so a temporary patch does not become permanent debt.',
                    'A first bot, on a stable process with access to a test environment, is usually built in two to four weeks. If you have a task that can only be done in front of the screen, request a free consultation. We will look at whether RPA, a Power Automate flow, or both are the right fit, and which metric will prove that time returned to the team.',
                ],
                'faqs' => $faq([
                    ['Should every process be a bot?', 'No. If an API or connector exists, Power Automate is usually simpler to maintain. RPA covers the screen that cannot be integrated another way.'],
                    ['What if the application changes appearance?', 'The bot stops and notifies someone. The change is tested before it returns to daily operation, the same as any system change.'],
                    ['Can the result be measured?', 'Yes. Cases closed, cases returned to a person, and cycle time can be seen with the rest of the operation, including in Power BI.'],
                    ['Can the internal team operate it?', 'Yes. We leave the schedule, the account, a log, and a guide to tell a bot failure from a legacy-system failure.'],
                ]),
            ],
        ],
    ],
];
