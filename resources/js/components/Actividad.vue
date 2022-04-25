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
                <i class="fa fa-align-justify"></i> Tareas
                <button type="button" @click="newTask()" class="btn btn-secondary" v-if="!OpenDet">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <!-- Listado de  Actividades -->
            <template v-if="listado == 0">
                <div class="card-body">
                    <div class="form-inline">
                        <div class="form-group mb-2 col-12">
                            <!-- <div class="input-group">
                                <select class="form-control mb-1" v-model="criterio">
                                    <option value="title">Actividad</option>
                                    <option value="emisor">Emisor</option>
                                    <option value="receptor">Receptor</option>
                                </select>
                            </div> -->
                            <!-- <div class="input-group">
                                <input type="text" v-model="buscar" @keyup.enter="listarActividades(1,buscar,criterio,estado)" class="form-control mb-1" placeholder="Texto a buscar">
                            </div> -->
                            <div class="input-group">
                                <select class="form-control mb-1" v-model="estado" @change="listarActividades(1,buscar,criterio,estado)">
                                    <option value="0">Incompleta</option>
                                    <option value="1">Completada</option>
                                    <option value="2">Cancelada</option>
                                </select>
                                <button class="btn btn-primary mb-1"><i class="fa fa-search"></i> Filtro</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Emisor</th>
                                <th>Actividad</th>
                                <th>Detalles</th>
                                <th>Fecha Compromiso</th>
                                <th>Completado</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayActividad.length">
                                <tr v-for="actividad in arrayActividad" :key="actividad.id">
                                    <td>
                                        <div class="form-inline">
                                            <button type="button" @click="verActividad(actividad)" class="btn btn-success btn-sm">
                                                <i class="icon-eye"></i>
                                            </button> &nbsp;
                                            <template v-if="actividad.status == 0">
                                                <button type="button" @click="editTask(actividad)" class="btn btn-warning btn-sm">
                                                    <i class="icon-pencil"></i>
                                                </button> &nbsp;
                                                <button type="button" class="btn btn-danger btn-sm" @click="desactivarActividad(actividad.id)">
                                                    <i class="icon-trash"></i>
                                                </button>&nbsp;
                                            </template>
                                        </div>
                                    </td>
                                    <td v-text="actividad.emisor"></td>
                                    <td v-text="actividad.title"></td>
                                    <td v-text="actividad.content"></td>
                                    <td> {{ convertDate(actividad.end) }} </td>
                                    <td class="text-center">
                                        <input type="checkbox" :id="'chk'+actividad.id" v-model="actividad.status"
                                            @change="cambiarEstado(actividad.id,actividad.status)" v-if="actividad.status != 2">
                                        <template v-if="actividad.status == 1">
                                            <label :for="'chk'+actividad.id">Completada</label>
                                        </template>
                                        <template v-else-if="actividad.status == 0">
                                            <label :for="'chk'+actividad.id">Incompleta</label>
                                        </template>
                                        <template v-else-if="actividad.status == 2">
                                            <span class="badge badge-danger">Cancelada</span>
                                        </template>
                                    </td>
                                    <td>
                                        <div v-if="actividad.status == 0">
                                            <span class="badge badge-warning">Incompleta</span>
                                        </div>
                                        <div v-else-if="actividad.status == 1">
                                            <span class="badge badge-success">Completada</span>
                                        </div>
                                        <div v-else-if="actividad.status == 2">
                                            <span class="badge badge-danger">Cancelada</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <strong>NO hay actividades registradas o con ese criterio...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estado)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estado)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estado)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </template>
            <!-- Fin Listado de  Actividades -->

            <!-- Editar/Nueva actividad -->
            <template v-if="listado == 1">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-12 text-center">
                           <h3 v-text="tituloDetalle"></h3>
                        </div>&nbsp;
                    </div>
                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">

                        <div class="row d-flex justify-content-center">
                            <div class="input-group input-group-sm col-12 col-lg-9 col-xl-6 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Receptores</span>
                                </div>
                                <v-select multiple v-model="selectedUsers" :on-search="selectReceptor" label="nombre" :options="arrayReceptores" placeholder="Buscar usuarios...">
                                </v-select>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center">
                            <div class="input-group input-group-sm col-12 col-lg-9 col-xl-3  mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Titulo</span>
                                </div>
                                <input type="text" v-model="title" class="form-control" placeholder="Título de la actividad"/>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-9 col-xl-3  mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Fecha Compromiso </span>
                                </div>
                                <date-picker name="date" v-model="end" :config="options"></date-picker>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="col-10">
                                <template class="justify-content-center">
                                    <editor :options="editorOptions" mode="wysiwyg" v-model="content" Width="100%"/>
                                </template>
                            </div>
                        </div>
                        <div v-show="errorActividad" class="form-group row div-error d-flex justify-content-center">
                            <div class="text-center text-error">
                            <div v-for="error in errorMostrarMsjActividad" :key="error" v-text="error"></div>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="col-12 col-lg-6 mb-3">
                                <button type="button" @click="cerrarDetalle()"  class="btn btn-secondary float-right">Cancelar</button>
                                <button v-if="TaskNew" type="button" class="btn btn-primary float-right mr-2" @click="registrarActividad()">Guardar</button>
                                <button v-if="TaskEdit" type="button" class="btn btn-primary float-right mr-2" @click="actualizarActividad()">Actualizar</button>
                            </div>
                        </div>
                    </form>
                    <br><br><br><br><br>
                </div>
            </template>
            <!-- Fin editar/nueva actividad -->

            <!-- Ver actividad y segumiento -->
            <template v-if="listado == 2">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-12 text-center">
                           <template v-if="status == 0">
                                <span style="font-size: 20px;" class="badge badge-warning">
                                    Incompleta <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                </span>
                            </template>
                            <template v-else-if="status == 1">
                                <span style="font-size: 20px;" class="badge badge-success">
                                    Completada <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                </span>
                            </template>
                            <template v-else-if="status == 2">
                                <span style="font-size: 20px;" class="badge badge-danger">
                                    Cancelada <i class="fa fa-times-circle-o" aria-hidden="true"></i>
                                </span>
                            </template>
                        </div>&nbsp;
                        <div class="col-12 text-center">
                           <h3 v-text="title"></h3>
                        </div>&nbsp;
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 col-sm-4 mb-3 text-center">
                            <viewer :value="content" width="600px"/>
                        </div>
                        <div class="col-12 col-sm-2 text-center mb-3 mt-5">
                            <h5 class="text-center">Actividad para: </h5>
                            <ul id="involvedUsers" v-for="usuario in selectedUsers" :key="usuario.id">
                                <li>
                                    <span style="font-size: 15px;" class="badge badge-primary">{{usuario.nombre}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 text-center mb-3">
                           <span style="font-size: 15px;"><strong>Fecha compromiso: </strong> {{ convertDate(end)}}</span>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 text-center">
                           <span style="font-size: 15px;"><strong>Creado por: </strong> {{ nom_emisor }} el dia {{ convertDate(start) }}</span>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center mt-3">
                        <div class="col-12 col-md-8 col-lg-8 col-xl-6 offset-md-2 offset-xl-0">
                            <div class="d-flex flex-column">
                                <div>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>Comentarios </h3>
                                        </div>
                                        <div>
                                            <button class="btn btn-primary rounded-circle" @click="newComment()"><i class="fa fa-plus-circle"></i></button>
                                        </div>
                                    </div>
                                    <!-- New Comment Box -->
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-12 col-sm-8" :class="{'showNewComment' : CommentNew}" style="display: none;">
                                            <!-- <form action method="post" enctype="multipart/form-data" class="form-horizontal"> -->
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Comentario</span>
                                                    </div>
                                                    <textarea class="form-control rounded-1" rows="8" maxlength="255" v-model="commentBody"></textarea>
                                                </div>
                                                <button class="btn btn-primary mt-2 float-right" @click="saveComment(activity_id)" v-if="commentBody && itsCommentNew">Guardar</button>
                                                <button class="btn btn-primary mt-2 float-right" @click="updateComment(activity_id)" v-if="commentBody && itsCommentUpd">Actualizar</button>
                                            <!-- </form> -->
                                            <button class="btn btn-secondary mt-2 mr-1 float-right" @click="cancelComment()">Cancelar</button>
                                        </div>
                                    </div>
                                    <!-- End new Comment Box -->
                                    <hr>
                                </div>
                                <div>
                                <div class="divtask" v-if="arrayComentarios.length">
                                    <ul class="row" v-for="comment in arrayComentarios" :key="comment.id">
                                            <li class="col-md-6" style="list-style:none;">
                                                <div class="form-group d-flex justify-content-center">
                                                    <div class="col-md my-3 pt-3 caja">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                 <h5 v-text="comment.nombre"></h5>
                                                            </div>
                                                            <div class="col-md">
                                                                <p><small style="font-size:10px;" class="text-muted"><i class="fa fa-clock-o"></i> {{ convertDate(comment.fecha) }}</small></p>
                                                            </div>
                                                            <div class="col-md">
                                                                <template v-if="comment.user == user_id">
                                                                    <button type="button" class="btn btn-sm btntask float-right" @click="editComment(comment)">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </button>&nbsp;
                                                                    <button type="button" class="btn btn-sm btntask float-right" @click="deleteComentario(comment.id,activity_id)">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>&nbsp;
                                                                </template>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <p v-text="comment.body"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                </div>
                                <div v-else style="height: auto !important;">
                                        <h5>Sin Comentaríos...</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 col-lg-6 mb-3">
                            <button type="button" @click="cerrarVerAct()"  class="btn btn-secondary float-right">Cerrar</button>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Fin ver actividad y segumiento -->

        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>
  </main>
</template>

<script>
import moment from 'moment';
import datePicker from 'vue-bootstrap-datetimepicker';
import vSelect from 'vue-select';
import 'tui-editor/dist/tui-editor.css';
import 'tui-editor/dist/tui-editor-contents.css';
import 'codemirror/lib/codemirror.css';
import 'highlight.js/styles/github.css';
import Editor from '@toast-ui/vue-editor/src/Editor.vue';
import { Viewer } from '@toast-ui/vue-editor'

Vue.use(datePicker);
export default {
    data() {
        return {
            activity_id : 0,
            title : "",
            content : "",
            start : "",
            end : "",
            status : 0,
            idemisor : 0,
            idreceptor : 0,
            arrayActividad: [],
            errorActividad: 0,
            errorMostrarMsjActividad: [],
            arrayReceptores : [],
            arrayComentarios : [],
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            criterio : 'title',
            buscar : '',
            estado : 0,
            listado : 0,
            tituloDetalle : "",
            OpenDet : false,
            TaskEdit : false,
            TaskNew : false,
            options: {
                format: 'YYYY-MM-DD HH:mm:ss',
                useCurrent: false,
                showClear: true,
                showClose: true,
                daysOfWeekDisabled: [0],
                minDate: moment().subtract(120, 'minutes'),
                maxDate: moment().add(90, 'days'),
            },
            nom_receptor : "",
            nom_emisor : "",
            commentBody : "",
            CommentNew : 0,
            itsCommentUpd : 0,
            itsCommentNew : 0,
            comment_id : 0,
            user_id : 0,
            selectedUsers : [],
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
                ],
                visible : true
            }
        };
    },
    components: {
        vSelect,
        'editor': Editor,
        'viewer': Viewer
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
        }
    },
    methods: {
        listarActividades (page,buscar,criterio,estado){
            let me=this;
            var url= '/actividad?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='+ estado;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayActividad = respuesta.actividades.data;
                me.pagination= respuesta.pagination;
                me.user_id = respuesta.userid;

            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio,estado){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarActividades(page,buscar,criterio,estado);
        },
        registrarActividad() {
            if (this.validarActividad()) {
                return;
            }
            let me = this;

            var ArrUsuarios = [];
            for(let i = 0; i < this.selectedUsers.length; i++){
                ArrUsuarios.push(this.selectedUsers[i]['id']);
            }
            //console.log(ArrUsuarios);

            axios.post("/actividad/registrar", {
                title: this.title,
                content: this.content,
                end : this.end,
                usuarios : ArrUsuarios
            })
            .then(function(response) {
                me.cerrarDetalle();
                swal.fire(
                'Atención!',
                'La actividad ha sido registrada con éxito.',
                'success')
                me.listarActividades(1,'','nombre','0');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarActividad() {
            if (this.validarActividad()) {
                return;
            }
            let me = this;

            var ArrUsuarios = [];

            for(let i = 0; i < this.selectedUsers.length; i++){
                ArrUsuarios.push(this.selectedUsers[i]['id']);
            }

            axios.put("/actividad/actualizar", {
                id : this.activity_id,
                start : this.start,
                end : this.end,
                title : this.title,
                content : this.content,
                status : this.status,
                idemisor : this.idemisor,
                usuarios : ArrUsuarios
            })
            .then(function(response) {
                me.cerrarDetalle();
                swal.fire(
                'Atención!',
                'La actividad ha sido actualizada con éxito.',
                'success')
                me.listarActividades(1,'','nombre','0');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        desactivarActividad(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de cancelar esta actividad?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/actividad/desactivar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarActividades(1,'','nombre','0');
                        swalWithBootstrapButtons.fire(
                            "Desactivado!",
                            "La actividad ha sido cancelada con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        validarActividad() {
            this.errorActividad = 0;
            this.errorMostrarMsjActividad = [];

            if (!this.title) this.errorMostrarMsjActividad.push("El titulo de la actividad no puede estar vacío.");
            /* if (!this.selectedUsers.length) this.errorMostrarMsjActividad.push("Seleccione un usuario para asignar la actividad."); */
            if (!this.content) this.errorMostrarMsjActividad.push("El contenido de la actividad no puede estar vacio");
            if (!this.end) this.errorMostrarMsjActividad.push("Seleccione una fecha compromiso para la actividad.");

            if (this.errorMostrarMsjActividad.length) this.errorActividad = 1;

            return this.errorActividad;
        },
        newTask(){
            this.listado = 1;
            this.title = "";
            this.content = "";
            this.idreceptor = 0;
            this.end = "";
            this.OpenDet = true;
            this.TaskNew = true;
            this.TaskEdit = false;
            this.tituloDetalle = 'Crear Nueva Actividad';
            //this.selectReceptor();
        },
        editTask(data = []){
            this.listado = 1;
            this.activity_id = data['id'];
            this.start = data['start'];
            this.end = data['end'];
            this.title = data['title'];
            this.content = data['content'];
            this.status = data['status'];
            this.idreceptor = data['idreceptor'];
            this.idemisor = data['idemisor'];
            this.OpenDet = true;
            this.TaskNew = false;
            this.TaskEdit = true;
            this.tituloDetalle = 'Editar Actividad';
            this.getUsers(data['id']);
            //this.selectReceptor();
        },
        cerrarDetalle(){
            this.listado = 0;
            this.activity_id = 0;
            this.start = "";
            this.end = "";
            this.title = "";
            this.content = "";
            this.status = 0;
            this.idreceptor = 0;
            this.idemisor = 0;
            this.tituloDetalle = "";
            this.OpenDet = false;
            this.TaskNew = false;
            this.TaskEdit = false;
            this.arrayReceptores = [];
            this.selectedUsers = [];
        },
        selectReceptor(){
            let me=this;
            var url= '/user/selectUsuarioAct';

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayReceptores = respuesta.usuarios;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstado(id,estado){
            let me = this;

            var pageac = me.pagination.current_page;


            if(estado == true){
                me.estado = 1;
            }else{
                me.estado = 0;
            }

            var estadoAct = me.estado;

            axios.put('/actividad/cambiarEstado',{
                'id': id,
                'estado' : this.estado
            }).then(function (response) {
                if(estado == 1){
                    swal.fire(
                    'Completado!',
                    'La actividad ha sido registrado como completada.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'La actividad ha sido registrado como incompleta.',
                    'warning')
                }
                me.listarActividades(pageac,'','nombre',estadoAct);
            }).catch(function (error) {
                console.log(error);
            });
        },
        verActividad(data = []){
            this.listado = 2;
            this.activity_id = data['id'];
            this.start = data['start'];
            this.end = data['end'];
            this.title = data['title'];
            this.content = data['content'];
            this.status = data['status'];
            this.nom_emisor = data['emisor'];
            this.OpenDet = true;
            this.getUsers(data['id']);
            this.getComments(data['id']);
        },
        getUsers(id){
            let me = this;
            var url= '/actividad/getUsers?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.selectedUsers = respuesta.usuarios;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cerrarVerAct(){
            this.listado = 0;
            this.start = "";
            this.end = "";
            this.title = "";
            this.content = "";
            this.status = 0;
            this.idreceptor = 0;
            this.idemisor = 0;
            this.tituloDetalle = "";
            this.nom_receptor = "";
            this.nom_emisor = "";
            this.CommentNew = 0;
            this.commentBody = "";
            this.activity_id = 0;
            this.arrayComentarios = [];
            this.comment_id = 0;
            this.itsCommentUpd = 0;
            this.itsCommentNew = 0;
            this.OpenDet = false;
        },
        convertDate(date){
            moment.locale('es');
            let me=this;
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            return datec;
        },
        newComment(data = []){
            this.CommentNew = 1;
            this.commentBody = "";
            this.itsCommentUpd = 0;
            this.itsCommentNew = 1;
        },
        cancelComment(){
            this.CommentNew = 0;
            this.commentBody = "";
            this.comment_id = 0;
            this.itsCommentUpd = 0;
            this.itsCommentNew = 0;
        },
        saveComment(id){
            let me = this;

            var actid = id;

            axios.post('/actividad/crearComment',{
                'id' : id,
                'body' : this.commentBody
            }).then(function(response) {
                me.cancelComment();
                me.getComments(actid);
                swal.fire(
                'Completado!',
                'El comentario ha sido registrado con éxito.',
                'success')
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        getComments(id){
            let me = this;
            var url= '/actividad/getComments?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayComentarios = respuesta.comentarios;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        editComment(data = []){
            this.comment_id = data['id'];
            this.commentBody = data['body'];
            this.CommentNew = 1;
            this.itsCommentUpd = 1;
            this.itsCommentNew = 0;
        },
        updateComment(id){

            let me = this;
            var actid = id;

            axios.put("/actividad/editComment", {
                id : this.comment_id,
                body : this.commentBody
            })
            .then(function(response) {
                me.cancelComment();
                me.getComments(actid);
                swal.fire(
                'Completado!',
                'El comentario ha sido actualizado con éxito.',
                'success')
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        deleteComentario(id,actividad){
            let me = this;
            var actid = actividad;
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro eliminar este comentario?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/actividad/deleteComment', {
                        'id' : id
                    }).then(function(response) {
                         me.getComments(actid);
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El comentario ha sido eliminada con éxito.",
                            "success"
                        );
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        }
    },
    mounted() {
        this.listarActividades(1,this.buscar, this.criterio, this.estado);
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
    .showNewComment {
      display:block !important;
    }
    ul#involvedUsers li{
        display:inline !important;
    }
</style>
