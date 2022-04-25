<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/">Escritorio</a> </li>
    </ol>
    <div class="container-fluid p-1">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header" style="background-color:#fff;">
                <template v-if="listado==0">
                    <div class="form-inline">
                        <div class="form-group mb-2 col-sm-10">
                            <div class="input-group">
                                <select class="form-control mb-1" v-model="criterio">
                                    <option value="nombre">Nombre</option>
                                    <option value="descripcion">Descripción</option>
                                    <option value="lote">Lote</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <input type="text" v-model="buscar" @keyup.enter="listarGalleries(1,buscar,criterio,estadoGal)" class="form-control mb-1" placeholder="Texto a buscar">
                                <button type="submit" @click="listarGalleries(1,buscar,criterio,estadoGal)" class="btn btn-sm btn-primary mb-1 mt-0"><i class="fa fa-search"></i>Buscar</button>
                            </div>
                            <div class="input-group input-group-sm ml-xl-5">
                                <select class="form-control mb-1" id="tipofact" name="tipofact" v-model="estadoGal" @change="listarGalleries(1,buscar,criterio,estadoGal)">
                                    <option value="">Todo</option>
                                    <option value="Vigente">Activas</option>
                                    <option value="Desactivada">Desactivadas</option>
                                </select>
                                <button class="btn btn-sm btn-info mb-1 mt-0" type="button"><i class="fa fa-search" aria-hidden="true"></i>&nbsp; Filtros</button>
                            </div>
                        </div>
                        <div class="input-group float-right">
                            <div><button class="btn btn-primary btn-sm mb-1 mt-0" @click="newGallery()">Nuevo</button></div>
                        </div>
                    </div>
                </template>
                <template v-else-if="listado==2">
                    <div class="form-inline float-right">
                        <button class="btn btn-warning btn-sm mt-1 mr-2" @click="editGallery()" v-if="estado != 'Desactivada'">Editar</button>
                        <template v-if="estado == 'Vigente'">
                            <button class="btn btn-danger btn-sm mt-1" @click="desactivarGallery(galeria_id)">Desactivar</button>
                        </template>
                        <template v-else-if="estado=='Desactivada'">
                            <button class="btn btn-info btn-sm mt-1" @click="activarGallery(galeria_id)">Activar</button>
                        </template>

                    </div>
                </template>
            </div>
            <div class="card-body">
                <!-- List -->
                <template v-if="listado==0">
                   <!--  <h2>En construcción <i class="fa fa-cogs" aria-hidden="true"></i></h2> -->
                   <div class="form-group row" v-if="arrayGalerias.length">
                            <div class="col-12 d-flex justify-content-start b-b-1">
                                <div><h3>Galerías</h3></div>
                            </div>
                            <div class="col-12 col-md-12 mt-2">
                                <div>
                                    <div class="form-inline justify-content-around">
                                        <div v-for="gallery in arrayGalerias" :key="gallery.id">
                                            <div class="card mt-1 mr-3" style="width:400px">
                                                <div class="card-body p-0">
                                                    <template v-if="gallery.cover">
                                                        <lightbox class="m-0" album="" :src="'https://drive.google.com/uc?id='+gallery.cover">
                                                            <figure class="m-0">
                                                                <img class="img-responsive img-fluid imglinkList card-img-top" width="400" height="200"
                                                                    :src="'https://drive.google.com/uc?id='+gallery.cover" alt="Foto del enlace">
                                                            </figure>
                                                        </lightbox>
                                                    </template>
                                                    <template v-else>
                                                        <span class="badge badge-danger">URL NO VALIDA O ARCHIVO DAÑADO</span>
                                                    </template>
                                                    <h4 class="card-title m-1" v-text="gallery.nombre"></h4>
                                                    <p class="card-text m-1 galleryDesc" v-text="gallery.descripcion"></p>
                                                        <!-- <viewer class="card-text m-1 galleryDesc" :value="gallery.descripcion"/> -->
                                                    <button class="btn btn-primary btn-sm btnElimImg m-2" type="button" @click="verGallery(gallery)">Ver más...</button>
                                                </div>
                                                <div class="card-footer p-2">
                                                    <div v-if="gallery.estado == 'Vigente'">
                                                        <span class="badge badge-success">Vigente</span>
                                                    </div>
                                                    <div v-else-if="gallery.estado == 'Desactivada'">
                                                        <span class="badge badge-danger">Desactivada</span>
                                                    </div>
                                                    <span style="font-size:12px"> Area : {{ gallery.area }}</span>&nbsp;<br>
                                                    <span style="font-size:12px"> Registrado el {{ convertDate(gallery.fecha) }}</span>&nbsp;
                                                    <span style="font-size:12px">por {{ gallery.usuario }} </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoGal)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoGal)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoGal)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                </template>
                <!-- END List -->

                <!-- NEW GALLERY -->
                <template v-if="listado==1">
                    <div class="form-group row">
                        <div class="col-12">
                            <h2>Crear galería <i class="fa fa-picture-o" aria-hidden="true"></i></h2>
                        </div>
                    </div>
                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="row">
                            <div class="input-group input-group-sm col-12 col-lg-5 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Nombre</span>
                                </div>
                                <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de la galería"/>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Lote</span>
                                </div>
                                <input type="text" v-model="lote" class="form-control" placeholder="Lote o contenedor"/>
                            </div>

                            <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Area</span>
                                </div>
                                <select class="form-control" v-model="area">
                                    <option value='' disabled>Seleccione el area</option>
                                    <option value="GDL">GDL</option>
                                    <option value="Al Corte">SLP</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-8 mb-3">
                                <h5>Descripción : </h5>
                                <template class="justify-content-center">
                                        <editor :options="editorOptions" mode="wysiwyg" v-model="description" Width="100%"/>
                                </template>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg  mb-4" v-if="cover != ''">
                                <lightbox class="m-0" album="" :src="'https://drive.google.com/uc?id='+cover">
                                    <figure class="m-0">
                                        <img class="img-responsive img-fluid imglink" width="200" height="100"
                                            :src="'https://drive.google.com/uc?id='+cover" alt="Foto del enlace">
                                    </figure>
                                </lightbox>
                            </div>
                        </div>
                        <div v-show="errorGaleria" class="form-group row div-error">
                            <div class="text-center text-error">
                            <div v-for="error in errorMostrarMsjGaleria" :key="error" v-text="error"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 d-flex justify-content-start b-b-1">
                                <div><h3>Añadir Imagenes</h3></div>
                                <div>
                                    <button type="button" class="btn btn-primary rounded-circle btn-sm ml-2" @click="agregarLink()">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-6 mt-2" v-if="arrayLinks.length">
                                <div class="form-inline mt-1 col-12" v-for="(link,index) in arrayLinks" :key="link.id">
                                    <div class="input-group input-group-sm col-9">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Enlace {{ index + 1 }} </span>
                                        </div>
                                        <input type="text" v-model="link.url" class="form-control" placeholder="Enlace GoogleDrive" @change="getImageDrive(index,link.url)"/>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm mr-1" @click="getImageDrive(index,link.url)" v-if="link.url != ''">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm mr-1" @click="eliminarLink(index,link.direction)">&times;</button>
                                    <div class="form-group form-check" v-if="link.url">
                                        <input type="checkbox" class="form-check-input" :id="'chkPrt'+link.direction" :checked="link.direction==cover"
                                            @change="selectCover(link.direction)">
                                        <label class="form-check-label" :for="'chkPrt'+link.direction">Portada</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mt-2" v-if="arrayLinks.length">
                                <div>
                                    <div class="form-inline">
                                        <div v-for="(link,index) in arrayLinks" :key="link.id">
                                            <div class="card m-0 mr-3">
                                                <div class="card-body p-0">
                                                    <template v-if="link.direction">
                                                        <lightbox class="m-0" album="" :src="'https://drive.google.com/uc?id='+link.direction">
                                                            <figure class="m-0">
                                                                <img class="img-responsive img-fluid imglink" width="200" height="100"
                                                                    :src="'https://drive.google.com/uc?id='+link.direction" alt="Foto del enlace">
                                                            </figure>
                                                        </lightbox>
                                                    </template>
                                                    <template v-else>
                                                        <span class="badge badge-danger">VALIDAR LA IMAGEN</span>
                                                    </template>
                                                </div>
                                                <div class="card-footer p-2">Imagen enlace {{ index + 1 }}</div>
                                                <!-- <span>Imagen enlace {{ index + 1 }} </span> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary btn-sm float-right" @click="ocultarForm()">Cerrar</button>
                            <button type="button" class="btn btn-primary btn-sm float-right mr-2" @click="guardarGaleria()">Guardar</button>
                        </div>
                    </div>

                </template>
                <!-- END GALLERY -->

                <!-- See GALLERY -->
                <template v-if="listado==2">
                    <div class="form-group row">
                        <div class="col-12 text-center">
                            <h3 v-text="nombre"></h3>
                            <div v-if="estado=='Desactivada'">
                                <span style="font-size:20px" class="badge badge-danger">GALERÍA DESACTIVADA</span>
                            </div>
                        </div>&nbsp;
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                           <!--  <p style="text-align:justify;font-size:20px;" v-text="description"></p> -->
                            <viewer :value="description" width="600px"/>
                        </div>&nbsp;
                        <div class="col-12 d-flex justify-content-start">
                            <div class="mr-4" v-if="lote">
                                <h5>Lote : {{ lote }}</h5>
                            </div>
                            <div>
                                <h5><i style="color:red;" class="fa fa-map-marker"></i> Area: &nbsp; {{ area }} </h5>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 d-flex justify-content-start b-b-1">
                            <div><h3>Imagenes</h3></div>
                        </div>
                        <div class="col-12 col-md-12 mt-2" v-if="arrayImagenes.length">
                            <div>
                                <div class="form-inline">
                                    <div v-for="img in arrayImagenes" :key="img.id">
                                        <div class="card mt-1 mr-3" style="width:200px">
                                            <div class="card-body p-0">
                                                <template v-if="img.direction">
                                                    <lightbox class="m-0" album="" :src="'https://drive.google.com/uc?id='+img.direction">
                                                        <figure class="m-0">
                                                            <img class="img-responsive img-fluid imglink" width="200" height="100"
                                                                :src="'https://drive.google.com/uc?id='+img.direction" alt="Foto del enlace">
                                                        </figure>
                                                    </lightbox>
                                                </template>
                                                <template v-else>
                                                    <span class="badge badge-danger">URL NO VALIDA O ARCHIVO DAÑADO</span>
                                                </template>
                                            </div>
                                            <div class="card-footer p-2">
                                                <span style="font-size:12px"> Registrado el {{ convertDate(img.fecha) }}</span>&nbsp;
                                                <span style="font-size:12px">por {{ img.nombre }} </span>
                                                <button class="btn btn-danger btn-sm btnElimImg" @click="eliminarImgLink(img.id,galeria_id)">Eliminar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mt-2" v-else><h4>Añade imagenes del articulo!</h4></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" @click="ocultarForm()" class="btn btn-secondary">Cerrar</button>
                        </div>
                    </div>
                </template>
                <!-- End See GALLERY -->

                <!-- EDIT GALLERY -->
                <template v-if="listado==3">
                    <div class="form-group row">
                        <div class="col-12">
                            <h2>Crear galería <i class="fa fa-picture-o" aria-hidden="true"></i></h2>
                        </div>
                    </div>

                        <div class="row">
                            <div class="input-group input-group-sm col-12 col-lg-5 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Nombre</span>
                                </div>
                                <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de la galería"/>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Lote</span>
                                </div>
                                <input type="text" v-model="lote" class="form-control" placeholder="Lote o contenedor"/>
                            </div>

                            <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Area</span>
                                </div>
                                <select class="form-control" v-model="area">
                                    <option value='' disabled>Seleccione el area</option>
                                    <option value="GDL">GDL</option>
                                    <option value="Al Corte">SLP</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-8 mb-3">
                                <h5>Descripción : </h5>
                                <template class="justify-content-center">
                                        <editor :options="editorOptions" mode="wysiwyg" v-model="description" Width="100%"/>
                                </template>
                            </div>
                            <!-- <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Descripción</span>
                                </div>
                                <textarea class="form-control rounded-0" rows="3" v-model="description"></textarea>
                            </div> -->
                            <div class="input-group input-group-sm col-12 col-lg  mb-4" v-if="cover != ''">
                                <lightbox class="m-0" album="" :src="'https://drive.google.com/uc?id='+cover">
                                    <figure class="m-0">
                                        <img class="img-responsive img-fluid imglink" width="200" height="100"
                                            :src="'https://drive.google.com/uc?id='+cover" alt="Foto del enlace">
                                    </figure>
                                </lightbox>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 d-flex justify-content-start b-b-1">
                                <div><h3>Imagenes</h3></div>
                            </div>
                            <div class="col-12 col-md-12 mt-2" v-if="arrayImagenes.length">
                                <div>
                                    <div class="form-inline">
                                        <div v-for="img in arrayImagenes" :key="img.id">
                                            <div class="card mt-1 mr-3" style="width:200px">
                                                <div class="card-body p-0">
                                                    <template v-if="img.direction">
                                                        <lightbox class="m-0" album="" :src="'https://drive.google.com/uc?id='+img.direction">
                                                            <figure class="m-0">
                                                                <img class="img-responsive img-fluid imglink" width="200" height="100"
                                                                    :src="'https://drive.google.com/uc?id='+img.direction" alt="Foto del enlace">
                                                            </figure>
                                                        </lightbox>
                                                    </template>
                                                    <template v-else>
                                                        <span class="badge badge-danger">URL NO VALIDA O ARCHIVO DAÑADO</span>
                                                    </template>
                                                </div>
                                                <div class="card-footer p-2">
                                                    <span style="font-size:12px"> Registrado el {{ convertDate(img.fecha) }}</span>&nbsp;
                                                    <span style="font-size:12px">por {{ img.nombre }} </span>
                                                    <button class="btn btn-danger btn-sm btnElimImg" @click="eliminarImgLink(img.id,galeria_id)">Eliminar</button>
                                                    <div class="form-group form-check" v-if="img.url">
                                                        <input type="checkbox" class="form-check-input" :id="'chkPrt'+img.direction" :checked="img.direction==cover"
                                                            @change="selectCover(img.direction)">
                                                        <label class="form-check-label" :for="'chkPrt'+img.direction">Portada</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 mt-2" v-else><h4>Añade imagenes del articulo!</h4></div>
                        </div>
                        <div v-show="errorGaleria" class="form-group row div-error">
                            <div class="text-center text-error">
                            <div v-for="error in errorMostrarMsjGaleria" :key="error" v-text="error"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 d-flex justify-content-start b-b-1">
                                <div><h3>Añadir Imagenes</h3></div>
                                <div>
                                    <button type="button" class="btn btn-primary rounded-circle btn-sm ml-2" @click="agregarLink()">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-6 mt-2" v-if="arrayLinks.length">
                                <div class="form-inline mt-1 col-12" v-for="(link,index) in arrayLinks" :key="link.id">
                                    <div class="input-group input-group-sm col-9">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Enlace {{ index + 1 }} </span>
                                        </div>
                                        <input type="text" v-model="link.url" class="form-control" placeholder="Enlace GoogleDrive" @change="getImageDrive(index,link.url)"/>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm mr-1" @click="getImageDrive(index,link.url)" v-if="link.url != ''">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm mr-1" @click="eliminarLink(index,link.direction)">&times;</button>
                                    <div class="form-group form-check" v-if="link.url">
                                        <input type="checkbox" class="form-check-input" :id="'chkPrt'+link.direction" :checked="link.direction==cover"
                                            @change="selectCover(link.direction)">
                                        <label class="form-check-label" :for="'chkPrt'+link.direction">Portada</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mt-2" v-if="arrayLinks.length">
                                <div>
                                    <div class="form-inline">
                                        <div v-for="(link,index) in arrayLinks" :key="link.id">
                                            <div class="card m-0 mr-3">
                                                <div class="card-body p-0">
                                                    <template v-if="link.direction">
                                                        <lightbox class="m-0" album="" :src="'https://drive.google.com/uc?id='+link.direction">
                                                            <figure class="m-0">
                                                                <img class="img-responsive img-fluid imglink" width="200" height="100"
                                                                    :src="'https://drive.google.com/uc?id='+link.direction" alt="Foto del enlace">
                                                            </figure>
                                                        </lightbox>
                                                    </template>
                                                    <template v-else>
                                                        <span class="badge badge-danger">VALIDAR LA IMAGEN</span>
                                                    </template>
                                                </div>
                                                <div class="card-footer p-2">Imagen enlace {{ index + 1 }}</div>
                                                <!-- <span>Imagen enlace {{ index + 1 }} </span> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary btn-sm float-right" @click="ocultarForm()">Cerrar</button>
                            <button type="button" class="btn btn-primary btn-sm float-right mr-2" @click="updateGallery()">Guardar</button>
                        </div>
                    </div>

                </template>
                <!-- EDIT GALLERY -->

            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>
  </main>
</template>

<script>
import moment from 'moment';
import 'tui-editor/dist/tui-editor.css';
import 'tui-editor/dist/tui-editor-contents.css';
import 'codemirror/lib/codemirror.css';
import 'highlight.js/styles/github.css';
import Editor from '@toast-ui/vue-editor/src/Editor.vue';
import { Viewer } from '@toast-ui/vue-editor';
export default {
    data() {
        return {
            galeria_id: 0,
            nombre : '',
            description : '',
            lote : '',
            cover : '',
            fecha_hora : '',
            area: '',
            estado : '',
            listado : 0,
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            criterio : 'nombre',
            buscar : '',
            estadoGal : 'Vigente',
            areaGal : '',
            errorGaleria: 0,
            errorMostrarMsjGaleria: [],
            arrayGalerias : [],
            arrayLinks : [],
            arrayImagenes : [],
            editorOptions: {
                useCommandShortcut: true,
                useDefaultHTMLSanitizer: true,
                usageStatistics: true,
                hideModeSwitch: false,
                language: 'es_MX',
                toolbarItems: [
                    'heading',
                    'bold',
                    'italic',
                    'strike',
                    'divider',
                    'hr',
                    'quote',
                    'divider',
                    'ul',
                    'ol',
                    'task',
                    'indent',
                    'outdent',
                    'divider',
                    'table',
                    'link',
                    'divider',
                    'code',
                    'codeblock'
                ]
            }
        };
    },
    components: {
        'editor': Editor,
        'viewer': Viewer
    },
    computed:{
        isActived: function(){
            return this.pagination.current_page;
        },
        pagesNumber: function() {
            if(!this.pagination.to) {
                return [];
            }

            var from = this.pagination.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.pagination.last_page){
                to = this.pagination.last_page;
            }

            var pagesArray = [];
            while(from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },
    methods: {

        listarGalleries(page,buscar,criterio,estado){
            let me=this;
            var url= '/gallery?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado=' + estado;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayGalerias = respuesta.galerias.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio,estado){
            let me = this;
            me.pagination.current_page = page;
            me.listarGalleries(page,buscar,criterio,estado);
        },
        convertDate(date){
            moment.locale('es');
            let me=this;
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            return datec;
        },
        newGallery(){
            let me = this;
            me.listado = 1;
            me.galeria_id = 0;
            me.nombre = '';
            me.description = '';
            me.lote = '';
            me.cover = '';
            me.fecha_hora = '';
            me.area= '';
            me.estado = '';
            me.errorMostrarMsjGaleria= [];
            me.arrayGalerias = [];
            me.arrayLinks = [];

        },
        ocultarForm(){
            let me = this;
            me.listado = 0;
            me.galeria_id = 0;
            me.nombre = '';
            me.description = '';
            me.lote = '';
            me.cover = '';
            me.fecha_hora = '';
            me.area= '';
            me.estado = '';
            me.errorMostrarMsjGaleria= [];
            me.arrayGalerias = [];
            me.arrayLinks = [];
            me.arrayImagenes = [];
            me.listarGalleries(me.pagination.current_page,me.buscar,me.criterio,me.estadoGal);
        },
        agregarLink(){
            let me = this;
            me.arrayLinks.push({ url : '', direction : ''});
        },
        eliminarLink(index,direction){
            let me = this;
            if(direction == me.cover) me.cover = '';
            me.arrayLinks.splice(index,1);
        },
        getImageDrive(index,url){
            var convert1 = url.split("d/");
            var convert2 = convert1[1].split("/");
            this.arrayLinks[index]['direction'] = convert2[0];
        },
        selectCover(link){
            this.cover = link;
        },
        validarGaleria(){

            this.errorGaleria = 0;
            this.errorMostrarMsjGaleria = [];

            if (this.nombre=='') this.errorMostrarMsjGaleria.push("Ingrese el nombre de la galeria.");
            if (!this.description) this.errorMostrarMsjGaleria.push("Ingrese la descrpipción corta de la galería.");
            if (!this.area) this.errorMostrarMsjGaleria.push("Seleccione el area de la galería.");

            if (this.arrayLinks.length <= 0) this.errorMostrarMsjGaleria.push("Debe enlazar al menos una imagen");
            if (this.errorMostrarMsjGaleria.length) this.errorGaleria = 1;

            if(this.arrayLinks.length){
                this.arrayLinks.forEach(element => {
                    if(element.url == ''){
                        Swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: 'Hay un enlace vacío!',
                        });
                        this.errorGaleria = 1;
                    }
                });
            }


            return this.errorGaleria;
        },
        validarGaleriaUpdt(){
            this.errorGaleria = 0;
            this.errorMostrarMsjGaleria = [];

            if (this.nombre=='') this.errorMostrarMsjGaleria.push("Ingrese el nombre de la galeria.");
            if (!this.description) this.errorMostrarMsjGaleria.push("Ingrese la descrpipción corta de la galería.");
            if (!this.area) this.errorMostrarMsjGaleria.push("Seleccione el area de la galería.");

            if (this.errorMostrarMsjGaleria.length) this.errorGaleria = 1;

            if(this.arrayLinks.length){
                this.arrayLinks.forEach(element => {
                    if(element.url == ''){
                        Swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: 'Hay un enlace vacío!',
                        });
                        this.errorGaleria = 1;
                    }
                });
            }

            return this.errorGaleria;
        },
        guardarGaleria(){
            let me = this;

            if (this.validarGaleria()) {
                return;
            }

            if(me.cover == ''){
                me.cover = me.arrayLinks[0]['direction'];
            }

            //console.log(`Cover NS : ${me.cover}`);

            axios.post("/gallery/registrar",{
                'nombre': this.nombre,
                'descripcion': this.description,
                'lote' : this.lote,
                'cover' : this.cover,
                'area' : this.area,
                'enlaces' : this.arrayLinks
            }).then(function (response) {
                me.listarGalleries(me.pagination.current_page,me.buscar,me.criterio,me.estadoGal);
                me.ocultarForm();
            }).catch(function (error) {
                console.log(error);
            });
        },
        verGallery(data = []){
            let me = this;
            me.galeria_id = data['id'];
            me.nombre = data['nombre'];
            me.description = data['descripcion'];
            me.lote = data['lote'];
            me.cover = data['cover'];
            me.area = data['area'];
            me.estado =  data['estado'];
            me.listado = 2;
            me.getLinks(data['id']);
        },
        getLinks(id){
            let me = this;
            var url= '/gallery/getLinks?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayImagenes = respuesta.links;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        editGallery(){
            let me = this;
            me.listado = 3;
        },
        updateGallery(){
            let me = this;

            if (this.validarGaleriaUpdt()) {
                return;
            }

            axios.put("/gallery/actualizar",{
                'id' : this.galeria_id,
                'nombre': this.nombre,
                'descripcion': this.description,
                'lote' : this.lote,
                'cover' : this.cover,
                'area' : this.area,
                'enlaces' : this.arrayLinks
            }).then(function (response) {
                Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'Actualizado con éxito!',
                });
                me.listarGalleries(me.pagination.current_page,me.buscar,me.criterio,me.estadoGal);
                me.ocultarForm();
            }).catch(function (error) {
                console.log(error);
            });
        },
        eliminarImgLink(id,idgallery){
            let me = this;
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro eliminar esta imagen?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/articulo/deleteLink', {
                        'id' : id
                    }).then(function(response) {
                         me.getLinks(idgallery);
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "La imagen ha sido eliminada con éxito.",
                            "success"
                        );
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            });
        },
        desactivarGallery(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de desactivar esta galería?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/gallery/desactivar', {
                        'id' : id
                    }).then(function(response) {
                       /*  me.listarGalleries(me.pagination.current_page,me.buscar,me.criterio,me.estadoGal); */
                        me.refreshGallery(id);
                        swalWithBootstrapButtons.fire(
                            "Desactivado!",
                            "La galería ha sido desactivada con éxito.",
                            "success"
                        );
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            });
        },
        activarGallery(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de activar esta galería?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/gallery/activar', {
                        'id' : id
                    }).then(function(response) {
                        me.refreshGallery(id);
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "Galería activado con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        refreshGallery(id){
            let me = this;
            var url= '/gallery/refreshGallery?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                var FreshGalle = respuesta.galery;
                me.verGallery(FreshGalle);
            })
            .catch(function (error) {
                console.log(error);
            });
        }
    },
    mounted() {
        //console.log(`Component Gallery Mounted`);
        this.listarGalleries(1,this.buscar, this.criterio,this.estadoGal);
    }
};
</script>

<style>
.imglinkList{
    max-width : 400px !important;
    max-height: 300px !important;
}
.galleryDesc{
    white-space: nowrap;
    width: 350px;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
