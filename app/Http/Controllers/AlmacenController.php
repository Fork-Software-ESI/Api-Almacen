<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Almacen;
use App\Models\Estanteria;


class AlmacenController extends Controller
{
    public function altaAlmacen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'direccion' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 400);
        }
        $infoValidada = $validator->validated();

        $almacen = Almacen::create($infoValidada);

        return response()->json(['message' => 'Almacen creado', 'datos' => $almacen], 201);

    }

    public function listarEstanterias($id)
    {
        $almacen = Almacen::find($id);

        if (!$almacen) {
            return response()->json(['message' => 'Almacén no encontrado'], 404);
        }

        $estanterias = Estanteria::where('almacen_id', $id)->get();

        return response()->json(['message' => 'Estanterías en el almacén', 'data' => $estanterias], 200);

    }
}