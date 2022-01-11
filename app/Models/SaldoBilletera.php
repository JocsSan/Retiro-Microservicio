<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SaldoBilletera
 * @package App\Models
 */

class SaldoBilletera extends Model{
    protected $table = 'saldo_billetera';
    public $timestamps=false;
    protected $primaryKey="ID_BILLETERA";

    protected $fillable = [
        'ID_BILLETERA',
        'SALDO_BILLETERA',
        'USU_CRE',
        'FEC_CRE'
    ];
}


