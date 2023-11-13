<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GerenteLote
 * 
 * @property int $ID_Gerente
 * @property int $ID_Lote
 * 
 * @property GerenteAlmacen $gerente_almacen
 * @property Lote $lote
 *
 * @package App\Models
 */
class GerenteLote extends Model
{
	protected $table = 'gerente_lote';
	protected $primaryKey = 'ID_Lote';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Gerente' => 'int',
		'ID_Lote' => 'int'
	];

	protected $fillable = [
		'ID_Gerente',
        'ID_Lote'
	];

	public function gerente_almacen()
	{
		return $this->belongsTo(GerenteAlmacen::class, 'ID_Gerente');
	}

	public function lote()
	{
		return $this->belongsTo(Lote::class, 'ID_Lote');
	}
}