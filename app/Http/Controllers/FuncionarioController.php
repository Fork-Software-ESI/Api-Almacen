<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\PaqueteEstante;
use App\Models\Estante;
use App\Models\FuncionarioPaqueteEstante;
use App\Models\Lote;
use App\Models\GerenteAlmacen;
use App\Models\FuncionarioAlmacen;
use App\Models\GerenteForma;
use App\Models\Forma;
use App\Models\LoteCamion;
use App\Models\Camion;


class FuncionarioController extends Controller
{
    public function registrarPaqueteEstante(Request $request)
    {
        $validatedData = $request->validate([
            'ID_Funcionario' => 'required|exists:funcionario_almacen,ID',
            'ID_Paquete' => 'required|exists:paquete,ID',
            'ID_Estante' => 'required|exists:estante,ID',
            'ID_Almacen' => 'required|exists:estante,ID_Almacen',
        ]);

        $paquete = Paquete::findOrFail($validatedData['ID_Paquete']);
        $estante = Estante::where('ID', $validatedData['ID_Estante'])->where('ID_Almacen', $validatedData['ID_Almacen'])->firstOrFail();

        $paqueteExistente = PaqueteEstante::where('ID_Paquete', $validatedData['ID_Paquete'])
            ->where('ID_Almacen', $validatedData['ID_Almacen'])
            ->first();

        if ($paqueteExistente) {
            return response()->json(['error' => 'Paquete ya se encuentra en un estante en el mismo almacén'], 422);
        }

        $paqueteEstante = PaqueteEstante::firstOrCreate([
            'ID_Paquete' => $validatedData['ID_Paquete'],
            'ID_Estante' => $validatedData['ID_Estante'],
            'ID_Almacen' => $validatedData['ID_Almacen'],
        ]);

        FuncionarioPaqueteEstante::firstOrCreate([
            'ID_Funcionario' => $validatedData['ID_Funcionario'],
            'ID_Paquete' => $validatedData['ID_Paquete'],
        ]);

        if ($paqueteEstante->wasRecentlyCreated) {
            return response()->json(['success' => 'Paquete ' . $paquete->Codigo . ' registrado en estante con ID ' . $estante->ID . ' en almacén ' . $paqueteEstante->ID_Almacen], 200);
        } else {
            return response()->json(['error' => 'Paquete ya se encuentra en un estante'], 422);
        }
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

        return response()->json(['success' => 'Paquete trasladado a estante con ID ' . $estante->ID . ' en almacén ' . $paqueteEstante->ID_Almacen], 200);
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

    public function listarPaquetesAlmacen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Funcionario' => 'required|exists:funcionario_almacen,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $funcionario = FuncionarioAlmacen::findOrFail($validatedData['ID_Funcionario']);

        $validatedData = $validator->validated();

        $paquetesEnAlmacen = PaqueteEstante::where('ID_Almacen', $funcionario->ID_Almacen)->whereNull('deleted_at')->get();

        return response()->json(['Paquetes' => $paquetesEnAlmacen], 200);
    }

    public function listarLotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Funcionario' => 'required|exists:funcionario_almacen,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $funcionario = FuncionarioAlmacen::findOrFail($validatedData['ID_Funcionario']);

        $gerente = GerenteAlmacen::where('ID_Almacen', $funcionario->ID_Almacen)->firstOrFail();

        $lotes = $gerente->gerente_lotes()->with('lote')->get()->pluck('lote');

        return response()->json(['success' => $lotes], 200);
    }

    public function listarPaqueteLote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Funcionario' => 'required|exists:funcionario_almacen,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $funcionario = FuncionarioAlmacen::findOrFail($validatedData['ID_Funcionario']);

        $gerente = GerenteAlmacen::where('ID_Almacen', $funcionario->ID_Almacen)->firstOrFail();

        $paquetesLotes = $gerente->gerente_formas()
            ->whereDoesntHave('forma', function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->with('forma')
            ->get()
            ->pluck('forma', 'ID_Paquete');

        return response()->json(['success' => $paquetesLotes], 200);
    }

    public function actualizarPaqueteLote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Funcionario' => 'required|exists:funcionario_almacen,ID',
            'ID_Paquete' => 'required|exists:forma,ID_Paquete',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $funcionario = FuncionarioAlmacen::where('ID', $validatedData['ID_Funcionario'])->whereNull('deleted_at')->first();
        $paquete = Paquete::where('ID', $validatedData['ID_Paquete'])->whereNull('deleted_at')->first();
        $paqueteLote = Forma::where('ID_Paquete', $validatedData['ID_Paquete'])->whereNull('deleted_at')->first();
        $lote = Lote::where('ID', $paqueteLote->ID_Lote)->whereNull('deleted_at')->first();
        $gerenteForma = GerenteForma::where('ID_Paquete', $paqueteLote->ID_Paquete)->whereNull('deleted_at')->first();
        $gerente = GerenteAlmacen::where('ID_Gerente', $gerenteForma->ID_Gerente)->whereNull('deleted_at')->first();
        $paqueteEstante = PaqueteEstante::where('ID_Paquete', $validatedData['ID_Paquete'])->whereNull('deleted_at')->first();

        if (!$funcionario || !$paquete || !$paqueteLote || !$lote || !$gerenteForma || !$gerente) {
            return response()->json(['error' => 'Recurso no encontrado'], 404);
        }

        if ($paqueteEstante) {
            return response()->json(['error' => 'No se puede cargar un paquete a un lote si aún está en un estante'], 422);
        }

        if ($paquete->ID_Estado != 1 || $lote->ID_Estado != 1 || $gerente->ID_Almacen != $funcionario->ID_Almacen || $paqueteLote->ID_Estado != 1) {
            return response()->json(['error' => 'Este par paquete lote no está disponible para actualizar'], 422);
        }

        $paquete->ID_Estado = 2;
        $paquete->save();
        $paqueteLote->ID_Estado = 2;
        $paqueteLote->save();

        return response()->json(['success' => 'Paquete lote actualizado'], 200);
    }

    public function cargarLoteCamion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Funcionario' => 'required|exists:funcionario_almacen,ID',
            'ID_Lote' => 'required|exists:lote,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $lotesAlmacen = $this->listarLotes($request)->getData()->success;
        $validatedData = $validator->validated();
        $funcionario = FuncionarioAlmacen::where('ID', $validatedData['ID_Funcionario'])->whereNull('deleted_at')->first();
        $loteCamion = LoteCamion::where('ID_Lote', $validatedData['ID_Lote'])->whereNull('deleted_at')->first();
        $lote = Lote::where('ID', $validatedData['ID_Lote'])->whereNull('deleted_at')->first();
        $camion = Camion::where('ID', $loteCamion->ID_Camion)->whereNull('deleted_at')->first();
        $loteForma = Forma::where('ID_Lote', $validatedData['ID_Lote'])->whereNull('deleted_at')->first();

        if (!$funcionario || !$loteCamion || !$lote || !$camion || !$loteForma) {
            return response()->json(['error' => 'Recurso no encontrado'], 404);
        }

        if ($loteCamion->ID_Estado != 1) {
            return response()->json(['error' => 'Este lote no está disponible para actualizar'], 422);
        }

        if ($loteForma->ID_Estado != 2) {
            return response()->json(['error' => 'Este lote no está listo para ser cargado'], 422);
        }

        foreach ($lotesAlmacen as $lote) {
            if ($lote->ID == $validatedData['ID_Lote']) {
                $loteCamion->ID_Estado = 2;

                $loteCamion->save();

                return response()->json(['success' => 'Lote ' . $loteCamion->ID_Lote . ' cargado en el camion con matricula ' . $camion->Matricula], 200);
            }
        }

        return response()->json(['error' => 'Este lote no se encuentra en el almacen'], 422);
    }
}
