<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Billetera extends Model{
    protected $table="billeteras";
    public $timestamps=false;
    protected $primaryKey="ID_BILLETERA";
    protected $fillable = [
        'ID_BILLETERA',
        'FECHA_CREACION',
        'BILLETERA_ASIGNADA',
        'USU_CRE',
        'FEC_CRE',
        'USU_MOD',
        'FEC_MOD'
    ];
}
