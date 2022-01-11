<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends  Model
{
    protected $table="transacciones";
    public $timestamps=false;
    protected $primaryKey="ID_TRANSACCION";
    protected $fillable=[
        'ID_TRANSACCION',
        'FECHA_TRANSACCION',
        'ID_BILLETERA',
        'TIPO_TRANSACCION',
        'ID_SERVICIO',
        'ESTADO_TRANSACCION',
        'ID_ERROR',
        'USU_CRE',
        'FEC_CRE',
        'USU_MOD',
        'FEC_MOD'
    ];
}
