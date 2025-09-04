<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactInversion extends Model
{
    use HasFactory;

    protected $table = 'fact_inversiones';
    protected $primaryKey = 'id_inversion';
    public $timestamps = false;
    
    protected $fillable = [
        'detalle',
        'monto',
        'id_met_pago',
        'fecha_inversion'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_inversion' => 'date'
    ];

    /**
     * Relación con método de pago
     */
    public function metodoPago()
    {
        return $this->belongsTo(DimMetPago::class, 'id_met_pago', 'id_met_pago');
    }
}