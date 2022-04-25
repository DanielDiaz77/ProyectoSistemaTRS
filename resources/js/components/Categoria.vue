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
          <i class="fa fa-align-justify"></i> Categorías
          <button type="button" @click="abrirModal('categoria','registrar')" class="btn btn-secondary" v-if="listado==0">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>
        <!-- Listado -->
        <template v-if="listado==0">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-12">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio">
                                <option value="nombre">Nombre</option>
                                <option value="descripcion">Descripción</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <input type="text" v-model="buscar" @keyup.enter="listarCategoria(1,buscar,criterio)" class="form-control mb-1" placeholder="Texto a buscar">
                            <button type="submit" @click="listarCategoria(1,buscar,criterio)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive col-md-12">
                    <table class="table table-bordered table-striped table-sm table-hover">
                        <thead>
                        <tr>
                            <th>Opciones</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody v-if="arrayCategoria.length">
                            <tr v-for="categoria in arrayCategoria" :key="categoria.id">
                                <td>
                                    <div class="form-inline">
                                        <button type="button" @click="abrirModal('categoria','actualizar',categoria)" class="btn btn-warning btn-sm">
                                            <i class="icon-pencil"></i>
                                        </button> &nbsp;
                                        <template v-if="categoria.condicion">
                                            <button type="button" class="btn btn-danger btn-sm" @click="desactivarCategoria(categoria.id)">
                                                <i class="icon-trash"></i>
                                            </button>&nbsp;
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-info btn-sm" @click="activarCategoria(categoria.id)">
                                                <i class="icon-check"></i>
                                            </button>&nbsp;
                                        </template>
                                        <!-- getListSku -->
                                        <button type="button" @click="getListSku(categoria.id,categoria.nombre)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;
                                    </div>
                                </td>
                                <td v-text="categoria.nombre"></td>
                                <td v-text="categoria.descripcion"></td>
                                <td>
                                <div v-if="categoria.condicion">
                                    <span class="badge badge-success">Activo</span>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Desactivado</span>
                                </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <strong>NO hay materiales registrados con ese criterio...</strong>
                                </td>
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
        </template>
        <!-- Listado SKUS-->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-12">
                        <div class="input-group">
                            <input type="text" v-model="buscarsku" @keyup.enter="listarSkuCategoria(1,categoria_id,buscarsku)" class="form-control mb-1" placeholder="Buscar">
                            <button type="submit" @click="listarSkuCategoria(1,categoria_id,buscarsku)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive col-md-12">
                    <table class="table table-bordered table-striped table-sm table-hover">
                        <thead>
                        <tr>
                            <th>Opciones</th>
                            <th>Material</th>
                            <th>Código de material</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody v-if="arraySku.length">
                            <tr v-for="codigoMat in arraySku" :key="codigoMat.id">
                                <td>
                                    <div class="form-inline">
                                        <button type="button" class="btn btn-success btn-sm" @click="getListArticulos(codigoMat.sku)">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;
                                    </div>
                                </td>
                                <td v-text="nom_category"></td>
                                <td v-text="codigoMat.sku"></td>
                                <td v-text="codigoMat.total"></td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <strong>NO hay articulos registrados con este material...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination_sku.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPaginaSku(pagination_sku.current_page - 1,categoria_id,buscarsku)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumberSKU" :key="page" :class="[page == isActivedSKU ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPaginaSku(page,categoria_id,buscarsku)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination_sku.current_page < pagination_sku.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPaginaSku(pagination_sku.current_page + 1,categoria_id,buscarsku)">Sig</a>
                        </li>
                    </ul>
                </nav>
                <div class="row">
                    <div class="col">
                        <div class="float-right">
                            <button type="button" class="btn btn-secondary" @click="cerrarDetalleSku()">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="listado==2">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-sm-10">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio_art">
                                <option value="descripcion">Descripción</option>
                                <option value="codigo">No° de placa</option>
                            </select>
                        </div>
                        <div class="input-group">   <!-- sku,page,buscar,criterio,bodega,acabado,estado -->
                            <input type="text" v-model="buscar_art" @keyup.enter="listarArticulosCategoria(sku,1,buscar_art,criterio_art,bodega_art,acabado_art,estado_art)" class="form-control mb-1" placeholder="Texto a buscar">
                            <input type="text" v-model="acabado_art" @keyup.enter="listarArticulosCategoria(sku,1,buscar_art,criterio_art,bodega_art,acabado_art,estado_art)" class="form-control mb-1" placeholder="Terminado">
                        </div>
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="bodega_art" @change="listarArticulosCategoria(sku,1,buscar_art,criterio_art,bodega_art,acabado_art,estado_art)">

                                    <option value="">Todas</option>
                                    <option value="Del Musico">Del Músico</option>
                                    <option value="Escultores">Escultores</option>
                                    <option value="Sastres">Sastres</option>
                                    <option value="Mecanicos">Mecánicos</option>
                                    <option value="Tractorista">Tractorista</option>
                                    <option value="San Luis">San Luis</option>
                                    <option value="Aguascalientes">Aguascalientes</option>
                            </select>
                            <button type="submit" @click="listarArticulosCategoria(sku,1,buscar_art,criterio_art,bodega_art,acabado_art,estado_art)" class="btn btn-sm btn-primary mb-1"><i class="fa fa-search"></i>Buscar</button>
                        </div>
                        <div class="input-group input-group-sm ml-xl-5">
                            <select class="form-control" id="tipofact" name="tipofact" v-model="estado_art" @change="listarArticulosCategoria(sku,1,buscar_art,criterio_art,bodega_art,acabado_art,estado_art)">
                                <option value="1">Disponible</option>
                                <option value="2">Vendido</option>
                                <option value="3">Cortado</option>
                            </select>
                            <button class="btn btn-sm btn-info" type="button"><i class="fa fa-search" aria-hidden="true"></i>&nbsp; Filtros</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive col-md-12">
                    <table class="table table-bordered table-striped table-sm text-center table-hover">
                        <thead>
                        <tr class="text-center">
                            <th>No. Placa</th>
                            <th>Código de Material</th>
                            <th>Material</th>
                            <th>Descripción</th>
                            <th>Largo</th>
                            <th>Alto</th>
                            <th>Metros<sup>2</sup></th>
                            <th>Espesor</th>
                            <th>Terminado</th>
                            <th>Bodega de descarga</th>
                            <th>Estado</th>
                            <th>Stock</th>
                            <th>Comprometido</th>
                        </tr>
                        </thead>
                        <tbody v-if="arrayArticulo.length">
                            <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                <td v-text="articulo.codigo"></td>
                                <td v-text="articulo.sku"></td>
                                <td v-text="articulo.nombre_categoria"></td>
                                <td v-text="articulo.descripcion"></td>
                                <td v-text="articulo.largo"></td>
                                <td v-text="articulo.alto"></td>
                                <td v-text="articulo.metros_cuadrados"></td>
                                <td v-text="articulo.espesor"></td>
                                <td v-text="articulo.terminado"></td>
                                <td v-text="articulo.ubicacion"></td>
                                <td>
                                    <div v-if="articulo.condicion == 1">
                                        <span class="badge badge-success">Activo</span>
                                    </div>
                                    <div v-else-if="articulo.condicion == 3">
                                        <span class="badge badge-warning">Cortado</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Desactivado</span>
                                    </div>
                                </td>
                                <td v-text="articulo.stock"></td>
                                <td>
                                    <div v-if="articulo.comprometido">
                                        <span class="badge badge-success">Si</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">No</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="14" class="text-center">
                                    <strong>NO hay artículos con ese criterio...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination"> <!-- sku,page,buscar,criterio,bodega,acabado,estado -->
                        <li class="page-item" v-if="pagination_art.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(sku,pagination_art.current_page - 1,buscar_art, criterio_art,bodega_art,acabado_art,estado_art)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumberART" :key="page" :class="[page == isActivedART ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(sku,page,buscar_art, criterio_art,bodega_art,acabado_art,estado_art)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination_art.current_page < pagination_art.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(sku,pagination_art.current_page + 1,buscar_art, criterio_art,bodega_art,acabado_art,estado_art)">Sig</a>
                        </li>
                    </ul>
                </nav>
                <div class="row">
                    <div class="col">
                        <div class="float-right">
                            <button type="button" class="btn btn-secondary" @click="cerrarListaArt()">Cerrar</button>
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
            <div class="modal-content content-category">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group row">
                        <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                            <div class="col-md-9">
                                <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de categoría"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Descripción</label>
                            <div class="col-md-9">
                                <input type="email" v-model="descripcion" class="form-control" placeholder="Ingrese descripción"/>
                            </div>
                        </div>
                        <div v-show="errorCategoria" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjCategoria" :key="error" v-text="error"></div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer d-block d-sm-block d-md-none">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarCategoria()">Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarCategoria()">Actualizar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarCategoria()">Guardar</button>
                    <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarCategoria()">Actualizar</button>
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
export default {
    data() {
        return {
            categoria_id: 0,
            nombre: "",
            descripcion: "",
            arrayCategoria: [],
            modal: 0,
            tituloModal: "",
            tipoAccion: 0,
            errorCategoria: 0,
            errorMostrarMsjCategoria: [],
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
            sku : ""
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
        listarCategoria (page,buscar,criterio){
            let me=this;
            var url= '/categoria?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayCategoria = respuesta.categorias.data;
                me.pagination= respuesta.pagination;
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
                me.listarCategoria(page,buscar,criterio);
        },
        registrarCategoria() {
            if (this.validarCategoria()) {
                return;
            }

            let me = this;

            axios.post("/categoria/registrar", {
                nombre: this.nombre,
                descripcion: this.descripcion
            })
            .then(function(response) {
                me.cerrarModal();
                me.listarCategoria(1,'','nombre');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarCategoria() {
            if (this.validarCategoria()) {
                return;
            }
            let me = this;
            axios.put("/categoria/actualizar", {
                nombre: this.nombre,
                descripcion: this.descripcion,
                id: this.categoria_id
            })
            .then(function(response) {
                me.cerrarModal();
                me.listarCategoria(1,'','nombre');
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
                        me.listarCategoria(1,'','nombre');
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
                        me.listarCategoria(1,'','nombre');
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
        validarCategoria() {
            this.errorCategoria = 0;
            this.errorMostrarMsjCategoria = [];

            if (!this.nombre)
                this.errorMostrarMsjCategoria.push(
                "El nombre de la categoría no puede estar vacío."
                );

            if (this.errorMostrarMsjCategoria.length) this.errorCategoria = 1;

            return this.errorCategoria;
        },
        cerrarModal() {
            this.modal = 0;
            this.tituloModal = "";
            this.nombre = "";
            this.descripcion = "";
        },
        abrirModal(modelo, accion, data = []) {
            switch (modelo) {
                case "categoria": {
                    switch (accion) {
                        case "registrar": {
                            this.modal = 1;
                            this.tituloModal = "Registrar Categoría";
                            this.nombre = "";
                            this.descripcion = "";
                            this.tipoAccion = 1;
                            break;
                        }
                        case "actualizar": {
                            this.modal = 1;
                            this.tituloModal = "Actualizar categoría";
                            this.tipoAccion = 2;
                            this.categoria_id = data["id"];
                            this.nombre = data["nombre"];
                            this.descripcion = data["descripcion"];
                            break;
                        }
                    }
                }
            }
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
            this.categoria_id = id;
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
            this.categoria_id = 0;
            this.nom_category = "";
            this.listarCategoria(1,'','nombre');
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
        this.listarCategoria(1,this.buscar, this.criterio);
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
