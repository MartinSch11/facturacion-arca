<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
        }

        body {
            width: 21cm;
            min-height: 29.7cm;
            font-size: 13px;
            display: flex;
            flex-direction: column;
        }

        .wrapper {
            border: 1.5px solid #333;
            padding: 5px;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .italic {
            font-style: italic;
        }

        .inline-block {
            display: inline-block;
        }

        .flex {
            display: flex;
            flex-wrap: wrap;
        }

        .no-margin {
            margin: 0;
        }

        .relative {
            position: relative;
        }

        .floating-mid {
            left: 0;
            right: 0;
            margin-left: auto;
            margin-right: auto;
            width: 75px;
            position: absolute;
            top: 1px;
            background: #fff;
        }

        .space-around {
            justify-content: space-around;
        }

        .space-between {
            justify-content: space-between;
        }

        .w50 {
            width: 50%;
        }

        th {
            border: 1px solid #000;
            background: #ccc;
            padding: 5px;
        }

        td {
            padding: 5px;
            font-size: 11px;
            vertical-align: top;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            height: 100%;
        }

        .text-20 {
            font-size: 20px;
        }

        .table-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .footer {
            margin-top: auto;
        }
    </style>
</head>

<body>
    {{--
    <div class="wrapper text-center bold text-20" style="width:100%;border-bottom: 0;">
        ORIGINAL
    </div>
    --}}

    <div class="flex relative">
        <div class="wrapper w50 flex" style="border-right: 0">
            <div style="padding-top: 2px; padding-left: 5px;">
                <img src="{{ asset('images/FundacionIPESMI.png') }}" alt="Logo" style="width:110px; height:30px;">
                <img src="{{ asset('images/LogoUGD.png') }}" alt="Otra Imagen" style="width:130px; height:30px;">
            </div>
            <p style="font-size: 13px;line-height: 1.5;margin-bottom: 0;align-self: flex-end;">
                <b>Fundación IPESMI</b>
                <br>Salta 1968
                <br>(CP. 3300) Posadas, Misiones
                <br>IVA Sujeto Exento
            </p>
        </div>
        <div class="wrapper inline-block w50">
            <h3 class="" style="font-size:24px;margin-bottom: 3px; padding-left: 60px;">FACTURA</h3>
            <p style="font-size: 13px;line-height: 1.5;margin-bottom: 0; padding-left: 60px;">
                <b>Punto de Venta: 1 Comp. Nro: {{ $comprobanteFormateado }}</b>
                <br><b>Fecha de Emisión: {{ $data['fechaComprobante'] }}</b>
                <br><b>CUIT: {{ $data['cuit'] }}</b>
                <br><b>Ingresos Brutos: 30683237740</b>
                <br><b>Fecha de Inicio de Actividades: 01/03/1995</b>
            </p>
        </div>
        <div class="wrapper floating-mid">
            <h3 class="no-margin text-center" style="font-size: 36px;">
            {{ $data['tipo_factura'] ?? 'C' }}
            </h3>
            <h5 class="no-margin text-center">COD.</h5>
        </div>
    </div>

    <div class="wrapper flex space-around" style="margin-top: 1px;">
        <span><b>Período Facturado Desde:</b> {{ $data['periodo_desde'] }}</span>
        <span><b>Hasta:</b> {{ $data['periodo_hasta'] }}</span>
        <span><b>Fecha de Vto. para el pago:</b> {{ $data['vto_pago'] }}</span>
    </div>
    <!-- detalle de pedido, obtener datos de la sesion del alumno-->
    <div class="wrapper" style="margin-top: 2px;font-size: 12px; padding-top:5px;">
        <div class="flex" style="margin-bottom: 5px;">
            <span style="width:40%"><b>DNI:</b> {{ $data['nroDoc'] }}</span>
            <span><b>Apellido y Nombre / Razón Social:</b> {{ $data['nombreApellido'] }}</span>
        </div>
        <div class="flex" style="flex-wrap: nowrap;margin-bottom: 5px;">
            <span style="width:40%"><b>Condición frente al IVA:</b> Consumidor</span>
            <span><b>Domicilio:</b> {{ $data['domicilio'] }}</span>
        </div>
        <div class="flex" style="flex-wrap: nowrap;margin-bottom: 5px;">
            <span style="width:40%"><b>Condición de venta:</b> Contado</span>
            <span><b>Forma de Pago:</b> {{ $data['formaPago'] }}</span>
        </div>
    </div>

    <!-- Contenedor de la tabla -->
    <div class="table-container">
        <table>
            <thead>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Producto / Servicio</th>
                <th class="text-center">Subtotal</th>
            </thead>
            <tbody>
                <tr style="height: 100%;"> <!-- Ocupa todo el espacio disponible -->
                    <td class="text-center" style="border: 1px solid black;">1</td>
                    <td class="text-center" style="border: 1px solid black;">{{ $data['concepto_nombre'] }}</td>
                    <td class="text-center" style="border: 1px solid black;">$<?php echo number_format($data['impTotal'], 2, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div style="width:100%;margin-top: 40px;" class="flex wrapper">
            <span class="text-right" style="width:70%"><b>Importe Total: $</b></span>
            <span class="text-right" style="width:20%"><b><?php echo number_format($data['impTotal'], 2, ',', '.'); ?></b></span>
        </div>
        <div class="flex relative" style="margin-top: 20px;">
            <div class="qr-container" style="padding: 0 20px 20px 20px;width: 20%;">
                @if (!empty($data['qrImage']))
                    <img src="data:image/png;base64,{{ $data['qrImage'] }}" alt="AFIP QR" style="max-width: 100%;">
                @endif
            </div>
            <div style="padding-left: 10px;width: 45%;">
                <img src="https://portalcf.cloud.afip.gob.ar/portal/app/frameworkAFIP/v1/img/logo_afip.png"
                    alt="Logo AFIP" style="max-width:130px; max-height: 50px;">
                <h4 class="italic bold">Comprobante Autorizado</h4>
                <p class="small italic bold" style="font-size: 9px;">Esta Administración Federal no se responsabiliza por los datos ingresados en el detalle de la operación</p>
            </div>
            <div class="flex" style="align-self: flex-start;width: 35%;">
                <span class="text-right" style="width:50%"><b>CAE N°: </b></span>
                <span class="text-left" style="padding-left: 10px;">{{ $cae }}</span>
                <span class="text-right" style="width:50%"><b>Vto CAE: </b></span>
                <span class="text-left" style="padding-left: 10px;">{{ $caeVto }}</span>
            </div>
        </div>
    </div>
</body>
</html>