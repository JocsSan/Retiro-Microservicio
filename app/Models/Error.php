<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Error extends Model{
    protected $table = 'errores';
    protected $fillable =[
        'ID_ERROR',
        'MENSAJE_ERROR',
        'USU_CRE',
        'FEC_CRE'
        ];
}
