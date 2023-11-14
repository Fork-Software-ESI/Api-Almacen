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
            return $this->marcarLlegada($camionPlataforma, $camion);
        }
        
        return $this->marcarSalida($camionPlataformaSalida, $camion);
    }

    private function marcarLlegada($camionPlataforma, $camion)
    {
        if ($camionPlataforma->Fecha_Hora_Llegada !== null) {
            return response()->json(['mensaje', 'El camión ya ha llegado']);
        }

        CamionPlataforma::where('ID_Camion', $camion->ID)->update(['Fecha_Hora_Llegada' => now()]);

        ChoferCamion::where('ID_Camion', $camion->ID)->update(['ID_Estado' => 2]);

        return response()->json(['mensaje','Se ha marcado la hora exitosamente']);
    }

    private function marcarSalida($camionPlataformaSalida, $camion)
    {
        if($camionPlataformaSalida->Fecha_Hora_Salida != null){
            return response()->json(['mensaje', 'El camión ya ha salido']);
        }
        CamionPlataformaSalida::where('ID_Camion', $camion->ID)->update(['Fecha_Hora_Salida' => now()]);
        ChoferCamion::where('ID_Camion', $camion->ID)->update(['ID_Estado' => 4]);

        $loteCamion = LoteCamion::where('ID_Camion', $camion->ID)->first();
        if($loteCamion){
            $this->loteCamionSalida($loteCamion);
        }

        return response()->json(['mensaje','Se a marcado la hora exitosamente']);
    }
    private function loteCamionSalida($loteCamion)
    {
        $loteCamion -> update(['ID_Estado' => 2]);
        $lote = Lote::where('ID', $loteCamion->ID_Lote)->first();
        if($lote){
            $this->lote($lote);
        }
    }
    private function lote($lote)
    {
        $lote -> update(['ID_Estado' => 2]);
        $formas = Forma::where('ID_Lote', $lote->ID)->get();
        foreach($formas as $forma){
            $forma -> update(['ID_Estado' => 2]);        
            $paquete = Paquete::where('ID', $forma->ID_Paquete)->first();
            if($paquete){
                $paquete -> update(['ID_Estado' => 3]);
            }
        }
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

    public function estadoCamion(Request $request)
    {
        $matricula = $request->input('matricula');
        $camion = Camion::where('Matricula', $matricula)->first();

        if (!$camion) {
            return response()->json(['mensaje' => 'Camión no encontrado'], 402);
        }

        $loteCamion = LoteCamion::where('ID_Camion', $camion->ID)->first();
        $choferCamion = ChoferCamion::where('ID_Camion', $camion->ID)->first();

        if($loteCamion){
            return $this->loteCamion($loteCamion, $choferCamion);
        }

        return $this->estadoNoTieneLote($camion);
    }


    private function estadoLoteCamion($loteCamion)
    {
        $estadoLoteCamion = $loteCamion->ID_Estado;
        if($estadoLoteCamion == 1) {
            $estadoLoteCamion = 'Pendiente';
        } 
        if($estadoLoteCamion == 2) {
            $estadoLoteCamion = 'Cargado';
        }
        if($estadoLoteCamion == 3) {
            $estadoLoteCamion = 'Entregado';
        }

        return $estadoLoteCamion;
    }

    private function estadoChoferCamion($choferCamion)
    {
        $estadoChoferCamion = $choferCamion->ID_Estado;
        if($estadoChoferCamion == 1) {
            $estadoChoferCamion = 'Estacionado';
        }
        if($estadoChoferCamion == 2) {
            $estadoChoferCamion = 'En plataforma';
        }
        if($estadoChoferCamion == 3) {
            $estadoChoferCamion = 'Cargado';
        }
        if($estadoChoferCamion == 4) {
            $estadoChoferCamion = 'En transito';
        }
        if($estadoChoferCamion == 5) {
            $estadoChoferCamion = 'Completado';
        }
        return $estadoChoferCamion;
    }

    private function loteCamion($loteCamion, $choferCamion)
    {
        $estadoLoteCamion = $this->estadoLoteCamion($loteCamion);
        $estadoChoferCamion = $this->estadoChoferCamion($choferCamion);
        if($estadoChoferCamion == 'En transito'){
            return response()->json([
            'mensaje' => 'Camión en transito'
            ]);
        }
    
        if($estadoLoteCamion != 3){
            $plataforma = CamionPlataforma::where('ID_Camion', $choferCamion->ID_Camion)->first();
        }
    
        return response()->json([
            'mensaje' => 'Camión encontrado en plataforma',
            'estadoLoteCamion' => $estadoLoteCamion,
            'estadoChoferCamion' => $estadoChoferCamion,
            'plataforma' => $plataforma->Numero_Plataforma,
            'almacen' => $plataforma->ID_Almacen
        ]);
    }    
    private function estadoNoTieneLote($camion)
    {
        $plataformaCamion = CamionPlataforma::where('ID_Camion', $camion->ID)->first();
        if(!$plataformaCamion){
            return response()->json(['mensaje' => 'Camion sin ubicacion, ni estado'], 402);
        }

        return response()->json([
            'mensaje' => 'Camión en Plataforma',
            'Plataforma' => $plataformaCamion -> Numero_Plataforma,
            'Almacen' => $plataformaCamion -> ID_Almacen
        ]);
    }

    public function paqueteEntregado(Request $request)
    {
        $ID_Paquete = $request->input('ID_Paquete');

        $paquete = Paquete::where('ID', $ID_Paquete)->first();
        if (!$paquete) {
            return response()->json(['mensaje' => 'Paquete no encontrado']);
        }

        if($paquete->ID_Estado == 4){
            return response()->json(['mensaje' => 'Paquete ya entregado']);
        }

        $paqueteLote = Forma::where('ID_Paquete', $ID_Paquete)->first();
        if (!$paqueteLote) {
            return response()->json(['mensaje' => 'Paquete no asignado a un lote']);
        }

        $loteCamion = LoteCamion::where('ID_Lote', $paqueteLote->ID_Lote)->first();
        if (!$loteCamion) {
            return response()->json(['mensaje' => 'Lote no asignado a un camion']);
        }

        $lote = Lote::where('ID', $loteCamion->ID_Lote)->first();

        $choferCamion = ChoferCamion::where('ID_Camion', $loteCamion->ID_Camion)->first();

        $paquete -> update([
            'ID_Estado' => 4,
        ]);

        $paqueteLote -> update([
            'ID_Estado' => 3,
        ]);

        $forma = Forma::where('ID_Lote', $lote->ID)->get();

        $paquetesEntregados = 1;
        foreach($forma as $formas){
            $paquetes = Paquete::where('ID', $formas->ID_Paquete)->first();
            if($paquetes && $paquetes->ID_Estado != 4){
                $paquetesEntregados = 0;
                break;
            }
        }

        if($paquetesEntregados == 1){
            Lote::where('ID', $lote->ID)
                ->update([
                    'ID_Estado' => 3,
                ]);

            LoteCamion::where('ID_Lote', $lote->ID)
                ->update([
                    'ID_Estado' => 3,
                ]);

            ChoferCamion::where('ID_Chofer', $choferCamion->ID_Chofer)
                ->where('ID_Camion', $choferCamion->ID_Camion)
                ->update([
                    'ID_Estado' => 5,
                ]);
        }

        return response()->json(['mensaje' => 'Paquete entregado con éxito']);
    }
}
