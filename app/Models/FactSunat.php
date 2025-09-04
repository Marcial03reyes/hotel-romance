<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactSunat extends Model
{
    protected $table = 'fact_sunat';
    protected $primaryKey = 'id_sunat';
    public $timestamps = true;

    protected $fillable = [
        'tipo_comprobante',
        'codigo_comprobante',
        'monto',
        'fecha_comprobante',
        'archivo_comprobante'
    ];

    protected $casts = [
        'fecha_comprobante' => 'date',
        'monto' => 'decimal:2'
    ];

    // Accessor para verificar si tiene archivo comprobante
    public function getTieneArchivoAttribute()
    {
        return !empty($this->archivo_comprobante) && file_exists(storage_path('app/public/' . $this->archivo_comprobante));
    }

    // Accessor para obtener la URL del archivo comprobante
    public function getArchivoUrlAttribute()
    {
        if ($this->archivo_comprobante) {
            return asset('storage/' . $this->archivo_comprobante);
        }
        return null;
    }

    // Accessor para obtener el tipo de comprobante formateado
    public function getTipoComprobanteFormateadoAttribute()
    {
        return ucfirst(strtolower($this->tipo_comprobante));
    }

    // Accessor para verificar si requiere código
    public function getRequiereCodigoAttribute()
    {
        return in_array($this->tipo_comprobante, ['BOLETA', 'FACTURA']);
    }

    // Scope para filtrar por tipo de comprobante
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_comprobante', $tipo);
    }

    // Scope para filtrar por rango de fechas
    public function scopePorRangoFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_comprobante', [$fechaInicio, $fechaFin]);
    }

    // Scope para obtener comprobantes del mes actual
    public function scopeDelMesActual($query)
    {
        return $query->whereMonth('fecha_comprobante', now()->month)
                    ->whereYear('fecha_comprobante', now()->year);
    }

    // Scope para obtener comprobantes con archivo
    public function scopeConArchivo($query)
    {
        return $query->whereNotNull('archivo_comprobante');
    }
}