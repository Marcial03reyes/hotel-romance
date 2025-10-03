<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactPagoProd extends Model
{
    protected $table = 'fact_pago_prod';
    protected $primaryKey = 'id_compra';
    public $timestamps = false;

    // ✅ CAMPOS ACTUALIZADOS PARA ASIGNACIÓN MASIVA
    protected $fillable = [
        'id_estadia',
        'id_prod_bod',
        'cantidad',
        'precio_unitario', // Se llenará automáticamente desde producto
        'id_met_pago',
        'comprobante',
        'fecha_venta',    
        'turno'          
    ];

    // ✅ RELACIONES
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

    // ✅ ACCESSORS

    /**
     * Calcular el total (cantidad × precio unitario)
     */
    public function getTotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    /**
     * Obtener el nombre del producto
     */
    public function getProductoNombreAttribute()
    {
        return $this->producto ? $this->producto->nombre : 'Producto no encontrado';
    }

    /**
     * Obtener el método de pago
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
     * Obtener el total formateado
     */
    public function getTotalFormateadoAttribute()
    {
        return 'S/ ' . number_format($this->total, 2);
    }

    /**
     * Obtener nombre del turno
     */
    public function getTurnoNombreAttribute()
    {
        return $this->turno == 0 ? 'DÍA' : 'NOCHE';
    }

    /**
     * Obtener fecha formateada
     */
    public function getFechaFormateadaAttribute()
    {
        return \Carbon\Carbon::parse($this->fecha_venta)->format('d/m/Y');
    }

    // ✅ SCOPES

    /**
     * Scope para filtrar por turno
     */
    public function scopePorTurno($query, $turno)
    {
        if ($turno !== null && $turno !== '') {
            return $query->where('turno', $turno);
        }
        return $query;
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_venta', $fecha);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para ventas de bodega (sin cliente asociado)
     */
    public function scopeVentasBodega($query)
    {
        return $query->whereNull('id_estadia');
    }

    /**
     * Scope con comprobante
     */
    public function scopeConComprobante($query)
    {
        return $query->where('comprobante', 'SI');
    }

    /**
     * Scope sin comprobante
     */
    public function scopeSinComprobante($query)
    {
        return $query->where('comprobante', 'NO');
    }

    /**
     * Scope por método de pago
     */
    public function scopePorMetodoPago($query, $idMetodoPago)
    {
        return $query->where('id_met_pago', $idMetodoPago);
    }
}