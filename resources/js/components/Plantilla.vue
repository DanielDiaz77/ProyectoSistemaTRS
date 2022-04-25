<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Escritorio</a> </li>
    </ol>
    <div class="container-fluid">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> Plantillas
               <button type="button" @click="abrirModal()" class="btn btn-secondary">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>
        <!-- Listado -->
        <template v-if="listado==0">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-12">
                    </div>
                </div>
                <div class="table-responsive col-md-12">
                    <table class="table table-bordered table-striped table-sm table-hover">
                        <thead>
                        <tr>
                            <th>Index</th>
                            <th>Opciones</th>
                            <th>Titulo</th>
                            <th>Estado</th>
                            <th>Tipo de archivo</th>
                        </tr>
                        </thead>
                        <tbody v-if="arrayPlantilla.length">
                            <tr v-for="(plantilla,index) in arrayPlantilla" :key="plantilla.id">
                                <td width="10px">{{ (index + 1) }}</td>
                                <td>
                                    <div class="form-inline">
                                        <button type="button" class="btn btn-success btn-sm" @click="verPlantilla(plantilla)">
                                            <i class="icon-eye"></i>
                                        </button>&nbsp;
                                        <button type="button" @click="abrirModal2(plantilla)" class="btn btn-warning btn-sm">
                                            <i class="icon-pencil"></i>
                                        </button> &nbsp;
                                        <template v-if="plantilla.condicion == 1 && usrol == 1">
                                            <button type="button" class="btn btn-danger btn-sm" @click="desactivarPlantilla(plantilla.id)">
                                                <i class="icon-trash"></i>
                                            </button>&nbsp;
                                        </template>
                                    </div>
                                </td>
                                <td v-text="plantilla.title"></td>
                                <td>
                                    <div v-if="plantilla.condicion">
                                        <span class="badge badge-success">Activo</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Desactivado</span>
                                    </div>
                                </td>
                                <td>
                                    <div v-if="plantilla.type == 'PDF'">
                                        <span class="btn btn-outline-danger btn-sm">
                                            <i class="fa fa-file-pdf-o"></i>PDF
                                        </span>
                                    </div>
                                    <div v-if="plantilla.type == 'Excell'">
                                        <span class="btn btn-outline-success btn-sm">
                                            <i class="fa fa-file-excel-o"></i>Excell
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <strong>NO hay materiales registrados con ese criterio...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!--Ver Plantillas-->
        <template v-else-if="listado == 2">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Titulo</strong></label>
                            <p v-text="title"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Número de Documento</strong></label>
                            <p v-text="num_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Estado</strong></label>
                            <div v-if="estado == 1">
                                <span class="badge badge-success">Activo</span>
                            </div>
                            <div v-else-if="estado == 0">
                                <span class="bad badge-danger">Eliminado</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo de Archivo</strong></label>
                            <div v-if="type == 'PDF'">
                                <span class="badge badge-danger">PDF</span>
                            </div>
                            <div v-else-if="type == 'Excell'">
                                <span class="bad badge-success">Excell</span>
                            </div>
                            <div v-else-if="type == 'Word'">
                                <span class="bad badge-info">Word</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Area</strong></label>
                            <div v-if="ubicacion == 'Guadalajara'">
                                <span class="badge badge-primary">Guadalajara</span>
                            </div>
                            <div v-else-if="ubicacion == 'San luis'">
                                <span class="badge badge-primary">San Luis</span>
                            </div>
                            <div v-else-if="ubicacion == 'Aguascalientes'">
                                <span class="badge badge-primary">Aguascalientes</span>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="form-group row-boder">
                    <div class="col-md-2">
                        <div class="form-group" v-if="title == 'Diagrama de Carga'">
                                <iframe src="https://docs.google.com/viewer?url=http://inventariostroystone.com/images/traslados/Diagramacarga.pdf&embedded=true"
                                style="width:500%; height:900px;" frameborder="0"></iframe>
                        </div>
                        <template v-if="title == 'Lista de Precios' && usrol == 1 ">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <iframe width="500" height="500" frameborder="0" scrolling="no" src="https://onedrive.live.com/embed?resid=9E75F4F69811EAAF%21472&authkey=%21ALxwC0fNTlslYwM&em=2&wdInConfigurator=True"></iframe>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <iframe width="500" height="500" frameborder="0" scrolling="no" src="https://onedrive.live.com/embed?resid=9E75F4F69811EAAF%21474&authkey=%21AL4bWYgQJvdzGI4&em=2&ActiveCell='Hoja1'!A1&wdDownloadButton=True&wdInConfigurator=True"></iframe>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <iframe width="500" height="500" frameborder="0" scrolling="no" src="https://onedrive.live.com/embed?resid=9E75F4F69811EAAF%21475&authkey=%21AJoFrdZFgvbR-yo&em=2&
                                    ActiveCell='Hoja1'!A1&wdDownloadButton=True&wdInConfigurator=True"></iframe>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="form-group" v-if="title == 'Lista de Precios' && area == 'GDL'  ">
                                <iframe width="700" height="800" frameborder="0" scrolling="no" src="https://onedrive.live.com/embed?resid=9E75F4F69811EAAF%21472&authkey=%21ALxwC0fNTlslYwM&em=2&wdInConfigurator=True"></iframe>
                            </div>
                            <div class="form-group" v-else-if="title == 'Lista de Precios' && area == 'SLP'">
                                <iframe width="700" height="900" frameborder="0" scrolling="no" src="https://onedrive.live.com/embed?resid=9E75F4F69811EAAF%21474&authkey=%21AL4bWYgQJvdzGI4&em=2&ActiveCell='Hoja1'!A1&wdDownloadButton=True&wdInConfigurator=True"></iframe>
                            </div>
                            <div class="form-group" v-else-if="title == 'Lista de Precios' && area == 'AGS'">
                              <iframe width="700" height="900" frameborder="0" scrolling="no" src="https://onedrive.live.com/embed?resid=9E75F4F69811EAAF%21475&authkey=%21AJoFrdZFgvbR-yo&em=2&
                              ActiveCell='Hoja1'!A1&wdDownloadButton=True&wdInConfigurator=True"></iframe>
                            </div>
                        </template>
                        <div class="form-group" v-if="title == 'Inspeccion de placa'">
                                <iframe src="https://docs.google.com/viewer?url=http://inventariostroystone.com/images/traslados/Controlplacas.pdf&embedded=true"
                                style="width:500%; height:900px;" frameborder="0"></iframe>
                        </div>
                        <div class="form-group" v-if="title == 'Diagrama de Corte'">
                                <iframe src="https://docs.google.com/viewer?url=http://inventariostroystone.com/images/traslados/Diagramacorte.pdf&embedded=true"
                                style="width:500%; height:900px;" frameborder="0"></iframe>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
     <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content content-task">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                <!-- TAB CLIENTE -->
                                <div class="tab-pane active" id="tabCliente" role="tab panel">
                                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Titulo</span>
                                                </div>
                                                <input type="text" v-model="title">
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-8 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Tipo de Documento</span>
                                                </div>
                                                <input type="text" v-model="type" class="form-control" placeholder="Palabra Clave" >
                                            </div>
                                        </div>
                                        <div v-show="errorPlantilla" class="form-group row div-error">
                                            <div class="text-center text-error">
                                            <div v-for="error in errorMostrarMsjPlantilla" :key="error" v-text="error"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="d-block d-sm-block d-md-none">
                    <div class="float-right d-block d-sm-block d-md-none">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                    <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal agregar/actualizar-->
  </main>
</template>

<script>
import vSelect from 'vue-select';
import VueBarcode from 'vue-barcode';
import VueLightbox from 'vue-lightbox';
import moment from 'moment';
import ToggleButton from 'vue-js-toggle-button';
import datePicker from 'vue-bootstrap-datetimepicker';
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton);
Vue.use(datePicker);
export default {
    data() {
        return {
            idusuario :0,
            plantilla_id: 0,
            usrol : 0,
            nombre: "",
            descripcion: "",
            arrayPlantilla: [],
            modal: 0,
            title: "",
            ubicacion : '',
            condicion:0,
            tituloModal: "",
            tipoAccion: 0,
            area : "",
            user: '',
            userarea:"",
            errorPlantilla: 0,
            errorMostrarMsjPlantilla: [],
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            type: "",
            criterio : 'nombre',
            buscar : '',
            listado : 0,
            pagination_sku : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            arraySku : [],
            buscarsku : '',
            nom_category : '',
            pagination_art : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            arrayArticulo : [],
            buscar_art : "",
            criterio_art : "codigo",
            bodega_art : "",
            acabado_art : "",
            estado_art : 1,
            sku : "",
            num_comprobante: '',
            arrayFiles : [],
            docsArray : [],
            userarea: "",
        };
    },
    components:{
        vSelect,
        'barcode': VueBarcode
    },
    computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
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
            },
            isActivedSKU: function(){
                return this.pagination_sku.current_page;
            },
            pagesNumberSKU: function() {
                if(!this.pagination_sku.to) {
                    return [];
                }

                var from = this.pagination_sku.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2);
                if(to >= this.pagination_sku.last_page){
                    to = this.pagination_sku.last_page;
                }

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            },
            isActivedART: function(){
                return this.pagination_art.current_page;
            },
            pagesNumberART: function() {
                if(!this.pagination_art.to) {
                    return [];
                }

                var from = this.pagination_art.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2);
                if(to >= this.pagination_art.last_page){
                    to = this.pagination_art.last_page;
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
        listarPlantilla (page,buscar){
            let me=this;
            var url= '/plantilla?page=' + page + '&buscar='+ buscar;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayPlantilla = respuesta.plantillas.data;
                me.pagination= respuesta.pagination;
                me.usrol = respuesta.userrol;
                me.usid = respuesta.usid;
                me.userarea = respuesta.userarea;

            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarPlantilla(page,buscar,criterio);
        },
        registrarCategoria() {
            if (this.validarPlantilla()) {
                return;
            }

            let me = this;

            axios.post("/categoria/registrar", {
                nombre: this.nombre,
                descripcion: this.descripcion
            })
            .then(function(response) {
                me.cerrarModal();
                me.listarPlantilla(1,'','nombre');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        validarPlantilla(){
            let me = this;
            me.errorPlantilla = 0;
            me.errorMostrarMsjPlantilla = [];

            if (me.title==0) me.errorMostrarMsjPlantilla.push("El Titulo no puede estar Vacio");
            if (!me.num_comprobante) me.errorMostrarMsjPlantilla.push("Ingrese el numero de comprobante");
            if (me.errorMostrarMsjPlantilla.length) me.errorPlantilla = 1;

            return me.errorPlantilla;
        },
        registrarPlantilla(){
            if (this.validarPlantilla()){
                return;
            }
            let me = this;
            let date = "";
            moment.locale('es');
            date = moment().format('YYMMDD');

            var rand = Math.round(Math.random() * (99 - 999));

            var numcomp = "P-".concat(date,rand);

            axios.post("/plantilla/registrar", {
                'title' : this,title,
                'num_comprobante' : numcomp,
                'condicion' : 1,
                'type' : this.type
            })
            .then(function (response){
                swal.fiere(
                    'Registrado!',
                    'La Plantilla se ha registrado con éxito',
                    'success');
                me.cerrarModal();
                me.listarPlantilla(1);
            })
            .catch(function(error){
                console.log(error);
            })
        },
        actualizarCategoria() {
            if (this.validarCategoria()) {
                return;
            }
            let me = this;
            axios.put("/categoria/actualizar", {
                nombre: this.nombre,
                descripcion: this.descripcion,
                id: this.plantilla_id
            })
            .then(function(response) {
                me.cerrarModal();
                me.listarPlantilla(1,'','nombre');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        desactivarCategoria(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de desactivar esta categoría?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/categoria/desactivar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarPlantilla(1,'','nombre');
                        swalWithBootstrapButtons.fire(
                            "Desactivado!",
                            "La categoría ha sido desactivada con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        activarCategoria(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de activar esta categoría?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/categoria/activar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarPlantilla(1,'','nombre');
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "La categoría ha sido activada con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        verPlantilla(data = []){
            this.listado = 2
            this.plantilla_id = data['id'];
            this.title = data['title'];
            this.num_comprobante = data['num_comprobante']
            this.type = data['type'];
            this.estado = data['condicion'];
            this.ubicacion = data['ubicacion'];
        },
        ocultarDetalle(){
            this.listado = 0;
            this.listarPlantilla(this.pagination.current_page)
        },
        eliminarFile(id,idplantilla){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro eliminar este documento?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/plantilla/eliminarDoc', {
                        'id' : id
                    }).then(function(response) {
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El documento ha sido eliminada con éxito.",
                            "success"
                        );
                        me.getDocs(idplantilla);
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        downloadDoc(file){
            window.open('plantillas/'+file);
        },
        fieldChange(e){
            let selectedFilesTemp = e.target.files;
            for(var i=0;i<selectedFilesTemp.length;i++){
                let upFile = e.target.files[i];
                let type = e.target.files[i]["type"];
                this.cargarFiles(upFile,type);
            }
        },
        cargarFiles(img,type){
            let reader = new FileReader();
            reader.onload = (e) => {
                this.arrayFiles.push({
                    url : e.target.result,
                    tipo : type
                });
            }
            reader.readAsDataURL(img);
        },
        guardarFiles(){
            let me = this;
            var plantilla = this.plantilla_id;
            axios.put('/plantilla/filesupplo',{
                'id' : this.plantilla_id,
                'filesdata': this.arrayFiles
            }).then(function(response) {
                swal.fire(
                'Completado!',
                'Los archivos fueron guardados con éxito.',
                'success');
                me.arrayFiles = [];
                me.getDocs(plantilla);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        getDocs(plantilla_id){
            let me = this;
            var url= '/plantilla/getDocs?id=' + plantilla_id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.docsArray = respuesta.documentos;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cerrarModal() {
            this.modal = 0;
            this.tituloModal = "";
            this.nombre = "";
            this.descripcion = "";
        },
        abrirModal() {
           let me =this;

           me.modal=1;
           me.tituloModal = "Registrar Plantilla";
           me.cerrarModal();
           me.listarPlantilla(1);
        },
        listarSkuCategoria(page,id,buscar){
            let me=this;
            var url= '/articulo/listByCategory?page=' + page + '&id='+ id + '&buscar='+ buscar;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arraySku = respuesta.articulos.data;
                me.pagination_sku = respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPaginaSku(page,id,buscar){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarSkuCategoria(page,id,buscar);
        },
        getListSku(id,category){
            this.listado = 1;
            this.cambiarPaginaSku(1,id,'');
            this.plantilla_id = id;
            this.nom_category = category;
        },
        cerrarDetalleSku(){
            this.listado = 0;
            this.arraySku = [];
            this.pagination_sku =
            {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            };
            this.plantilla_id = 0;
            this.nom_category = "";
            this.listarPlantilla(1,'','nombre');
        },
        getListArticulos(sku){
            this.listado = 2;
            this.sku = sku;
            this.listarArticulosCategoria(sku,1,'','codigo','','',1);
        },
        cerrarListaArt(){
            this.listado = 1;
            this.arrayArticulo = [];
            this.pagination_art =
            {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            };
            this.sku = "";
        },
        listarArticulosCategoria(sku,page,buscar,criterio,bodega,acabado,estado){
            let me=this;
            var url= '/articulo/listBySku?sku='+ sku + '&page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&bodega='
            + bodega + '&acabado=' + acabado + '&estado=' + estado;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayArticulo = respuesta.articulos.data;
                me.pagination_art= respuesta.pagination;
                /* me.zona = respuesta.userarea; */
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPaginaArt(sku,page,buscar,criterio,bodega,acabado,estado){
            let me = this;
            //Actualiza la página actual
            me.pagination_art.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarArticulosCategoria(sku,page,buscar,criterio,bodega,acabado,estado);
        }
    },
    mounted() {
        this.listarPlantilla(1,this.buscar, this.criterio);
    }
};
</script>
<style>
    .modal-content {
      width: 100% !important;
      position: absolute !important;
    }
    .mostrar {
      display: list-item !important;
      opacity: 1 !important;
      position: absolute !important;
      background-color: #3c29297a !important;
    }
    .div-error {
      display: flex;
      justify-content: center;
    }
    .text-error {
      color: red !important;
      font-weight: bold;
    }
    .content-category{
        height: 350px !important;
    }
</style>
