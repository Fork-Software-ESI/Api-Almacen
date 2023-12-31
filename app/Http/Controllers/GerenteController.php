<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Paquete;
use App\Models\PaqueteEstante;
use App\Models\Almacen;
use App\Models\Lote;
use App\Models\Forma;
use App\Models\EstadoF;
use App\Models\LoteCamion;
use App\Models\CamionLlevaLote;
use App\Models\Chofer;
use App\Models\Camion;
use App\Models\ChoferCamion;
use App\Models\CamionPlataforma;
use App\Models\CamionPlataformaSalida;
use App\Models\GerenteLote;
use App\Models\GerenteAlmacen;
use App\Models\GerentePaquete;
use App\Models\GerenteForma;

class GerenteController extends Controller
{
    private function validarDireccion($direccion)
    {
        $apiKey = '7a6TfdGhaJbpPMG2ehCfSExHYsnzdkIb5a0YlJzjU5U';
        $address = urlencode($direccion);
        $countryName = "Uruguay";
        $url = "https://geocode.search.hereapi.com/v1/geocode?q=$address&country=$countryName&apiKey=$apiKey";

        $response = @file_get_contents($url);
        $data = json_decode($response);

        if (empty($data->items || $data == null)) {
            return response()->json(['error' => 'Dirección inválida'], 400);
        }

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
                return response()->json(['error' => 'Dirección inválida'], 400);
            }
        }
    }

    public function crearPaquete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Gerente' => 'required|exists:gerente_almacen,ID_Gerente',
            'Descripcion' => 'string',
            'Peso_Kg' => 'required|numeric|min:1',
            'ID_Cliente' => 'required|exists:cliente,ID',
            'Calle' => 'required|string',
            'Numero_Puerta' => 'required|string',
            'Ciudad' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        $gerente = GerenteAlmacen::where('ID_Gerente', $validatedData['ID_Gerente'])->first();

        if ($gerente == null) {
            return response()->json(['error' => 'Gerente no encontrado'], 404);
        }

        $direccion = $validatedData['Calle'] . ', ' . $validatedData['Numero_Puerta'] . ', ' . $validatedData['Ciudad'];

        $direccionValida = $this->validarDireccion($direccion);

        if ($direccionValida !== true) {
            return $direccionValida;
        }

        $validatedData['Destino'] = $direccion;
        $validatedData['Codigo'] = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        if (Paquete::where('Codigo', $validatedData['Codigo'])->first() !== null) {
            $validatedData['Codigo'] = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        }

        $validatedData['ID_Estado'] = 1;

        $paqueteCreado = Paquete::create($validatedData);

        GerentePaquete::create([
            'ID_Gerente' => $gerente->ID_Gerente,
            'ID_Paquete' => $paqueteCreado->ID
        ]);

        return response()->json(['success' => 'Paquete creado, código: ' . $paqueteCreado->Codigo], 200);
    }

    public function eliminarPaquete($id)
    {
        $paquete = Paquete::findOrFail($id);
        Forma::where('ID_Paquete', $id)->delete();
        PaqueteEstante::where('ID_Paquete', $id)->delete();
        $paquete->delete();
        return response()->json(['success' => 'Paquete con codigo ' . $paquete->Codigo . ' eliminado'], 200);
    }

    public function editarPaquete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID' => 'required|exists:paquete,ID',
            'Descripcion' => 'string',
            'Peso_Kg' => 'numeric|min:1',
            'ID_Cliente' => 'exists:cliente,ID',
            'ID_Estado' => 'exists:estadop,ID',
            'Calle' => 'string',
            'Numero_Puerta' => 'numeric',
            'Ciudad' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        $paquete = Paquete::findOrFail($validatedData['ID']);

        if (isset($validatedData['Calle']) || isset($validatedData['Numero_Puerta']) || isset($validatedData['Ciudad'])) {
            $direccion = $validatedData['Calle'] . ', ' . $validatedData['Numero_Puerta'] . ', ' . $validatedData['Ciudad'];

            $direccionValida = $this->validarDireccion($direccion);

            if ($direccionValida !== true) {
                return $direccionValida;
            }

            $validatedData['Destino'] = $direccion;
        }

        $paquete->update($validatedData);

        return response()->json(['success' => 'Paquete editado'], 200);
    }

    public function listarPaquetesAlmacen($ID_Almacen)
    {
        $almacen = Almacen::findOrFail($ID_Almacen);
        $paquetesEnAlmacen = PaqueteEstante::where('ID_Almacen', $almacen->ID)->whereNull('deleted_at')->get();

        return response()->json(['Paquetes' => $paquetesEnAlmacen], 200);
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
            $paquete = Paquete::where('Codigo', $validatedData['Codigo'])->whereNull('deleted_at')->first();
        } else {
            $paquete = Paquete::where('ID_Cliente', $validatedData['ID_Cliente'])->whereNull('deleted_at')->get();
        }

        if ($paquete == null) {
            return response()->json(['error' => 'Paquete no encontrado'], 404);
        }

        return response()->json(['Paquete' => $paquete], 200);
    }

    public function buscarLote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Gerente' => 'required|exists:gerente_almacen,ID_Gerente',
            'ID_Lote' => 'required|numeric|exists:lote,ID',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $validatedData = $validator->validated();
        $lote = Lote::where('ID', $validatedData['ID_Lote'])
            ->whereHas('gerente_lote', function ($query) use ($validatedData) {
                $query->where('ID_Gerente', $validatedData['ID_Gerente']);
            })
            ->whereNull('deleted_at')
            ->whereIn('ID_Estado', [1, 2])
            ->first();

        if (!$lote) {
            return response()->json(['error' => 'Lote no asociado al gerente'], 404);
        }
        return response()->json(['Lote' => $lote], 200);
    }
    public function crearLote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Descripcion' => 'string',
            'Peso_Kg' => 'numeric|required|min:1',
            'ID_Gerente' => 'required|exists:gerente_almacen,ID_Gerente',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $validatedData['ID_Estado'] = 1;

        $lote = Lote::create([
            'Descripcion' => $validatedData['Descripcion'],
            'Peso_Kg' => $validatedData['Peso_Kg'],
            'ID_Estado' => $validatedData['ID_Estado']
        ]);

        GerenteLote::create([
            'ID_Gerente' => $validatedData['ID_Gerente'],
            'ID_Lote' => $lote->ID
        ]);

        return response()->json(['success' => 'Lote creado con ID ' . $lote->ID], 200);
    }

    public function listarLotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Gerente' => 'required|exists:gerente_almacen,ID_Gerente',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $gerente = GerenteAlmacen::where('ID_Gerente', $validatedData['ID_Gerente'])->first();

        $lotes = $gerente->gerente_lotes()->with('lote')->get()->pluck('lote')->whereNull('deleted_at')->whereIn('ID_Estado', [1, 2]);

        return response()->json(['data' => $lotes], 200);
    }

    public function asignarPaqueteLote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Gerente' => 'required|exists:gerente_almacen,ID_Gerente',
            'ID_Paquete' => 'required|exists:paquete,ID',
            'ID_Lote' => 'required|exists:lote,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $gerente = GerenteAlmacen::where('ID_Gerente', $validatedData['ID_Gerente'])->first();

        if (!$gerente) {
            return response()->json(['error' => 'Gerente no encontrado'], 404);
        }

        try {
            $paquete = Paquete::where('ID', $validatedData['ID_Paquete'])->whereNull('deleted_at')->firstOrFail();
            $lote = Lote::where('ID', $validatedData['ID_Lote'])->whereNull('deleted_at')->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Paquete o lote no encontrado'], 404);
        }

        if (Forma::where('ID_Paquete', $validatedData['ID_Paquete'])->whereNull('deleted_at')->first() !== null) {
            return response()->json(['error' => 'Paquete ya asignado a un lote'], 422);
        }

        if ($lote->ID_Estado != 1) {
            return response()->json(['error' => 'Lote no disponible'], 422);
        }

        $gerenteRegistraPaquete = GerentePaquete::where('ID_Paquete', $paquete->ID)->whereNull('deleted_at')->first();
        $gerenteRegistraLote = GerenteLote::where('ID_Lote', $lote->ID)->whereNull('deleted_at')->first();

        $gerentePaquete = GerenteAlmacen::where('ID_Gerente', $gerenteRegistraPaquete->ID_Gerente)->whereNull('deleted_at')->first();
        $gerenteLote = GerenteAlmacen::where('ID_Gerente', $gerenteRegistraLote->ID_Gerente)->whereNull('deleted_at')->first();

        if ($gerentePaquete->ID_Almacen != $gerenteLote->ID_Almacen) {
            return response()->json(['error' => 'El paquete y el lote no pertenecen al mismo almacén'], 422);
        }

        Forma::create([
            'ID_Lote' => $validatedData['ID_Lote'],
            'ID_Paquete' => $validatedData['ID_Paquete'],
            'ID_Estado' => 1
        ]);

        GerenteForma::create([
            'ID_Gerente' => $gerente->ID_Gerente,
            'ID_Paquete' => $paquete->ID
        ]);

        return response()->json(['success' => 'Paquete ' . $paquete->ID . ' asignado a lote ' . $lote->ID], 200);
    }

    public function editarLote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID' => 'required|exists:lote,ID',
            'Descripcion' => 'string',
            'Peso_Kg' => 'numeric|min:1',
            'ID_Estado' => 'exists:estadol,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $lote = Lote::findOrFail($validatedData['ID']);

        $lote->update($validatedData);

        return response()->json(['success' => 'Lote ' . $lote->ID . ' editado'], 200);
    }

    public function eliminarLote($id)
    {
        $lote = Lote::findOrFail($id);
        Forma::where('ID_Lote', $lote->ID)->delete();
        CamionLlevaLote::where('ID_Lote', $lote->ID)->delete();
        LoteCamion::where('ID_Lote', $lote->ID)->delete();
        $lote->delete();
        return response()->json(['success' => 'Lote ' . $lote->ID . ' eliminado'], 200);
    }

    public function verChoferes()
    {
        $choferes = Chofer::whereNull('deleted_at')->get();
        return response()->json(['Choferes' => $choferes], 200);
    }

    public function verCamiones()
    {
        $camiones = Camion::whereNull('deleted_at')->get();
        return response()->json(['Camiones' => $camiones], 200);
    }

    public function verCamionesSinChofer()
    {
        $camiones = Camion::whereNull('camion.deleted_at')
            ->where(function ($query) {
                $query->whereDoesntHave('chofers', function ($subquery) {
                    $subquery->whereNull('chofer.deleted_at');
                })->orWhereHas('chofers', function ($subquery) {
                    $subquery->whereNotNull('chofer_camion.deleted_at');
                })->orWhereHas('chofers', function ($subquery) {
                    $subquery->whereNull('chofer_camion.deleted_at')
                        ->where('chofer_camion.ID_Estado', 5);
                });
            })
            ->get();

        return response()->json(['Camiones' => $camiones], 200);
    }

    public function verCamionesDisponibles()
    {
        $response = $this->verCamionesSinChofer();
        $camionesSinChoferData = json_decode($response->getContent());

        $camionesSinChoferIds = collect($camionesSinChoferData->Camiones)->pluck('ID');
        $camionesDisponibles = Camion::whereNull('camion.deleted_at')
            ->whereNotIn('ID', $camionesSinChoferIds)
            ->get();

        $camionesFinales = ChoferCamion::whereIn('ID_Camion', $camionesDisponibles->pluck('ID')->toArray())
            ->whereNull('deleted_at')
            ->whereIn('ID_Estado', [1, 2])
            ->get();

        return response()->json(['Camiones disponibles' => $camionesFinales], 200);
    }

    public function verChoferesDisponibles()
    {
        $choferes = Chofer::whereNull('chofer.deleted_at')
            ->where(function ($query) {
                $query->whereDoesntHave('camions', function ($subquery) {
                    $subquery->whereNull('camion.deleted_at');
                })->orWhereHas('camions', function ($subquery) {
                    $subquery->whereNotNull('chofer_camion.deleted_at');
                })->orWhereHas('camions', function ($subquery) {
                    $subquery->whereNull('chofer_camion.deleted_at')
                        ->where('chofer_camion.ID_Estado', 5);
                });
            })
            ->get();

        return response()->json(['Choferes' => $choferes], 200);
    }

    public function asignarChoferCamion(Request $request)
    {
        $data = $request->validate([
            'ID_Chofer' => 'required|exists:chofer,ID',
            'ID_Camion' => 'required|exists:camion,ID',
        ]);

        $choferesDisponiblesResponse = $this->verChoferesDisponibles();
        $choferesDisponiblesData = json_decode($choferesDisponiblesResponse->getContent());

        $camionesSinChoferResponse = $this->verCamionesSinChofer();
        $camionesSinChoferData = json_decode($camionesSinChoferResponse->getContent());

        $choferesDisponibles = collect($choferesDisponiblesData->Choferes);
        if (!$choferesDisponibles->contains('ID', $data['ID_Chofer'])) {
            return response()->json(['error' => 'El chofer no está disponible'], 400);
        }

        $camionesSinChofer = collect($camionesSinChoferData->Camiones);
        if (!$camionesSinChofer->contains('ID', $data['ID_Camion'])) {
            return response()->json(['error' => 'El camión no está disponible'], 400);
        }

        $choferCamion = ChoferCamion::create([
            'ID_Chofer' => $data['ID_Chofer'],
            'ID_Camion' => $data['ID_Camion'],
            'Fecha_Hora_Inicio' => now(),
            'ID_Estado' => 1,
        ]);

        return response()->json(['message' => "Chofer ID: " . $choferCamion->ID_Chofer . " asignado a Camión ID: " . $choferCamion->ID_Camion . " con Estado: " . $choferCamion->ID_Estado], 200);
    }

    public function listarChoferesCamiones()
    {
        $choferesCamiones = ChoferCamion::whereNull('deleted_at')->where('ID_Estado', '<>', 5)->get();
        return response()->json(['ChoferesCamiones' => $choferesCamiones], 200);
    }

    public function verCamionesEnPlataformas(Request $request)
    {
        $almacen = Almacen::findOrFail($request->ID_Almacen);
        $camionesEnPlataformas = CamionPlataforma::select('camion_plataforma.ID_Camion', 'camion.Matricula', 'camion_plataforma.Numero_Plataforma', 'camion_plataforma.Fecha_Hora_Llegada')
            ->leftJoin('camion_plataforma_salida', function ($join) {
                $join->on('camion_plataforma.ID_Camion', '=', 'camion_plataforma_salida.ID_Camion')
                    ->on('camion_plataforma.ID_Almacen', '=', 'camion_plataforma_salida.ID_Almacen')
                    ->on('camion_plataforma.Numero_Plataforma', '=', 'camion_plataforma_salida.Numero_Plataforma');
            })
            ->leftJoin('camion', 'camion_plataforma.ID_Camion', '=', 'camion.ID')
            ->where('camion_plataforma.ID_Almacen', $almacen->ID)
            ->whereNull('camion_plataforma.deleted_at')
            ->where(function ($query) {
                $query->whereNull('camion_plataforma_salida.Fecha_Hora_Salida')
                    ->orWhereNotNull('camion_plataforma_salida.deleted_at');
            })
            ->get();

        return $camionesEnPlataformas;
    }

    public function verCamionesEnTransito()
    {
        $camionesEnTransito = ChoferCamion::where('ID_Estado', 4)->whereNull('deleted_at')->get();
        if (!$camionesEnTransito) {
            return response()->json(['error' => 'No hay camiones en tránsito'], 404);
        }

        $horariosSalida = [];

        foreach ($camionesEnTransito as $camion) {
            $horarioSalida = CamionPlataformaSalida::where('ID_Camion', $camion->ID_Camion)->value('Fecha_Hora_Salida');
            $horariosSalida[$camion->ID_Camion] = $horarioSalida;
        }

        return response()->json([
            'Camiones en tránsito' => $camionesEnTransito,
            'Horarios de salida' => $horariosSalida
        ], 200);
    }

    public function buscarCamion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Camion' => 'exists:camion,ID',
            'Matricula' => 'string|exists:camion,Matricula',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if ($request->ID_Camion) {
            $camion = Camion::where('ID', $request->ID_Camion)->first();
        } else {
            $camion = Camion::where('Matricula', $request->Matricula)->first();
        }

        if (!$camion) {
            return response()->json(['error' => 'Camión no encontrado'], 404);
        }

        return response()->json(['Camion' => $camion], 200);
    }

    public function asignarLoteCamion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Lote' => 'required|exists:lote,ID',
            'ID_Camion' => 'required|exists:camion,ID'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $camion = Camion::findOrFail($validatedData['ID_Camion'])->whereNull('deleted_at')->first();
        $lote = Lote::where('ID', $validatedData['ID_Lote'])->whereNull('deleted_at')->first();
        $lotesCamion = LoteCamion::where('ID_Lote', $lote->ID)->whereNull('deleted_at')->get();


        if ($lotesCamion->count() > 0) {
            return response()->json(['error' => 'Lote ya asignado a un camión'], 422);
        }

        if ($lotesCamion->count() > 0) {
            return response()->json(['error' => 'Lote ya asignado a un camión'], 422);
        }

        $forma = Forma::where('ID_Lote', $lote->ID)->whereNull('deleted_at')->get();

        if (!$forma) {
            return response()->json(['error' => 'El lote está vacío'], 422);
        }

        $choferCamion = ChoferCamion::where('ID_Camion', $camion->ID)->whereNull('deleted_at')->first();

        if (!$choferCamion) {
            return response()->json(['error' => 'Camión no asignado a un chofer'], 422);
        }

        if ($choferCamion->ID_Estado !== 2 && $choferCamion->ID_Estado !== 1) {
            return response()->json(['error' => 'Camión no disponible'], 422);
        }

        if ($lote->ID_Estado != 1) {
            return response()->json(['error' => 'Lote no disponible'], 422);
        }

        $pesoTotalKg = Paquete::whereIn('ID', $forma->pluck('ID_Paquete')->toArray())->sum('Peso_Kg') + $lote->Peso_Kg;

        if ($pesoTotalKg > $camion->PesoMaximoKg) {
            return response()->json(['error' => 'Camión no tiene capacidad suficiente'], 422);
        }

        LoteCamion::create([
            'ID_Lote' => $lote->ID,
            'ID_Camion' => $camion->ID,
            'Fecha_Hora_Inicio' => now(),
            'ID_Estado' => 1
        ]);

        return response()->json(['success' => 'Lote ' . $lote->ID . ' asignado a camión ' . $camion->ID], 200);
    }

    public function marcarCamionComoPreparado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Chofer' => 'required|exists:chofer_camion,ID_Chofer',
            'ID_Camion' => 'required|exists:chofer_camion,ID_Camion'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $choferCamion = ChoferCamion::where('ID_Chofer', $validatedData['ID_Chofer'])->where('ID_Camion', $validatedData['ID_Camion'])->whereNull('deleted_at')->first();

        $lotesCamion = LoteCamion::where('ID_Camion', $validatedData['ID_Camion'])->whereNull('deleted_at')->get();

        if ($choferCamion === null) {
            return response()->json(['error' => 'Chofer y camión no encontrados'], 404);
        }

        if ($choferCamion->ID_Estado != 2) {
            return response()->json(['error' => 'Chofer camión no disponibles'], 422);
        }

        $lotesListos = true;

        foreach ($lotesCamion as $lote) {
            if ($lote->ID_Estado != 2) {
                $lotesListos = false;
                break;
            }
        }

        if (!$lotesListos) {
            return response()->json(['error' => 'No todos los lotes están listos'], 422);
        }

        $choferCamion->ID_Estado = 3;

        ChoferCamion::where('ID_Chofer', $validatedData['ID_Chofer'])->where('ID_Camion', $validatedData['ID_Camion'])->whereNull('deleted_at')->update([
            'ID_Estado' => 3
        ]);

        return response()->json(['success' => 'Camión ' . $choferCamion->ID_Camion . ' listo para salir'], 200);
    }
}
