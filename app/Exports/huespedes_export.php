<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class huespedes_export implements FromCollection, WithHeadings, WithMapping
{
    protected $registros;
    
    public function __construct($registros)
    {
        $this->registros = $registros;
    }

    public function collection()
    {
        return $this->registros;
    }

    public function headings(): array
    {
        return [
            'Nro',
            'Nombre y Apellidos',
            'Sexo',
            'Edad',
            'Fecha Nac.',
            'Lugar Nac.',
            'Nacionalidad',
            'Documento de Identidad',
            'DOC.NRO',
            'Estado Civil',
            'Profesión/Ocupación',
            'Ciudad Procedencia',
            'Ciudad Destino',
            'Mot. Viaje',
            'N° Habitación',
            'Fecha Ingreso',
            'Hora Ingreso',
            'Fecha Salida',
            'Hora Salida',
            'Turno',
            'Método de Pago',
            'Monto',
            'Placa Vehículo',
            'Observaciones'
        ];
    }

    public function map($registro): array
    {
        static $contador = 0;
        $contador++;
        
        $docNro = preg_replace('/[^0-9]/', '', $registro->doc_identidad);
        $edad = $registro->fecha_nacimiento ? \Carbon\Carbon::parse($registro->fecha_nacimiento)->age : '';
        
        return [
            $contador,
            $registro->nombre_apellido ?: '',
            $registro->sexo ?: '',
            $edad,
            $registro->fecha_nacimiento ? \Carbon\Carbon::parse($registro->fecha_nacimiento)->format('d/m/Y') : '',
            $registro->lugar_nacimiento ?: '',
            $registro->nacionalidad ?: '',
            $registro->doc_identidad ?: '',
            $docNro,
            $registro->estado_civil ?: '',
            $registro->profesion_ocupacion ?: '',
            $registro->ciudad_procedencia ?: '',
            $registro->ciudad_destino ?: '',
            $registro->motivo_viaje ?: '',
            $registro->habitacion ?: '',
            $registro->fecha_ingreso_real ? \Carbon\Carbon::parse($registro->fecha_ingreso_real)->format('d/m/Y') : 
                ($registro->fecha_ingreso ? \Carbon\Carbon::parse($registro->fecha_ingreso)->format('d/m/Y') : ''),
            $registro->hora_ingreso_real ? \Carbon\Carbon::createFromFormat('H:i:s', $registro->hora_ingreso_real)->format('h:i A') :
                ($registro->hora_ingreso ? \Carbon\Carbon::createFromFormat('H:i:s', $registro->hora_ingreso)->format('h:i A') : ''),
            $registro->fecha_salida ? \Carbon\Carbon::parse($registro->fecha_salida)->format('d/m/Y') : '',
            $registro->hora_salida ? \Carbon\Carbon::createFromFormat('H:i:s', $registro->hora_salida)->format('h:i A') : '',
            $registro->turno == 0 ? 'DÍA' : ($registro->turno == 1 ? 'NOCHE' : ''),
            $registro->metodo_pago ?: '',
            $registro->precio ? number_format($registro->precio, 2) : '',
            $registro->placa_vehiculo ?: '',
            $registro->obs ?: ''
        ];
    }
}