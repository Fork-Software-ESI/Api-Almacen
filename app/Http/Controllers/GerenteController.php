<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Paquete;
use App\Models\PaqueteEstante;
use App\Models\Estante;

class GerenteController extends Controller
{
    public function validarDireccion($direccion)
    {
        $apiKey = '7a6TfdGhaJbpPMG2ehCfSExHYsnzdkIb5a0YlJzjU5U';
        $address = urlencode($direccion);
        $countryName = "Uruguay";
        $url = "https://geocode.search.hereapi.com/v1/geocode?q=$address&apiKey=$apiKey&country=$countryName";

        $response = @file_get_contents($url);
        $data = json_decode($response);

        if ($data != null && !empty($data->items)) {
            if (count($data->items) > 1) {
                return response()->json(['error' => 'Múltiples direcciones', 'Direcciones' => $data], 400);
            }

            $addressDetails = $data->items[0]->address;

            if (
                isset($addressDetails->street) &&
                isset($addressDetails->houseNumber) &&
                isset($addressDetails->city)
            ) {
                return true;
            } else {
                return response()->json(['error' => 'Dirección incompleta'], 400);
            }
        } else {
            return response()->json(['error' => 'Dirección inválida'], 400);
        }
    }

    public function crearPaquete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Descripcion' => 'string',
            'Peso_Kg' => 'required|numeric|min:1',
            'ID_Cliente' => 'required|exists:cliente,ID',
            'ID_Estado' => 'required|exists:estadop,ID',
            'Calle' => 'required|string',
            'Numero_Puerta' => 'required|string',
            'Ciudad' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        $direccion = $validatedData['Calle'] . ' ' . $validatedData['Numero_Puerta'] . ', ' . $validatedData['Ciudad'];

        $direccionValida = $this->validarDireccion($direccion);

        if ($direccionValida !== true) {
            return $direccionValida;
        }

        $validatedData['Destino'] = $direccion;
        $validatedData['Codigo'] = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

        Paquete::create($validatedData);

        return response()->json(['success' => 'Paquete creado, código: ' . $validatedData['Codigo']], 200);
    }

    public function listarPaquetes()
    {
        $paquetesEnEstante = PaqueteEstante::whereNull('deleted_at')->get();
        $paquetes = Paquete::whereNotIn('ID', $paquetesEnEstante->pluck('ID_Paquete'));

        $paquetesCombinados = $paquetes->union($paquetesEnEstante)->get();

        return response()->json(['success' => $paquetesCombinados], 200);
    }



    public function buscarPaquete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Codigo' => 'string',
            'ID_Cliente' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        if (isset($validatedData['Codigo'])) {
            $paquete = Paquete::where('Codigo', $validatedData['Codigo'])->first();
        } else {
            $paquete = Paquete::where('ID_Cliente', $validatedData['ID_Cliente'])->get();
        }

        if ($paquete == null) {
            return response()->json(['error' => 'Paquete no encontrado'], 404);
        }

        return response()->json(['Paquete' => $paquete], 200);
    }

    public function registrarPaqueteEstante(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Paquete' => 'required|exists:paquete,ID',
            'ID_Estante' => 'required|exists:estante,ID',
            'ID_Almacen' => 'required|exists:almacen,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            $paquete = Paquete::findOrFail($validatedData['ID_Paquete']);
            $estante = Estante::where('ID', $validatedData['ID_Estante'])
                ->where('ID_Almacen', $validatedData['ID_Almacen'])
                ->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Paquete, estante o almacén no encontrado'], 404);
        }

        $paqueteEstante = PaqueteEstante::where('ID_Paquete', $validatedData['ID_Paquete'])->whereNull('deleted_at')->first();

        if ($paqueteEstante !== null) {
            return response()->json(['error' => 'Paquete ya se encuentra en un estante'], 422);
        }

        $paqueteEstante = PaqueteEstante::create([
            'ID_Paquete' => $validatedData['ID_Paquete'],
            'ID_Estante' => $validatedData['ID_Estante'],
            'ID_Almacen' => $validatedData['ID_Almacen'],
        ]);

        return response()->json(['success' => 'Paquete ' . $paquete->Codigo . ' registrado en estante con ID ' . $paqueteEstante->ID_Estante . ' en almacén ' . $paqueteEstante->ID_Almacen], 200);
    }

    public function trasladarPaqueteEstante(Request $request)
    {
        $paqueteEstante = PaqueteEstante::where('ID_Paquete', $request->ID_Paquete)->whereNull('deleted_at')->first();

        if ($paqueteEstante == null) {
            return response()->json(['error' => 'Paquete no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ID_Estante' => 'required|exists:estante,ID',
            'ID_Almacen' => 'required|exists:almacen,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            $estante = Estante::where('ID', $validatedData['ID_Estante'])
                ->where('ID_Almacen', $validatedData['ID_Almacen'])
                ->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Estante o almacén no encontrado'], 404);
        }

        $paqueteEstante->ID_Estante = $validatedData['ID_Estante'];

        $paqueteEstante->ID_Almacen = $validatedData['ID_Almacen'];

        $paqueteEstante->save();

        return response()->json(['success' => 'Paquete trasladado a estante con ID ' . $paqueteEstante->ID_Estante . ' en almacén ' . $paqueteEstante->ID_Almacen], 200);
    }

    public function quitarPaqueteDeEstante(Request $request)
    {
        $paqueteEstante = PaqueteEstante::where('ID_Paquete', $request->ID_Paquete)->whereNull('deleted_at')->first();

        if ($paqueteEstante == null) {
            return response()->json(['error' => 'Paquete no encontrado'], 404);
        }

        $paqueteEstante->delete();

        return response()->json(['success' => 'Paquete quitado de estante'], 200);
    }
}
