<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GerenteForma
 * 
 * @property int $ID_Gerente
 * @property int $ID_Paquete
 * 
 * @property GerenteAlmacen $gerente_almacen
 * @property Forma $forma
 *
 * @package App\Models
 */
class GerenteForma extends Model
{
	protected $table = 'gerente_forma';
	protected $primaryKey = 'ID_Paquete';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Gerente' => 'int',
		'ID_Paquete' => 'int'
	];

	protected $fillable = [
		'ID_Gerente',
        'ID_Paquete'
	];

	public function gerente_almacen()
	{
		return $this->belongsTo(GerenteAlmacen::class, 'ID_Gerente');
	}

	public function forma()
	{
		return $this->belongsTo(Forma::class, 'ID_Paquete');
	}
}