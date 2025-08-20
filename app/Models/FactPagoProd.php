<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactPagoProd extends Model
{
    protected $table = 'fact_pago_prod';
    protected $primaryKey = 'id_compra';
    public $timestamps = false;

    // ✅ CAMPOS PERMITIDOS PARA ASIGNACIÓN MASIVA
    protected $fillable = [
        'id_estadia',
        'id_prod_bod',
        'cantidad',
        'precio_unitario',
        'id_met_pago',
        'comprobante' // ✅ NUEVO CAMPO AGREGADO
    ];

    // ✅ RELACIONES EXISTENTES
    public function estadia()
    {
        return $this->belongsTo(FactRegistroCliente::class, 'id_estadia', 'id_estadia');
    }

    public function producto()
    {
        return $this->belongsTo(DimProductoBodega::class, 'id_prod_bod', 'id_prod_bod');
    }

    public function metodoPago()
    {
        return $this->belongsTo(DimMetPago::class, 'id_met_pago', 'id_met_pago');
    }

    // ✅ ACCESSORS ÚTILES PARA LA VISTA
    
    /**
     * Calcular el total (cantidad × precio unitario)
     */
    public function getTotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    /**
     * Obtener el nombre del producto de forma rápida
     */
    public function getProductoNombreAttribute()
    {
        return $this->producto ? $this->producto->nombre : 'Producto no encontrado';
    }

    /**
     * Obtener el método de pago de forma rápida
     */
    public function getMetPagoAttribute()
    {
        return $this->metodoPago ? $this->metodoPago->met_pago : 'Sin método';
    }

    /**
     * Verificar si tiene comprobante
     */
    public function getTieneComprobanteAttribute()
    {
        return $this->comprobante === 'SI';
    }

    /**
     * Obtener el total formateado para mostrar
     */
    public function getTotalFormateadoAttribute()
    {
        return 'S/ ' . number_format($this->total, 2);
    }

    // ✅ SCOPES ÚTILES PARA CONSULTAS

    /**
     * Scope para obtener solo consumos con comprobante
     */
    public function scopeConComprobante($query)
    {
        return $query->where('comprobante', 'SI');
    }

    /**
     * Scope para obtener solo consumos sin comprobante
     */
    public function scopeSinComprobante($query)
    {
        return $query->where('comprobante', 'NO');
    }

    /**
     * Scope para obtener consumos de una estadía específica
     */
    public function scopeDeEstadia($query, $idEstadia)
    {
        return $query->where('id_estadia', $idEstadia);
    }

    /**
     * Scope para obtener consumos por método de pago
     */
    public function scopePorMetodoPago($query, $idMetodoPago)
    {
        return $query->where('id_met_pago', $idMetodoPago);
    }
}