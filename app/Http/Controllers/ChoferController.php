<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chofer;
use App\Models\ChoferCamion;
use App\Models\LoteCamion;
use App\Models\Lote;
use App\Models\Forma;
use App\Models\Paquete;
use App\Models\Camion;
use App\Models\CamionPlataforma;
use App\Models\CamionPlataformaSalida;
use App\Models\ChoferCamionManeja;

class ChoferController extends Controller
{
    //
    public function verContenidoCamion($id)
    {
        $chofer = Chofer::find($id);

        if (!$chofer) {
            return response()->json(['mensaje' => 'Chofer no encontrado'], 402);
        }

        $choferCamion = ChoferCamion::where('ID_Chofer', $chofer->ID)->first();
        
        if (!$choferCamion) {
            return response()->json(['mensaje' => 'Chofer no tiene camión asignado'], 402);
        }

        $lotesCamion = LoteCamion::where('ID_Camion', $choferCamion->ID_Camion)->first();

        if(!$lotesCamion){
            return response()->json(['mensaje' => 'Camión no tiene lote asignado'], 402);
        }

        $lote = Lote::where('ID', $lotesCamion->ID_Lote)->first();

        if(!$lote){
            return response()->json(['mensaje' => 'No tiene lote asignado'], 402);
        }

        $forma = Forma::where('ID_Lote', $lote->ID)->first();

        $paquete = Paquete::find($forma->ID_Paquete);

        $datosLote = [
            'lote' => $lote ? $lote->ID : 'No disponible',
            'descripcionLote' => $lote ? $lote->Descripcion : 'No disponible',
            'pesoLote' => $lote ? $lote->Peso_Kg : 'No disponible',
        ];
    
        $datosPaquete = [
            'paquete' => $paquete ? $paquete->ID : 'No disponible',
            'descripcionPaquete' => $paquete ? $paquete->Descripcion : 'No disponible',
            'pesoPaquete' => $paquete ? $paquete->Peso_Kg : 'No disponible',
        ];

        return response()->json(['datosLote' => $datosLote, 'datosPaquete' => $datosPaquete ]);
    }

    public function marcarHora(Request $request)
    {
        $matricula = $request->input('matricula');
        $hora = $request->input('hora');

        $camion = Camion::where('Matricula', $matricula)->first();
        if (!$camion) {
            return response()->json(['mensaje', 'Camión no encontrado']);
        }

        $camionPlataforma = CamionPlataforma::where('ID_Camion', $camion->ID)->first();

        if (!$camionPlataforma) {
            return response()->json(['mensaje', 'El camión no tiene una plataforma asignada']);
        }

        $camionPlataformaSalida = CamionPlataformaSalida::where('ID_Camion', $camion->ID)->first();

        if($hora == 'llegada'){
            if ($camionPlataforma->Fecha_Hora_Llegada !== null) {
                return response()->json(['mensaje', 'El camión ya ha llegado']);
            }

            CamionPlataforma::where('ID_Camion', $camion->ID)->update(['Fecha_Hora_Llegada' => now()]);

            $estadoc = ChoferCamion::where('ID_Camion', $camion->ID)->first();
            $estadoc ->update([
                'ID_Estado' => 2,
            ]);

            return response()->json(['mensaje','Se ha marcado la hora exitosamente']);
        }
        
        if($camionPlataformaSalida->Fecha_Hora_Salida != null){
            return response()->json(['mensaje', 'El camión ya ha salido']);
        }

        CamionPlataformaSalida::where('ID_Camion', $camion->ID)->update(['Fecha_Hora_Salida' => now()]);

        ChoferCamion::where('ID_Camion', $camion->ID)->update(['ID_Estado' => 4]);
        
        return response()->json(['mensaje','Se a marcado la hora exitosamente']);
    }

    public function liberarCamion(Request $request)
    {
        $ID_Chofer = $request->input('ID_Chofer');

        $chofer = Chofer::find($ID_Chofer);

        if(!$chofer){
            return response()->json(['mensaje' => 'Chofer no encontrado'], 402);
        }

        $choferCamion = ChoferCamion::where('ID_Chofer', $ID_Chofer)->first();
        if(!$choferCamion){
            return response()->json(['mensaje' => 'Chofer no tiene camión asignado'], 402);
        }

        ChoferCamionManeja::where('ID_Chofer', $ID_Chofer)->update(['Fecha_Hora_Fin' => now()]);

        return response()->json(['mensaje' => 'Chofer ha sido liberado']);
    }
}
