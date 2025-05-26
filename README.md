Servicio AfipService

1. Constructor (__construct)
Define las rutas y URLs necesarias para interactuar con el WSFE de la AFIP.

taPath: Ruta al archivo TA.xml (Ticket de Acceso).

wsfeWsdl: Ruta al archivo wsfe.wsdl.

wsfeUrl: URL del servicio WSFE.

2. Método getTokenAndSign
Lee el archivo TA.xml y extrae el token y la firma necesarios para autenticarse en el WSFE.

3. Método facturar
Obtiene el último número de comprobante desde la base de datos.

Incrementa el número de comprobante.

Actualiza los datos de la factura con el nuevo número.

Realiza la solicitud al WSFE para generar la factura.

Actualiza el último número de comprobante en la base de datos.

4. Métodos obtenerUltimoNumero y actualizarUltimoNumero
obtenerUltimoNumero: Obtiene el último número de comprobante desde la tabla ultimos_numeros.

actualizarUltimoNumero: Actualiza el último número de comprobante en la tabla ultimos_numeros.

<p>Confusión sobre los Controladores y Comandos<p>
Tienes dos controladores y un comando que hacen prácticamente lo mismo. Esto puede generar confusión y duplicación de código. Vamos a aclarar su propósito y cómo organizarlos.

FacturarCommand
Propósito: Este comando se ejecuta desde la línea de comandos (CLI) y se usa para generar facturas de manera manual o programada (por ejemplo, con un cron job).

Ubicación: app/Console/Commands/FacturarCommand.php.

Uso: Ejecutas php artisan afip:facturar para generar una factura.

FacturaController
Propósito: Este controlador se usa para generar facturas a través de una solicitud HTTP (por ejemplo, desde un formulario en tu aplicación web).

Ubicación: app/Http/Controllers/FacturaController.php.

Uso: Accedes a una ruta (por ejemplo, /facturar) para generar una factura.
