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

        .wrapper2 {
            border: 1.5px solid #333;
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
            border-bottom: 1px solid #000;
            padding-top: 5px;
            padding-bottom: 1px;
            font-size: 12px
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
    <div class="flex relative">
        <div class="wrapper w50" style="border-right: 0; text-align: left;">
            <div style="text-align: center;">
                <img src="https://www.nuevaescuela.edu.ar/Nueva%20Escuela__LOGO-color.png" alt="Logo"
                    style="width:100px; height:100px;">
            </div>
            <div style="padding-left: 10px;">
                <p style="font-size: 12px; line-height: 1.5; margin-bottom: 0;">
                    Nueva Escuela S.A.<br>
                    Callao 67 (1022)<br>
                    Cdad. de Bs.As.<br>
                    IVA: RESP.INSCRIPTO
                </p>
            </div>
        </div>

        <div class="wrapper inline-block w50">
            <div style="text-align: center; padding-top: 20px;">
                <p style="font-size: 12px; margin: 0;">FACTURA <br> ORIGINAL</p>
                <p style="margin: 0;">N°: {{ $comprobanteFormateado }}</p>
            </div>

            <p style="font-size: 12px;line-height: 1.5;margin-bottom: 0;">
                <br>Fecha de Emisión: {{ $data['fechaComprobante'] }}
                <br>Lugar: Callao 67(1022)
                <br>Cdad. de Bs.As.
                <br>CUIT: {{ $data['cuit'] }} <span style="padding-left: 40px">IIBB: 871308-01</span>
                <br>Inicio de Actividad: 01/08/1992
            </p>
        </div>
        <div class="wrapper floating-mid">
            <h3 class="no-margin text-center" style="font-size: 12px;">
                {{ $data['tipo_factura'] ?? 'C' }}
            </h3>
            <h5 class="no-margin text-center">COD. {{ $data['cod_factura'] }}</h5>
        </div>
    </div>
    <div style="font-size: 12px; border-left: 1px solid black; border-right: 1px solid black; padding: 10px 15px;">
        <!-- Nombre y Dirección -->
        <div style="margin-bottom: 4px;">Sr.(s): {{ $data['nombreApellido'] }}</div>
        <div style="margin-bottom: 4px;">Dirección: {{ $data['domicilio'] }}</div>

        <!-- Localidad y Provincia -->
        <div style="display: flex; margin-bottom: 4px;">
            <span style="width: 33.33%;">Localidad: {{ $data['localidad'] }}</span>
            <span style="width: 33.33%;">Provincia: {{ $data['provincia'] }}</span>
            <span style="width: 33.33%;"></span>
        </div>

        <!-- IVA - DNI - Mes/Cuota -->
        <div style="display: flex; margin-bottom: 4px;">
            <span style="width: 33.33%;">IVA: CONS.FINAL - cursos oficiales</span>
            <span style="width: 33.33%;">DNI: {{ $data['nroDoc'] }}</span>
            <span style="width: 33.33%;">Mes: {{ $data['mes_numero'] }} Cuota: {{ $data['mes_nombre'] }}</span>
        </div>

        <!-- Curso y Fecha Vto -->
        <div style="display: flex;">
            <span style="width: 33.33%;"></span>
            <span style="width: 33.33%;">Curso: {{ $data['codigoCurso'] }}</span>
            <span style="width: 33.33%;">Fecha de Vencimiento de Pago: {{ $data['vto_pago'] }}</span>
        </div>
    </div>


    <!-- Contenedor de la tabla -->
    <div class="table-container wrapper2">
        <table>
            <thead>
                <th class="text-center">Artículo</th>
                <th class="text-center">Unid.</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Cant.</th>
                <th class="text-center">% Bonificación</th>
                <th class="text-center" style="border-left: 1px solid black; border-right: 1px solid black">Precio</th>
                <th class="text-center">Importe</th>
            </thead>
            <tbody>
                <tr style="height: 100%;">
                    <td class="text-center">0210</td>
                    <td class="text-center">u</td>
                    <td class="text-center">{{ $data['concepto_nombre'] }}</td>
                    <td class="text-center">1</td>
                    <td class="text-center"></td>
                    <td class="text-center" style="border-left: 1px solid black">
                        ${{ number_format($data['impTotal'], 2, '.', '') }}
                    <td class="text-center" style="border-left: 1px solid black;">
                        ${{ number_format($data['impTotal'], 2, '.', '') }}
                </tr>
            </tbody>
        </table>
    </div>

    <div
        style="font-size: 12px; padding: 10px; border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black">
        <!-- Total numérico y letras -->
        <div style="text-align: right; margin-bottom: 10px; padding-top: 120px;">
            <p style="margin: 0; padding-right: 10px;">
                <span style="display: inline-block; min-width: 80px; text-align: right;">
                    Total:
                </span>
                <span style="display: inline-block; padding-left: 50px;">
                    ${{ number_format($data['impTotal'], 2, '.', '') }}
                </span>
            </p>
            <p style="margin: 5px; font-size: 11px; margin-right: 200px;">
                Es un total de: {{ $data['totalEnLetras'] }}
            </p>
        </div>

        <!-- Forma de pago y observaciones -->
        <div style="text-align: left; padding-bottom: 90px">
            <p style="margin: 0; font-weight: bold; padding-left: 10px;">
                {{ $data['formaPago'] }}
                <span style="margin-left: 100px;">
                    ${{ number_format($data['impTotal'], 2, '.', '') }}
                </span>
            </p>
        </div>
        <p style="padding-left: 10px;">Observaciones AFIP</p>
    </div>


    <div class="footer" style="display: flex; justify-content: space-between; align-items: flex-start; padding: 10px;">
        <!-- Parte izquierda: datos del CAE -->
        <div style="font-size: 12px; padding-top: 10px;">
            <p>Transporte</p>
            <span>C.A.E. Nº {{ $cae }} Fecha Vto. CAE: {{ $caeVto }}</span><br>
            <img src="https://c3.klipartz.com/pngpicture/194/50/sticker-png-bar-code-barcode-thumbnail.png"
                alt="Código de barras" style="width: 300px; height: 40px;">
            <p>3065858947006000275150638875700202504257</p>
            <p>147 Teléfono Gratuito CABA, Area de Defensa y Protección al Consumidor</p>
        </div>

        <!-- Parte derecha: QR -->
        <div class="qr-container" style="text-align: center;">
            @if (!empty($data['qrImage']))
                <img src="data:image/png;base64,{{ $data['qrImage'] }}" alt="AFIP QR" style="width: 100px;">
            @else
                <img src="https://www.sistemasagiles.com.ar/soft/pyafipws/qr_afip_lgiepn.png" alt="" style="width: 100px;">
            @endif
            <p style=" font-size: 12px;">Página 1 de 1</p>
        </div>
    </div>

</body>

</html>