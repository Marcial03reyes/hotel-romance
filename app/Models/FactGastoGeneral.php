<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactGastoGeneral extends Model
{
    protected $table = 'fact_gastos_generales';
    protected $primaryKey = 'id_gasto';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_gasto',
        'id_met_pago',
        'monto',
        'fecha_gasto',
        'tipo_comprobante',    // ← NUEVO CAMPO
        'codigo_comprobante',  // ← NUEVO CAMPO
        'comprobante'
    ];

    // Relación con tipo de gasto
    public function tipoGasto()
    {
        return $this->belongsTo(DimTipoGasto::class, 'id_tipo_gasto', 'id_tipo_gasto');
    }

    // Relación con método de pago
    public function metodoPago()
    {
        return $this->belongsTo(DimMetPago::class, 'id_met_pago', 'id_met_pago');
    }

    // Accessor para obtener el nombre del tipo de gasto
    public function getTipoAttribute()
    {
        return $this->tipoGasto ? $this->tipoGasto->nombre : 'Sin tipo';
    }

    // Accessor para obtener el método de pago
    public function getMetPagoAttribute()
    {
        return $this->metodoPago ? $this->metodoPago->met_pago : 'Sin método';
    }

    // ← NUEVO: Accessor para verificar si tiene código de comprobante
    public function getTieneCodigoComprobanteAttribute()
    {
        return !empty($this->codigo_comprobante);
    }

    // Accessor para verificar si tiene comprobante
    public function getTieneComprobanteAttribute()
    {
        return !empty($this->comprobante) && file_exists(storage_path('app/public/' . $this->comprobante));
    }

    // Accessor para obtener la URL del comprobante
    public function getComprobanteUrlAttribute()
    {
        if ($this->comprobante) {
            return asset('storage/' . $this->comprobante);
        }
        return null;
    }
}