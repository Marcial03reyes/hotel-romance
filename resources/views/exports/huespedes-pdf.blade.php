<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LIBRO DE HUESPEDES - HOSTAL ROMANCE</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 6px;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
            vertical-align: middle;
            font-size: 5px;
            height: 20px;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 5px;
            line-height: 1.1;
        }
        
        .col-nro { width: 2%; }
        .col-nombre { width: 8%; }
        .col-sexo { width: 2%; }
        .col-edad { width: 2%; }
        .col-fecha-nac { width: 4%; }
        .col-lugar-nac { width: 6%; }
        .col-nacionalidad { width: 4%; }
        .col-doc-identidad { width: 6%; }
        .col-doc-nro { width: 5%; }
        .col-estado-civil { width: 4%; }
        .col-profesion { width: 6%; }
        .col-ciudad-proc { width: 5%; }
        .col-ciudad-dest { width: 5%; }
        .col-motivo { width: 4%; }
        .col-habitacion { width: 3%; }
        .col-fecha-ing { width: 4%; }
        .col-hora-ing { width: 4%; }
        .col-fecha-sal { width: 4%; }
        .col-hora-sal { width: 4%; }
        .col-tarifa { width: 4%; }
        .col-placa { width: 5%; }
        .col-doc-nro { width: 4%; }
        
        .text-left { text-align: left; }
        .text-small { font-size: 4px; }
    </style>
</head>
<body>
    <div class="header">
        LIBRO DE HUESPEDES - Romance
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="col-nro">Nro</th>
                <th class="col-nombre">NOMBRE Y<br>APELLIDOS</th>
                <th class="col-sexo">SEXO</th>
                <th class="col-edad">EDAD</th>
                <th class="col-fecha-nac">FECHA<br>NAC.</th>
                <th class="col-lugar-nac">LUGAR<br>NAC.</th>
                <th class="col-nacionalidad">NACION.</th>
                <th class="col-doc-identidad">DOCUMENTO<br>DE<br>IDENTIDAD</th>
                <th class="col-doc-nro">DOC.<br>NRO</th>
                <th class="col-estado-civil">ESTADO<br>CIVIL</th>
                <th class="col-profesion">PROFESION/<br>OCUPACION</th>
                <th class="col-ciudad-proc">CIUDAD<br>PROCEDENCIA</th>
                <th class="col-ciudad-dest">CIUDAD<br>DESTINO</th>
                <th class="col-motivo">MOT.<br>VIAJE</th>
                <th class="col-habitacion">N°<br>HAB.</th>
                <th class="col-fecha-ing">FECHA<br>INGRESO</th>
                <th class="col-hora-ing">HORA<br>INGRESO</th>
                <th class="col-fecha-sal">FECHA<br>SALIDA</th>
                <th class="col-hora-sal">HORA<br>SALIDA</th>
                <th class="col-tarifa">TARIFA</th>
                <th class="col-placa">PLACA<br>VEHICULO</th>
            </tr>
        </thead>
        <tbody>
            @php $contador = 1; @endphp
            @foreach($registros as $registro)
            <tr>
                <td>{{ $contador++ }}</td>
                <td class="text-left text-small">{{ $registro->nombre_apellido ?: '' }}</td>
                <td>{{ $registro->sexo ?: '' }}</td>
                <td>{{ $registro->fecha_nacimiento ? \Carbon\Carbon::parse($registro->fecha_nacimiento)->age : '' }}</td>
                <td>{{ $registro->fecha_nacimiento ? \Carbon\Carbon::parse($registro->fecha_nacimiento)->format('d/m/Y') : '' }}</td>
                <td class="text-small">{{ $registro->lugar_nacimiento ?: '' }}</td>
                <td class="text-small">{{ $registro->nacionalidad ?: '' }}</td>
                <td class="text-small">{{ $registro->doc_identidad ?: '' }}</td>
                <td>{{ preg_replace('/[^0-9]/', '', $registro->doc_identidad) }}</td>
                <td class="text-small">{{ $registro->estado_civil ?: '' }}</td>
                <td class="text-small">{{ $registro->profesion_ocupacion ?: '' }}</td>
                <td class="text-small">{{ $registro->ciudad_procedencia ?: '' }}</td>
                <td class="text-small">{{ $registro->ciudad_destino ?: '' }}</td>
                <td class="text-small">{{ $registro->motivo_viaje ?: '' }}</td>
                <td>{{ $registro->habitacion ?: '' }}</td>
                <td>{{ $registro->fecha_ingreso_real ? \Carbon\Carbon::parse($registro->fecha_ingreso_real)->format('d/m/Y') : 
                    ($registro->fecha_ingreso ? \Carbon\Carbon::parse($registro->fecha_ingreso)->format('d/m/Y') : '') }}</td>
                <td>{{ $registro->hora_ingreso_real ? \Carbon\Carbon::createFromFormat('H:i:s', $registro->hora_ingreso_real)->format('h:i A') :
                    ($registro->hora_ingreso ? \Carbon\Carbon::createFromFormat('H:i:s', $registro->hora_ingreso)->format('h:i A') : '') }}</td>
                <td>{{ $registro->fecha_salida ? \Carbon\Carbon::parse($registro->fecha_salida)->format('d/m/Y') : '' }}</td>
                <td>{{ $registro->hora_salida ? \Carbon\Carbon::createFromFormat('H:i:s', $registro->hora_salida)->format('h:i A') : '' }}</td>
                <td>{{ $registro->precio ? 'S/ '.number_format($registro->precio, 2) : '' }}</td>
                <td>{{ $registro->placa_vehiculo ?: '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>