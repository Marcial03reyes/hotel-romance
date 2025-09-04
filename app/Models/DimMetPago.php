<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimMetPago extends Model
{
    protected $table = 'dim_met_pago';
    protected $primaryKey = 'id_met_pago';
    public $timestamps = false;

    protected $fillable = ['met_pago'];
    
    public function pagosHab()
    {
        return $this->hasMany(FactPagoHab::class, 'id_met_pago');
    }

    public function pagosProd()
    {
        return $this->hasMany(FactPagoProd::class, 'id_met_pago');
    }

    public function gastosGenerales()
    {
        return $this->hasMany(FactGastoGeneral::class, 'id_met_pago');
    }
    
    // Relación con los pagos de gastos fijos
    public function pagosGastosFijos()
    {
        return $this->hasMany(FactPagoGastoFijo::class, 'id_met_pago', 'id_met_pago');
    }

    // Relación con inversiones (si existe)
    public function inversiones()
    {
        return $this->hasMany(FactInversion::class, 'id_met_pago');
    }

    // ===== MÉTODOS ÚTILES =====

    // Obtener el total de transacciones para este método de pago
    public function getTotalTransaccionesAttribute()
    {
        return $this->pagosHab()->count() + 
               $this->pagosProd()->count() + 
               $this->gastosGenerales()->count() +
               $this->pagosGastosFijos()->count();
    }

    // Obtener el monto total manejado por este método de pago
    public function getMontoTotalAttribute()
    {
        $totalHabitaciones = $this->pagosHab()->sum('monto');
        $totalProductos = $this->pagosProd()->sum(\DB::raw('cantidad * precio_unitario'));
        $totalGastosGenerales = $this->gastosGenerales()->sum('monto');
        $totalGastosFijos = $this->pagosGastosFijos()->sum('monto_pagado');
        
        return $totalHabitaciones + $totalProductos + $totalGastosGenerales + $totalGastosFijos;
    }

    // Scope para obtener métodos de pago más utilizados
    public function scopeMasUtilizados($query)
    {
        return $query->withCount([
            'pagosHab',
            'pagosProd', 
            'gastosGenerales',
            'pagosGastosFijos'
        ])->orderByDesc('pagos_hab_count')
          ->orderByDesc('pagos_prod_count');
    }
}