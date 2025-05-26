<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-10 bg-gray-100 border-black">
    <div class="max-w-4xl mx-auto bg-white p-8 border border-gray-300">
        <!-- Encabezado -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-xl font-bold">Apellido y Nombre</h1>
                <p class="text-sm">Rubro / Matrícula (opcional)</p>
                <p class="text-sm">Dirección</p>
                <p class="text-sm">Tel. (opcional)</p>
                <p class="text-sm">e-mail (opcional)</p>
                <p class="text-sm">Web (opcional)</p>
                <p class="text-sm mt-2">IVA Responsable Inscripto</p>
            </div>
            <div class="text-center border border-black p-2">
                <h2 class="text-2xl font-bold">A</h2>
                <p class="text-xs">Código N° 01</p>
            </div>
            <div class="text-right">
                <h1 class="text-2xl font-bold">FACTURA</h1>
                <p class="text-sm">N° <?php echo $data['cbteDesde']; ?> - <?php echo $data['cbteHasta']; ?></p>
                <div class="border border-black w-32 h-10 mt-2"></div>
                <p class="text-xs mt-1">FECHA: <?php echo $data['fechaComprobante']; ?></p>
                <p class="text-xs">C.U.I.T.: <?php echo $data['cuit']; ?></p>
                <p class="text-xs">ING. BRUTOS: Reg. Simpl.</p>
                <p class="text-xs">INICIO DE ACT.: 11/2013</p>
            </div>
        </div>

        <!-- Información del cliente -->
        <div class="border-t border-black pt-2 mb-4">
            <p class="text-sm"><strong>Señores:</strong> <?php echo $data['nroDoc']; ?></p>
            <p class="text-sm"><strong>Dirección:</strong> _____________________</p>
            <p class="text-sm"><strong>Localidad:</strong> _____________________</p>
            <p class="text-sm"><strong>I.V.A. Responsable Inscripto</strong></p>
            <p class="text-sm"><strong>C.U.I.T.:</strong> _____________________</p>
        </div>

        <!-- Condiciones de venta -->
        <div class="border-t border-black pt-2 mb-4">
            <p class="text-sm"><strong>Condiciones de Venta:</strong>
                <span class="ml-4">Contado <input type="checkbox"></span>
                <span class="ml-4">Cta. Cte. <input type="checkbox"></span>
                <span class="ml-4">Remito N° ________________</span>
            </p>
        </div>

        <!-- Tabla de productos -->
        <table class="w-full border border-black text-sm mb-4">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black p-2">CANT.</th>
                    <th class="border border-black p-2">DESCRIPCIÓN</th>
                    <th class="border border-black p-2">P. UNITARIO</th>
                    <th class="border border-black p-2">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-black p-2">1</td>
                    <td class="border border-black p-2">Producto/Servicio</td>
                    <td class="border border-black p-2">$<?php echo number_format($data['impNeto'], 2, ',', '.'); ?></td>
                    <td class="border border-black p-2">$<?php echo number_format($data['impNeto'], 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="border border-black p-2 text-right">SUBTOTAL:</td>
                    <td class="border border-black p-2">$<?php echo number_format($data['impNeto'], 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="border border-black p-2 text-right">IMPUESTO:</td>
                    <td class="border border-black p-2">$<?php echo number_format($data['impIVA'], 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="border border-black p-2 text-right font-bold">TOTAL:</td>
                    <td class="border border-black p-2 font-bold">$<?php echo number_format($data['impTotal'], 2, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Información adicional -->
        <div class="border-t border-black pt-2 text-xs text-center">
            <p>C.A.I. <?php echo $cae; ?> | VTO. <?php echo $caeVto; ?></p>
        </div>
    </div>
</body>
</html>
