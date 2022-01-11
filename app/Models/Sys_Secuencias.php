<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
 *@property string NOMBRE_TABLA;
 *@property int INCREMENTO;
 *@property int SECUENCIA_ANTERIOR;
 *@property int SECUENCIA_SIGUIENTE;

**/

class Sys_Secuencias extends Model{
    protected $table = "sys_secuencias";
    public $timestamps=false;
}
