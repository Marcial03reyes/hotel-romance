<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactCompraBodega extends Model
{
    protected $table = 'fact_compra_bodega';
    protected $primaryKey = 'id_compra_bodega';
    public $timestamps = true;

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_prod_bod',
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
     * Relación con el producto de bodega
     */
    public function productoBodega()
    {
        return $this->belongsTo(DimProductoBodega::class, 'id_prod_bod', 'id_prod_bod');
    }

    /**
     * Accessor para el nombre del producto
     */
    public function getNombreProductoAttribute()
    {
        return $this->productoBodega?->nombre;
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
        return $query->orderByDesc('fecha_compra')->orderByDesc('id_compra_bodega');
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