<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactCompraInterna extends Model
{
    protected $table = 'fact_compra_interna';
    protected $primaryKey = 'id_compra_interna'; // Campo correcto
    public $timestamps = true; // Cambiado a true para tracking de fechas

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_prod_bod', // Campo correcto
        'cantidad', 
        'precio_unitario',
        'fecha_compra',
        'proveedor'
    ];

    // Conversión de tipos
    protected $casts = [
        'fecha_compra' => 'date',
        'precio_unitario' => 'decimal:2',
        'cantidad' => 'integer'
    ];

    /**
     * Relación con el producto de hotel
     */
    public function producto()
    {
        return $this->belongsTo(DimProductoHotel::class, 'id_prod_bod', 'id_prod_hotel');
    }

    /**
     * Accessor para el nombre del producto
     */
    public function getNombreProductoAttribute()
    {
        return $this->producto?->nombre;
    }

    /**
     * Accessor para el total de la compra
     */
    public function getTotalCompraAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    /**
     * Scope para ordenar por fecha más reciente
     */
    public function scopeRecientes($query)
    {
        return $query->orderByDesc('fecha_compra')->orderByDesc('created_at');
    }

    /**
     * Scope para filtrar por producto
     */
    public function scopeDelProducto($query, $idProducto)
    {
        return $query->where('id_prod_bod', $idProducto);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_compra', [$fechaInicio, $fechaFin]);
    }
}