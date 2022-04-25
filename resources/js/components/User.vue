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
          <i class="fa fa-align-justify"></i> Usuarios
          <button type="button" @click="abrirModal('persona','registrar')" class="btn btn-secondary">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>
        <div class="card-body">
            <div class="form-inline">
                <div class="form-group mb-2 col-12">
                    <div class="input-group">
                        <select class="form-control mb-1" v-model="criterio">
                            <option value="nombre">Nombre</option>
                            <option value="rfc">RFC</option>
                            <option value="email">Correo electrónico</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="text" v-model="buscar" @keyup.enter="listarPersona(1,buscar,criterio)" class="form-control mb-1" placeholder="Texto a buscar">
                        <button type="submit" @click="listarPersona(1,buscar,criterio)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>
            </div>
            <div class="table-responsive col-md-12">
                <table class="table table-bordered table-striped table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Opciones</th>
                            <th>Nombre</th>
                            <!-- <th>Ciudad</th> -->
                            <!-- <th>Domicilio</th> -->
                            <th>Teléfono</th>
                            <th>Correo electrónico</th>
                           <!--  <th>RFC</th> -->
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Area</th>
                            <th>Auto. Ingresos</th>
                            <th>Edicion</th>
                            <th>Ultima Conexion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="persona in arrayPersona" :key="persona.id">
                            <td>
                                <button type="button" @click="abrirModal('persona','actualizar',persona)" class="btn btn-warning btn-sm">
                                    <i class="icon-pencil"></i>
                                </button>&nbsp;
                                <template v-if="persona.condicion">
                                    <button type="button" class="btn btn-danger btn-sm" @click="desactivarUsuario(persona.id)">
                                        <i class="icon-trash"></i>
                                    </button>&nbsp;
                                </template>
                                <template v-else>
                                    <button type="button" class="btn btn-info btn-sm" @click="activarUsuario(persona.id)">
                                        <i class="icon-check"></i>
                                    </button>&nbsp;
                                </template>
                                <button type="button" class="btn btn-secondary btn-sm" @click="cambiarPassword(persona.id)">
                                    <i class="icon-lock"></i>
                                </button>&nbsp;
                            </td>
                            <td v-text="persona.nombre"></td>
                            <td v-text="persona.telefono"></td>
                            <td v-text="persona.email"></td>
                            <td v-text="persona.usuario"></td>
                            <td v-text="persona.rol"></td>
                            <td v-if="persona.area == 'GDL'">
                                Guadalajara
                            </td>
                            <td v-else-if="persona.area == 'SLP'">
                                San Luis
                            </td>
                            <td v-else-if="persona.area == 'AGS'">
                                Aguascalientes
                            </td>
                            <td v-else-if="persona.area == 'PBA'">
                                Puebla
                            </td>
                            <!-- <td v-text="persona.autoing"></td> -->
                            <td class="text-center">
                                <input type="checkbox" :id="'chkEn'+persona.id" v-model="persona.autoing"
                                    @change="cambiarEstadoAutoIngreso(persona.id,persona.autoing,persona.nombre)" :disabled="persona.idrol === 1">
                                    <input type="checkbox" :id="'chkEn'+persona.id" v-model="persona.autoing"
                                    @change="cambiarEstadoAutoFormato(persona.id,persona.autoing,persona.nombre)" :disabled="persona.idrol === 1">
                                <template v-if="persona.autoing">
                                        <label :for="'chkEn'+persona.id">Ingresos Habilidatos</label>
                                </template>
                                <template v-else>
                                    <label :for="'chkEn'+persona.id">Ingresos Deshabilidatos</label>
                                </template>
                            </td>
                            <td class="text-center">
                                   <input type="checkbox" :id="'chkEn'+persona.id" v-model="persona.usedit"
                                    @change="cambiarEstadoEdicion(persona.id,persona.usedit,persona.nombre)" :disabled="persona.idrol === 1">
                                    <template v-if="persona.usedit">
                                        <label :for="'chkEn'+persona.id">Edicion Habilitada</label>
                                    </template>
                                    <template v-else>
                                        <label :for="'chkEn'+persona.id">Edicion Deshabilitada</label>
                                    </template>
                            </td>
                            <td>{{formatDate(persona.last_act)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav>
                <ul class="pagination">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio)">Ant</a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio)" v-text="page"></a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio)">Sig</a>
                    </li>
                </ul>
            </nav>
        </div>
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                            <div class="col-md-9">
                            <input type="text" v-model="nombre" class="form-control" placeholder="Nombre del usuarío"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Ciudad</label>
                            <div class="col-md-9">
                                <input type="text" v-model="ciudad" class="form-control" placeholder="Ciudad donde habita la persona"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Domicilio</label>
                            <div class="col-md-9">
                                <input type="text" v-model="domicilio" class="form-control" placeholder="Domicilio del persona"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Teléfono</label>
                            <div class="col-md-9">
                                <input type="text" v-model="telefono" class="form-control" placeholder="Teléfono del usuarío"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Correo electrónico</label>
                            <div class="col-md-9">
                                <input type="email" v-model="email" class="form-control" placeholder="Correo electrónico del usuarío"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">RFC</label>
                            <div class="col-md-9">
                                <input type="text" v-model="rfc" maxlength="13" class="form-control" placeholder="RFC del usuarío"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Rol (*)</label>
                            <div class="col-md-9">
                            <select class="form-control" v-model="idrol">
                                <option value="0">Seleccione un rol</option>
                                <option v-for="rol in arrayRol" :key="rol.id" :value="rol.id" v-text="rol.nombre">

                                </option>
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Usuario (*)</label>
                            <div class="col-md-9">
                                <input type="text" v-model="usuario" class="form-control" placeholder="Nombre de usuarío"/>
                            </div>
                        </div>
                        <div class="form-group row" v-if="showPass">
                            <label class="col-md-3 form-control-label" for="text-input">Contraseña (*)</label>
                            <div class="col-md-9">
                                <input type="password" v-model="password" class="form-control" placeholder="Contraseña de acceso al sistema"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="text-input" class="col-md-3 form-control-label">Area</label>
                            <div class="col-md-9">
                                <select class="form-control" v-model="area">
                                    <option value="" disabled>Seleccione un area para el usuario</option>
                                    <option value="GDL">Guadalajara</option>
                                    <option value="SLP">San Luis</option>
                                    <option value="AGS">Aguascalientes</option>
                                    <option value="PBA">Puebla</option>
                                </select>
                            </div>
                        </div>

                    <div v-show="errorPersona" class="form-group row div-error">
                        <div class="text-center text-error">
                        <div v-for="error in errorMostrarMsjPersona" :key="error" v-text="error"></div>
                        </div>
                    </div>
                    </form>
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
    <!--Fin del modal-->
  </main>
</template>

<script>
import moment from 'moment';
export default {
    data() {
        return {
            persona_id: 0,
            nombre: "",
            tipo_documento: "",
            num_documento: "",
            ciudad: "",
            domicilio: "",
            telefono: "",
            email: "",
            rfc: "",
            usuario: "",
            password: "",
            idrol: 0,
            area : "",
            autoing : 0,
            usedit: 0,
            arrayPersona: [],
            arrayRol: [],
            modal: 0,
            tituloModal: "",
            tipoAccion: 0,
            errorPersona: 0,
            errorMostrarMsjPersona: [],
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
            showPass : false
        };
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

        listarPersona (page,buscar,criterio){
            let me=this;
            var url= '/user?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayPersona = respuesta.personas.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        selectRol(){

            let me=this;
            var url= '/rol/selectRol';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayRol = respuesta.roles;
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
                me.listarPersona(page,buscar,criterio);
        },
        registrarPersona() {
            if (this.validarPersona()) {
                return;
            }

            let me = this;
            let autoing = 0;
            let usedit = 0;

            if (this.idrol === 1) {
                autoing = 1;
            }
            if (this.idrol === 1) {
                usedit= 1;
            }

            axios.post("/user/registrar", {
                'nombre'         : this.nombre,
                'tipo_documento' : this.tipo_documento,
                'num_documento'  : this.num_documento,
                'ciudad'         : this.ciudad,
                'domicilio'      : this.domicilio,
                'telefono'       : this.telefono,
                'email'          : this.email,
                'rfc'            : this.rfc,
                'usuario'        : this.usuario,
                'password'       : this.password,
                'idrol'          : this.idrol,
                'area'           : this.area,
                'autoing'        : autoing,
                'usedit'         : usedit,
            })
            .then(function(response) {
                me.cerrarModal();
                me.listarPersona(1,'','nombre');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarPersona() {
            if (this.validarPersona()) {
                return;
            }
            let me = this;
            let autoing = 0;
            if (this.idrol === 1) {
                autoing = 1;
            }
            let usedit = 0;
            if (this.idrol === 1) {
                usedit= 1;
            }
            axios.put("/user/actualizar", {
                'nombre'         : this.nombre,
                'tipo_documento' : this.tipo_documento,
                'num_documento'  : this.num_documento,
                'ciudad'         : this.ciudad,
                'domicilio'      : this.domicilio,
                'telefono'       : this.telefono,
                'email'          : this.email,
                'rfc'            : this.rfc,
                'usuario'        : this.usuario,
                'idrol'          : this.idrol,
                'id'             : this.persona_id,
                'area'           : this.area,
                'autoing'        : autoing,
                'usedit'         : usedit,
            })
            .then(function(response) {
                me.cerrarModal();
                me.listarPersona(1,'','nombre');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        desactivarUsuario(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de desactivar este usuarío?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/user/desactivar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarPersona(1,'','nombre');
                        swalWithBootstrapButtons.fire(
                            "Desactivado!",
                            "El usuarío ha sido desactivado con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        activarUsuario(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de activar este usuarío?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/user/activar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarPersona(1,'','nombre');
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "El usuarío ha sido activado con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        validarPersona() {
            this.errorPersona = 0;
            this.errorMostrarMsjPersona = [];

            if (!this.nombre) this.errorMostrarMsjPersona.push("El nombre de la persona no puede estar vacío.");
            if (!this.usuario) this.errorMostrarMsjPersona.push("El nombre de usuario no puede estar vacío.");
            if (!this.password) this.errorMostrarMsjPersona.push("La contraseña del usuario no puede estar vacía.");
            if (this.idrol == 0 ) this.errorMostrarMsjPersona.push("Debe seleccionar un rol de usuario");
            if (this.errorMostrarMsjPersona.length) this.errorPersona = 1;

            return this.errorPersona;
        },
        cerrarModal() {
            this.modal = 0;
            this.tituloModal = "";
            this.nombre = "";
            this.tipo_documento = "";
            this.num_documento = "";
            this.ciudad = "";
            this.domicilio = "";
            this.telefono = "";
            this.email = "";
            this.rfc = "";
            this.usuario ="";
            this.password ="";
            this.idrol =0;
            this.errorPersona = 0;
            this.area = 0;
            this.showPass = false;
        },
        abrirModal(modelo, accion, data = []) {
            this.selectRol();
            switch (modelo) {
                case "persona": {
                    switch (accion) {
                        case "registrar": {
                            this.modal = 1;
                            this.tituloModal = "Registrar Usuario";
                            this.nombre = "";
                            this.tipo_documento = "";
                            this.num_documento = "";
                            this.ciudad = "";
                            this.domicilio = "";
                            this.telefono = "";
                            this.email = "";
                            this.rfc = "";
                            this.usuario ="";
                            this.password ="";
                            this.idrol =0;
                            this.tipoAccion = 1;
                            this.area = "";
                            this.showPass = true;
                            break;
                        }
                        case "actualizar": {
                            this.modal = 1;
                            this.tituloModal = "Actualizar Usuario";
                            this.tipoAccion = 2;
                            this.persona_id = data["id"];
                            this.nombre = data["nombre"];
                            this.tipo_documento = data["tipo_documento"];
                            this.num_documento = data["num_documento"];
                            this.ciudad = data["ciudad"];
                            this.domicilio = data["domicilio"];
                            this.telefono = data["telefono"];"";
                            this.email = data["email"];
                            this.rfc = data["rfc"];
                            this.usuario = data["usuario"];
                            this.password = data["password"];
                            this.idrol = data["idrol"];
                            this.area = data["area"];
                            this.showPass = false;
                            break;
                        }
                    }
                }
            }
        },
        cambiarEstadoAutoIngreso(id,estado,user){
            let me = this;
            var factip = me.tipo_fact;
            var pageac = me.pagination.current_page;

            if(estado == true){
                me.autoing = 1;
            }else{
                me.autoing = 0;
            }

            axios.put('/user/autoIngreso',{
                'id'          : id,
                'autoingreso' : this.autoing
            }).then(function (response) {
                if(estado == 1){
                    swal.fire(
                    'Completado!',
                    'Se habilitaron los ingresos al usuario '+ user + ' con éxito.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'Se deshabilitaron los ingresos al usuario '+ user,
                    'warning')
                }
                me.listarPersona(pageac,this.buscar,this.criterio);
            }).catch(function (error) {
                console.log(error);
            });

        },
        cambiarEstadoAutoFormato(id,estado,user){
            let me = this;
            var factip = me.tipo_fact;
            var pageac = me.pagination.current_page;

            if(estado == true){
                me.autoing = 1;
            }else{
                me.autoing = 0;
            }

            axios.put('/user/autoFormato',{
                'id'          : id,
                'autoformato' : this.autofor
            }).then(function (response) {
                if(estado == 1){
                    swal.fire(
                    'Completado!',
                    'Se habilitaron los ingresos al usuario '+ user + ' con éxito.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'Se deshabilitaron los ingresos al usuario '+ user,
                    'warning')
                }
                me.listarPersona(pageac,this.buscar,this.criterio);
            }).catch(function (error) {
                console.log(error);
            });

        },
        cambiarEstadoEdicion(id,estado,user){
            let me = this;
            var factip = me.tipo_fact;
            var pageac = me.pagination.current_page;

            if(estado == true){
                me.usedit =1;
            }else{
                me.usedit = 0;
            }

            axios.put('/user/editarArticulos',{
                'id'          : id,
                'usedit' : this.usedit
            }).then(function (response) {
                if(estado == 1){
                    swal.fire(
                    'Completado!',
                    'Se habilitaron la Edicion para'+ user + ' con éxito.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'Se deshabilito la edicion para'+ user,
                    'warning')
                }
                me.listarPersona(pageac,this.buscar,this.criterio);
            }).catch(function (error) {
                console.log(error);
            });

        },
        formatDate(date){
            if(date != null){
                moment.locale('es');
                let me=this;
                var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
                return datec;
            }else{
                return 'Sin registro';
            }
        },
        cambiarPassword(id){
            Swal.fire({
            title: 'Cambiar contraseña!',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Cambiar',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                if(!password || password.length < 5){
                    Swal.showValidationMessage(
                    `Ingrese la contraseña minimo 5 caracteres`,
                    );
                }else{
                    axios.put('/user/cambiarPassword',{
                        'id': id,
                        'password' : password
                    })
                    .then(response => {
                        console.log(response);
                    })
                    .catch(error => {
                    })
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
            if (result.value) {
                Swal.fire({
                title: `Contraseña cambiada`,
                type : 'success'
                })
            }
            });
        }
    },
    mounted() {
        this.listarPersona(1,this.buscar, this.criterio);
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
</style>
