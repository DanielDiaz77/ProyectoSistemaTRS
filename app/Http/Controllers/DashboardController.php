<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {

        $usid = \Auth::user()->id;
        $usarea = \Auth::user()->area;


                $anio=date('Y');
                $ingresos=DB::table('ingresos as i')
                ->join('users','i.idusuario','=','users.id')
                ->select(DB::raw('MONTH(i.fecha_hora) as mes'),
                DB::raw('YEAR(i.fecha_hora) as anio'),
                DB::raw('COUNT(i.id) as total'))
                ->where([['i.estado','Registrado'],['users.area',$usarea]])
                ->whereYear('i.fecha_hora',$anio)
                ->groupBy(DB::raw('MONTH(i.fecha_hora)'),DB::raw('YEAR(i.fecha_hora)'))
                ->get();

                $ventas=DB::table('ventas as v')
                ->join('users','v.idusuario','=','users.id')
                ->select(DB::raw('MONTH(v.fecha_hora) as mes'),
                DB::raw('YEAR(v.fecha_hora) as anio'),
                DB::raw('SUM(v.total) as total'))
                ->where([['v.pagado',1],['users.area',$usarea]])
                ->whereYear('v.fecha_hora',$anio)
                ->groupBy(DB::raw('MONTH(v.fecha_hora)'),DB::raw('YEAR(v.fecha_hora)'))
                ->get();

                $project=DB::table('projects as p')
                ->join('users','p.idusuario','=','users.id')
                ->select(DB::raw('MONTH(p.inicio) as mes','users.idrol'),
                DB::raw('YEAR(p.inicio) as anio'),
                DB::raw('SUM(p.total) as total'))
                ->where([['p.pagado',1],['users.area',$usarea]])
                ->whereYear('p.inicio',$anio)
                ->groupBy(DB::raw('MONTH(p.inicio)'),DB::raw('YEAR(p.inicio)'))
                ->get();





        return [
                'ingresos'=>$ingresos,
                'ventas'=>$ventas,
                'project'=>$project,
                'anio'=>$anio,
            ];


    }
}
