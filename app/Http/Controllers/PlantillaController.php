<?php

namespace App\Http\Controllers;

use App\Document;
use App\Plantilla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlantillaController extends Controller
{
    public function index(Request $request){
        //if(!$request->ajax()) return redirect('/');

        $usrol = \Auth::user()->idrol;
        $usid = \Auth::user()->id;
        $usarea = \Auth::user()->area;


            $plantillas = Plantilla::select('plantillas.title','plantillas.num_comprobante','plantillas.condicion',
            'plantillas.type','plantillas.ubicacion')
            ->orderBy('plantillas.id', 'desc')->paginate(10);



       return [
            'pagination' => [
                'total'        => $plantillas->total(),
                'current_page' => $plantillas->currentPage(),
                'per_page'     => $plantillas->perPage(),
                'last_page'    => $plantillas->lastPage(),
                'from'         => $plantillas->firstItem(),
                'to'           => $plantillas->lastItem(),
            ],
            'plantillas' => $plantillas,
            'userrol' => $usrol,
            'usid' => $usid,
            'usarea' => $usarea
        ];
    }
    public function store(Request $request){

        $plantilla = new Plantilla();
        $plantilla->title = $request->title;
        $plantilla->num_comprobante = $request->num_comprobante;
        $plantilla->condicion = 1;
        $plantilla->type->$request->type;
        $plantilla->ubicacion->$request->ubicacion;
        $plantilla->save();


    }
    public function filesUppload(Request $request){

        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            //The name of the directory that we need to create.
            $directoryName = 'plantillas';

            if(!is_dir($directoryName)){
                //Directory does not exist, so lets create it.
                mkdir($directoryName, 0777);
            }

            $archivos = $request->filesdata;//Array de archivos

            $docs = array();
            foreach($archivos as $ar=>$file){

                $exploded = explode(',', $file['url']);
                $decoded = base64_decode($exploded[1]);
                $extn = explode('/', $file['tipo']);
                $extension = $extn[1];
                $fileName = str_random().'.'.$extension;
                $path = public_path($directoryName).'/'.$fileName;
                file_put_contents($path,$decoded);

                $docum = new Document(['url' => $fileName, 'tipo' => $extension ]);
                $plantilla = Plantilla::findOrFail($request->id); //ID plantilla
                $plantilla->documents()->save($docum);
                DB::commit();
            }

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function getDocs(Request $request){

        if (!$request->ajax()) return redirect('/');
        $plantilla = Plantilla::findOrFail($request->id); //ID dueño de los archivos
        $files = $plantilla->documents()->get();

        return [
            'documentos' => $files
        ];
    }
    public function eliminarDoc(Request $request){
        if (!$request->ajax()) return redirect('/');

        //The name of the directory that we need to create.
        $directoryName = 'plantillas';

        try{
            DB::beginTransaction();

            $doc= Document::findOrFail($request->id);
            $img = $doc->url;

            if($img != null){
                $image_path = public_path($directoryName).'/'.$img;
                if(file_exists($image_path)){
                    unlink($image_path);
                }
            }

            $doc->delete();
            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }
    }
    public function obtenerCabecera(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $plantillas = Plantilla::select('plantillas.id','plantillas.num_comprobante',
            'plantillas.title','plantillas.condicion','plantillas.type','platillas.ubicacion')
        ->where('plantillas.id','=',$id)
        ->orderBy('plantillas.id', 'desc')->take(1)->get();

        return ['plantillas' => $plantillas];


        return ['plantillas' => $plantillas ];

    }
}
