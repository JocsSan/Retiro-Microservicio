<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MovimientoBilletera extends Model{
    protected $table = 'movimientos_billeteras';
    public $timestamps=false;

    protected $fillable = [

        'FECHA_MOVIMIENTO',
        'ID_TRANSACCION',
        'MONTO_TRANSACCION',
        'SALDO_ANTERIOR',
        'SALDO_POSTERIOR',
        'USU_CRE',
        'FEC_CRE'
    ];
}
