<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BilleteraCliente extends Model{
    protected $table="billeteras_clientes";
    public $timestamps=false;

    protected $fillable = [
        'ID_CLIENTE',
        'ID_BILLETERA',
        'USU_CRE',
        'FEC_CRE',
        'USU_MOD',
        'FEC_MOD'
    ];
}


