<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Gallery;
use App\User;
use App\Link;

class GalleryController extends Controller
{
    public function index(Request $request){
        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $estado = $request->estado;

        if($buscar == ''){
            if($estado == ''){
                $galerias = Gallery::join('users','users.id','galleries.user_id')
                ->leftjoin('personas', 'users.id','=','personas.id')
                ->select('galleries.id','galleries.nombre','galleries.descripcion',
                    'galleries.lote','galleries.cover','galleries.fecha_hora',
                    'galleries.estado','galleries.area','personas.nombre as usuario')
                ->orderBy('galleries.fecha_hora','desc')->paginate(9);
            }elseif($estado == 'Vigente'){
                $galerias = Gallery::join('users','users.id','galleries.user_id')
                ->leftjoin('personas', 'users.id','=','personas.id')
                ->select('galleries.id','galleries.nombre','galleries.descripcion',
                    'galleries.lote','galleries.cover','galleries.fecha_hora',
                    'galleries.estado','galleries.area','personas.nombre as usuario')
                ->where([['galleries.estado','Vigente']])
                ->orderBy('galleries.fecha_hora','desc')->paginate(9);
            }elseif($estado == 'Desactivada'){
                $galerias = Gallery::join('users','users.id','galleries.user_id')
                ->leftjoin('personas', 'users.id','=','personas.id')
                ->select('galleries.id','galleries.nombre','galleries.descripcion',
                    'galleries.lote','galleries.cover','galleries.fecha_hora',
                    'galleries.estado','galleries.area','personas.nombre as usuario')
                ->where([['galleries.estado','Desactivada']])
                ->orderBy('galleries.fecha_hora','desc')->paginate(9);
            }
        }else{
            if($estado == ''){
                $galerias = Gallery::join('users','users.id','galleries.user_id')
                ->leftjoin('personas', 'users.id','=','personas.id')
                ->select('galleries.id','galleries.nombre','galleries.descripcion',
                    'galleries.lote','galleries.cover','galleries.fecha_hora',
                    'galleries.estado','galleries.area','personas.nombre as usuario')
                ->where([['galleries.'.$criterio, 'like', '%'. $buscar . '%']])
                ->orderBy('galleries.fecha_hora','desc')->paginate(9);
            }elseif($estado == 'Vigente'){
                $galerias = Gallery::join('users','users.id','galleries.user_id')
                ->leftjoin('personas', 'users.id','=','personas.id')
                ->select('galleries.id','galleries.nombre','galleries.descripcion',
                    'galleries.lote','galleries.cover','galleries.fecha_hora',
                    'galleries.estado','galleries.area','personas.nombre as usuario')
                ->where([['galleries.estado','Vigente'],['galleries.'.$criterio, 'like', '%'. $buscar . '%']])
                ->orderBy('galleries.fecha_hora','desc')->paginate(9);
            }elseif($estado == 'Desactivada'){
                $galerias = Gallery::join('users','users.id','galleries.user_id')
                ->leftjoin('personas', 'users.id','=','personas.id')
                ->select('galleries.id','galleries.nombre','galleries.descripcion',
                    'galleries.lote','galleries.cover','galleries.fecha_hora',
                    'galleries.estado','galleries.area','personas.nombre as usuario')
                ->where([['galleries.estado','Desactivada'],['galleries.'.$criterio, 'like', '%'. $buscar . '%']])
                ->orderBy('galleries.fecha_hora','desc')->paginate(9);
            }
        }

        return [
            'pagination' => [
                'total'         => $galerias->total(),
                'current_page'  => $galerias->currentPage(),
                'per_page'      => $galerias->perPage(),
                'last_page'     => $galerias->lastPage(),
                'from'          => $galerias->firstItem(),
                'to'            => $galerias->lastItem(),
            ],
            'galerias' => $galerias
        ];
    }

    public function store(Request $request){
        if(!$request->ajax()) return redirect('/');
        $mytime = Carbon::now('America/Mexico_City');

        try{
            DB::beginTransaction();
            //CODE STORE HERE

            $galeria = new Gallery();
            $galeria->user_id     = \Auth::user()->id;
            $galeria->nombre      = $request->nombre;
            $galeria->descripcion = $request->descripcion;
            $galeria->lote        = $request->lote;
            $galeria->cover       = $request->cover;
            $galeria->fecha_hora  = $mytime;
            $galeria->area        = $request->area;
            $galeria->estado      = 'Vigente';
            $galeria->save();

            $enlaces = $request->enlaces;
            $userid = \Auth::user()->id;

            //Recorro todos los elementos
            foreach($enlaces as $ep=>$enl){
                $link = new Link(['user_id' => $userid, 'url' => $enl['url'], 'direction' => $enl['direction']]);
                $galeria->links()->save($link);
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    public function update(Request $request){
        if(!$request->ajax()) return redirect('/');



        try{
            DB::beginTransaction();
            $galeria = Gallery::findOrFail($request->id);
            $galeria->user_id     = $galeria->user_id;
            $galeria->nombre      = $request->nombre;
            $galeria->descripcion = $request->descripcion;
            $galeria->fecha_hora  = $galeria->fecha_hora;
            $galeria->lote        = $request->lote;
            $galeria->cover       = $request->cover;
            $galeria->area        = $request->area;
            $galeria->estado      = 'Vigente';
            $galeria->save();

            $enlaces = $request->enlaces;

            if(sizeof($enlaces) > 0){
                $userid = \Auth::user()->id;
                foreach($enlaces as $ep=>$enl){
                    $link = new Link(['user_id' => $userid, 'url' => $enl['url'], 'direction' => $enl['direction']]);
                    $galeria->links()->save($link);
                }
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }


        $galeriaUp = Gallery::findOrFail($galeria->id);

        return [
            'request' => $request->all(),
            'gallery' => $galeriaUp
        ];
    }

    public function desactivar(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $galeria = Gallery::findOrFail($request->id);
            $galeria->estado = 'Desactivada';
            $galeria->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    public function activar(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $galeria = Gallery::findOrFail($request->id);
            $galeria->estado = 'Vigente';
            $galeria->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    public function getLinks(Request $request){

        if (!$request->ajax()) return redirect('/');

        $galeria = Gallery::findOrFail($request->id);

        $links = $galeria->links()
        ->join('users','users.id','links.user_id')
        ->leftjoin('personas', 'users.id','=','personas.id')
        ->select('links.id','links.user_id as user','links.url','links.updated_at as fecha',
            'links.direction','personas.nombre')
        ->orderBy('links.updated_at','desc')
        ->get();

        return ['links' => $links];

    }

    public function deleteLink(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $link = Link::findOrFail($request->id);
            $link->delete();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    public function refreshGallery(Request $request){
        if (!$request->ajax()) return redirect('/');

        $galery = Gallery::join('users','users.id','galleries.user_id')
        ->leftjoin('personas', 'users.id','=','personas.id')
        ->select('galleries.id','galleries.nombre','galleries.descripcion',
            'galleries.lote','galleries.cover','galleries.fecha_hora',
            'galleries.estado','galleries.area','personas.nombre as usuario')
        ->where('galleries.id',$request->id)->first();

        return [ 'galery' => $galery ];
    }
}
