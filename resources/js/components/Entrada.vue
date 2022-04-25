<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a> </li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i>Ingreso de Herramientas
                    <button type="button" class="btn btn-secondary" @click="nuevoIngreso()" v-if="itsNewEn">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                    <div v-else class="float-right">
                        <p class="font-weight-bold">{{ gettimeNow }}</p>
                    </div>
                </div>
                <!-- Listado -->
                <template v-if="listado==1">
                    <div class="card-body">
                        <div class="form-inline">
                            <div class="form-group mb-2 col-12">
                                <div class="input-group">
                                    <select class="form-control mb-1" v-model="criterio">
                                        <option value="num_comprobante">N° Comprobante</option>
                                        <option value="fecha_hora">Fecha</option>
                                        <option value="estado">Estado</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <input type="text" v-model="buscar" @keyup.enter="listarEntrada(1,buscar,criterio,estadoIn)" class="form-control mb-1" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarEntrada(1,buscar,criterio,estadoIn)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                                <div class="input-group ml-2">
                                    <button type="submit" @click="listarEntrada(1,buscar,criterio,estadoIn)" class="btn btn-outline-info mb-1"><i class="fa fa-question"></i> Estado</button>
                                </div>
                                <select class="form-control mb-1" v-model="estadoIn" @change="listarEntrada(1,buscar,criterio,estadoIn)">
                                    <option value="">Todo</option>
                                    <option value="Registrado">Registrado</option>
                                    <option value="Anulado">Anulado</option>
                                </select>
                            </div>
                        </div>
                        <!-- Table For SM > Devices -->
                        <div class="table-responsive col-md-12 d-none d-sm-none d-md-block">
                            <table class="table table-bordered table-striped table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Usuario</th>
                                        <th>No° Comprobante</th>
                                        <th>Tipo Comprobante</th>
                                        <th>Fecha</th>
                                        <th>Proveedor</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayEntrada.length">
                                    <tr v-for="entrada in arrayEntrada" :key="entrada.id">
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm" @click="verEntrada(entrada.id)">
                                                <i class="icon-eye"></i>
                                            </button>&nbsp;
                                            <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfEntrada(entrada.id)">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </button>&nbsp;
                                            <template v-if="entrada.estado == 'Registrado'">
                                                <button type="button" class="btn btn-danger btn-sm" @click="cancelarEntrada(entrada.id)">
                                                    <i class="icon-trash"></i>
                                                </button>&nbsp;
                                            </template>
                                        </td>
                                        <td v-text="entrada.usuario"></td>
                                        <td v-text="entrada.num_comprobante"></td>
                                        <td v-text="entrada.tipo_comprobante"></td>
                                        <td>{{ convertDateEntrada(entrada.fecha_hora) }}</td>
                                        <td v-text="entrada.nombre"></td>
                                        <td>
                                            <div v-if="entrada.estado == 'Registrado'">
                                                <span class="badge badge-success">Activo</span>
                                            </div>
                                            <div v-else>
                                                <span class="badge badge-danger">Cancelado</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <strong>NO hay entradas registradas o con ese criterio...</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Table For xs < Devices -->
                        <div class="table-responsive col-md-12 d-block d-sm-block d-md-none">
                            <table class="table table-striped table-responsive-xl">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="text-center">Entradas</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayEntrada.length">
                                    <tr v-for="entrada in arrayEntrada" :key="entrada.id" :class="{'bg-danger text-white' : entrada.estado == 'Anulado'}">
                                        <td>
                                            <strong>No° Comprobante: </strong> {{entrada.num_comprobante}} <br>
                                            <strong>Proveedor: </strong> {{entrada.nombre}} <br>
                                        </td>
                                        <td> <strong>Fecha: {{ convertDateEntrada(entrada.fecha_hora) }}</strong>

                                        <td>
                                            <div class="btn btn-group mr-3 mb-2">
                                                <button class="btn btn-outline-light text-dark dropdown-toggle" id="dp-categorias" data-toggle="dropdown">&bull;&bull;&bull;</button>
                                                <div class="dropdown-menu" aria-labelledby="dp-categorias">
                                                    <a href="#"  @click="verEntrada(entrada.id)" class="dropdown-item"><i class="icon-eye"></i>Detalles</a>
                                                    <template v-if=" entrada.estado == 'Registrado'">
                                                        <!-- <a href="#" @click="abrirModal('persona','actualizar',persona)" class="dropdown-item">Editar</a> -->
                                                        <a href="#"  @click="cancelarEntrada(entrada.id)" class="dropdown-item bg-danger"><i class="icon-trash"></i>Anular</a>
                                                    </template>
                                                </div>
                                            </div><br>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <strong>NO hay usuarios registrados o con ese criterio...</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoIn)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoIn)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoIn)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </template>
                <!-- Fin Listado -->

                <!-- Nuevo Ingreso -->
                <template v-else-if="listado==0">
                    <div class="card-body">
                        <!-- Proveedor Section -->
                        <div class="form-group row border">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for=""><strong>Proveedor (*)</strong></label>
                                        <v-select @search="selectProveedor" label="nombre" :options="arrayProveedor"
                                            placeholder="Buscar Proveedores..." @input="getDatosProveedor"></v-select>
                                </div>
                            </div>&nbsp;
                            <div class="col-md-2 text-center" v-if="nombre_proveedor">
                                <div class="form-group">
                                    <label for=""><strong>Proveedor</strong></label>
                                    <h6 for=""><strong v-text="nombre_proveedor"></strong></h6>
                                </div>
                            </div>&nbsp;
                           <div class="col-md-2 text-center" v-if="rfc_proveedor">
                                <div class="form-group">
                                    <label for=""><strong>RFC</strong></label>
                                    <h6 for=""><strong v-text="rfc_proveedor"></strong></h6>
                                </div>
                            </div>&nbsp;
                            <div class="col-md-2 text-center" v-if="contacto_prov">
                                <div class="form-group">
                                    <label for=""><strong>Contacto</strong></label>
                                    <h6 for=""><strong v-text="contacto_prov"></strong></h6>
                                </div>
                            </div>&nbsp;
                            <div class="col-md-2 text-center" v-if="tel_contacto">
                                <div class="form-group">
                                    <label for=""><strong>Telefono de contacto</strong></label>
                                    <h6 for=""><strong v-text="tel_contacto"></strong></h6>
                                </div>
                            </div>&nbsp;
                        </div>
                        <!-- END Proveedor Section -->
                        <div class="form-group row border">
                            <div class="col-md-2 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Tipo Comprobante (*)</strong></label>
                                    <select v-model="tipo_comprobante" class="form-control">
                                        <option value="">Seleccione</option>
                                        <option value="INGRESO">INGRESO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Número de entrada (*)</strong></label>
                                    <div class="d-flex justify-content-center">
                                        <div>
                                            <input type="number" readonly :value="getFechaCode" class="form-control col-md"/>
                                        </div>
                                        <div>
                                            <input type="text" class="form-control col-md" v-model="num_comprobante" placeholder="000xx">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mt-1">
                                <div class="form-group">
                                    <label for=""><strong>Articulo</strong> <span style="color:red;" v-show="articulo==''">(*Seleccione)</span> </label>
                                    <div class="form-inline">
                                        <input type="text" class="form-control" v-model="codigo_art" @keyup.enter="buscarArticulo()"  placeholder="Ingrese código del artículo" >
                                        <button @click="abrirModal()" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                                        <input type="text" readonly class="form-control" v-model="articulo" v-if="articulo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 text-center mt-1">
                                <div class="form-group">
                                    <label for=""><strong>Cantidad</strong> <span style="color:red;" v-show="cantidad==0">(*Ingrese la cantidad)</span></label>
                                    <input type="number" min="0" value="0"  class="form-control" v-model="cantidad">
                                </div>
                            </div>
                            <div class="col-sm-2 text-center mt-1">
                                <div class="form-group">
                                    <button @click="agregarDetalle()" class="btn btn-block btn-success mt-4"><i class="icon-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div v-show="errorEntrada" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjEntrada" :key="error" v-text="error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group row border">

                        </div> -->
                        <div class="form-group row border">
                            <div class="table-responsive col-md-12 d-none d-sm-none d-md-block">
                                <table class="table table-bordered table-striped table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>Código</th>
                                            <th>SKU</th>
                                            <th>Descripción</th>
                                            <th>Ubicación</th>
                                           <!--  <th>Precio</th> -->
                                            <th>Stock</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                            <td>
                                                <div class="form-inline">
                                                    <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                                    <i class="icon-close"></i>
                                                    </button>&nbsp;
                                                </div>
                                            </td>
                                            <td v-text="detalle.codigo"></td>
                                            <td v-text="detalle.sku"></td>
                                           <!--  <td>
                                                <input v-model="detalle.sku" type="text" class="form-control">
                                            </td> -->
                                            <td v-text="detalle.descripcion"></td>
                                            <td v-text="detalle.ubicacion"></td>
                                            <!-- <td>
                                                <input v-model="detalle.precio" min="0" type="number" value="3" class="form-control">
                                            </td> -->
                                            <td v-text="detalle.stock"></td>
                                            <td>
                                                <input v-model="detalle.cantidad" min="0" type="number" value="2" class="form-control">
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                <strong>NO hay artículos agregados...</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Table For xs < Devices -->
                            <div class="table-responsive col-md-12 d-block d-sm-block d-md-none">
                                <!-- <h1>Just XS Devices</h1> -->
                                <table class="table table-striped table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="text-center">Detalles de entrada</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id" :class="{'bg-danger text-white' : detalle.estado == 'Anulado'}">
                                            <td>
                                                <strong> Articulo: {{detalle.codigo}}</strong>
                                                <br>
                                                <strong> Ubicacion: {{detalle.ubicacion}}</strong><br>
                                                <strong> Stock: {{detalle.stock}}</strong>
                                            </td>
                                            <td>
                                                {{ detalle.descripcion}} <br>
                                                <strong> Cantidad:</strong>
                                                <input v-model="detalle.cantidad" min="0" type="number" value="2" class="form-control"></td>
                                            <td>
                                                <div class="btn btn-group mr-3 mb-2">
                                                <button class="btn btn-outline-light text-dark dropdown-toggle" id="dp-categorias" data-toggle="dropdown">&bull;&bull;&bull;</button>
                                                <div class="dropdown-menu" aria-labelledby="dp-categorias">
                                                    <a href="#"  @click="eliminarDetalle(index)" class="dropdown-item bg-danger">Quitar</a>
                                                </div>
                                            </div><br>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <strong>NO hay artículos registrados o con ese criterio...</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="cerrarNuevoIngreso()"  class="btn btn-secondary">Cerrar</button>
                                <button type="button" @click="registrarEntrada()" class="btn btn-primary">Registrar Ingreso</button>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- Fin Nuevo Ingreso -->

                <!-- Ver Ingreso -->
                <template v-else-if="listado==2">
                    <div class="card-body">
                        <div class="form-group row border">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for=""><strong>Proveedor</strong></label>
                                    <p v-text="proveedor"></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for=""><strong>Tipo Comprobante</strong></label>
                                    <p v-text="tipo_comprobante"></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for=""><strong>Número de Comprobante</strong></label>
                                    <p v-text="num_comprobante"></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for=""><strong>Registrado por:</strong></label>
                                    <p v-text="user"></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for=""><strong>Fecha:</strong></label>
                                    <p v-text="fecha_hora"></p>
                                </div>
                            </div>
                            <div class="col-md-2">

                                <div class="form-group">
                                    <label for=""><strong>Estado</strong></label>
                                    <div v-if="estado == 'Registrado'">
                                        <span class="badge badge-success">Presupuesto Validado</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Presupuesto Cancelado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row border">
                            <!-- Table For SM > Devices -->
                            <div class="table-responsive col-md-12 d-none d-sm-none d-md-block">
                                <table class="table table-bordered table-striped table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th width="10px">No°</th>
                                            <th>Código</th>
                                            <th>SKU</th>
                                             <th>Cantidad</th>
                                            <th>Descripción</th>
                                            <th>Ubicación</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                            <td width="10px" v-text="index + 1"></td>
                                            <td v-text="detalle.codigo"></td>
                                            <td v-text="detalle.sku"></td>
                                            <td v-text="detalle.cantidad"></td>
                                            <td v-text="detalle.descripcion"></td>
                                            <td v-text="detalle.ubicacion"></td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <strong>NO hay artículos en este detalle...</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Table For xs < Devices -->
                            <div class="table-responsive col-md-12 d-block d-sm-block d-md-none">
                                <!-- <h1>Just XS Devices</h1> -->
                                <table class="table table-striped table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="text-center">Detalles de entrada</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="detalle in arrayDetalle" :key="detalle.id" :class="{'bg-danger text-white' : detalle.estado == 'Anulado'}">
                                            <td>
                                                <template v-if="detalle.file">
                                                    <lightbox class="m-0" album="" :src="'images/tools/'+detalle.file">
                                                        <img class="img-responsive img-fluid imgcenter" width="100px" :src="'images/tools/'+detalle.file">
                                                    </lightbox>&nbsp;
                                                </template>
                                                <br>
                                                <strong> Ubicacion: {{detalle.ubicacion}}</strong>
                                            </td>
                                            <td>{{ detalle.sku }} <br> {{ detalle.descripcion}} <br> </td>
                                            <td>
                                                <strong> Cantidad: {{detalle.cantidad}}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <strong>NO hay artículos registrados o con ese criterio...</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="cerrarNuevoIngreso()"  class="btn btn-secondary">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- Fin Ver Ingreso -->

            </div>
        </div>
        <!--Inicio del modal listar articulos-->
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
                        <!-- Filtros Modal Articulos -->
                        <div class="form-group row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <select class="form-control" v-model="criterioA">
                                        <option value="sku">SKU</option>
                                        <option value="codigo">Código de material</option>
                                        <option value="descripcion">Descripción</option>
                                    </select>
                                    <input type="text" v-model="buscarA" @keyup.enter="listarArticulo(1,buscarA,criterioA,bodegaA)" class="form-control" placeholder="Texto a buscar">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <button type="submit" @click="listarArticulo(1,buscarA,criterioA,bodegaA)" class="btn btn-primary"><i class="fa fa-search"></i>Buscar</button>&nbsp;
                                    <template>
                                        <select class="form-control" v-model="bodegaA" @change="listarArticulo(1,buscarA,criterioA,bodegaA)">
                                            <option value="" disabled>Ubicacion</option>
                                            <option value="">Todas</option>
                                            <option value="Del Musico">Del Músico</option>
                                            <option value="Escultores">Escultores</option>
                                            <option value="Sastres">Sastres</option>
                                            <option value="Mecanicos">Mecánicos</option>
                                            <option value="San Luis">San Luis</option>
                                            <option value="Oficina">Oficina Lazaro</option>
                                            <option value="Aguascalientes">Aguascalientes</option>
                                        </select>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <!-- Table MD > Devices -->
                        <div class="table-responsive d-none d-sm-none d-md-block">
                            <table class="table table-bordered table-striped table-sm text-center table-hover">
                                <thead>
                                <tr class="text-center">
                                    <th>Opciones</th>
                                    <th>Código</th>
                                    <th>SKU</th>
                                    <th>Descripción</th>
                                    <th>Stock</th>
                                    <th>Ubicacion</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody v-if="arrayArticulo.length">
                                <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                    <td>
                                        <button type="button" @click="agregarDetalleModal(articulo)" class="btn btn-success btn-sm">
                                            <i class="icon-check"></i>
                                        </button>
                                    </td>
                                    <td v-text="articulo.codigo"></td>
                                    <td v-text="articulo.sku"></td>
                                    <td v-text="articulo.descripcion"></td>
                                    <td v-text="articulo.stock"></td>
                                    <td v-text="articulo.ubicacion"></td>
                                    <td>
                                        <div v-if="articulo.condicion">
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
                                        <td colspan="7" class="text-center">
                                            <strong>NO hay artículos con ese criterio...</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Table SM < Devides -->
                        <table class="table table-striped table-responsive-xl d-block d-sm-block d-md-none">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center">Artículos</th>
                                    <!-- <th>Edad</th>
                                    <th>País</th> -->
                                </tr>
                            </thead>
                            <tbody v-if="arrayArticulo.length">
                                <tr v-for="articulo in arrayArticulo" :key="articulo.id" :class="{'bg-danger text-white' : articulo.condicion == 0}">
                                    <td v-if="articulo.file">
                                        <lightbox class="m-0" album="" :src="'images/tools/'+articulo.file">
                                            <img class="img-responsive img-fluid imgcenter" width="100px" :src="'images/tools/'+articulo.file">
                                        </lightbox>&nbsp;
                                        <br>
                                        <strong> Ubicacion: {{articulo.ubicacion}}</strong>
                                    </td>
                                    <td v-else><strong> Ubicacion: {{articulo.ubicacion}}</strong></td>
                                    <td>{{ articulo.sku }} <br> {{ articulo.descripcion}} <br> <strong>$ {{ articulo.precio_venta}} </strong>  </td>
                                    <td>
                                        <div class="btn btn-group mr-3 mb-2">
                                            <button class="btn btn-outline-light text-dark dropdown-toggle" id="dp-categorias" data-toggle="dropdown">&bull;&bull;&bull;</button>
                                            <div class="dropdown-menu" aria-labelledby="dp-categorias">
                                                <a href="#" @click="agregarDetalleModal(articulo)" class="dropdown-item"><i class="icon-plus"></i>&nbsp;Añadir</a>
                                            </div>
                                        </div><br>
                                        <strong> Disponibles: {{articulo.stock}}</strong>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <strong>NO hay artículos registrados o con ese criterio...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Paginacion MODAL -->
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="paginationart.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page - 1,buscarA,criterioA,bodegaA)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumberArt" :key="page" :class="[page == isActivedArt ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(page,buscarA,criterioA,bodegaA)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="paginationart.current_page < paginationart.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page + 1,buscarA,criterioA,bodegaA)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                        <hr class="d-block d-sm-block d-md-none">
                        <div class="float-right d-block d-sm-block d-md-none">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        </div>
                    </div>
                    <div class="modal-footer d-none d-sm-none d-md-block">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
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
import vSelect from 'vue-select';
/* import 'vue-select/dist/vue-select.css'; */
export default {
    data() {
        return {
            identrada: 0,
            idproveedor : 0,
            nombre_proveedor : "",
            rfc_proveedor : "",
            contacto_prov : "",
            tel_contacto : "",
            proveedor: '',
            user: '',
            idusuario : 0,
            tipo_comprobante : 'INGRESO',
            num_comprobante : '',
            fecha_hora : '',
            impuesto : 0.16,
            total : 0.0,
            total_parcial : 0.0,
            estado : '',
            arrayDetalle : [],
            arrayEntrada : [],
            arrayArticulo : [],
            arrayProveedor : [],
            criterio : 'num_comprobante',
            estadoIn : '',
            buscar : '',
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            modal: 0,
            tituloModal: "",
            tipoAccion: 0,
            errorEntrada: 0,
            errorMostrarMsjEntrada: [],
            CodeDate : "",
            sigNum : 0,
            listado : 1,
            itsNewEn : 1,
            timeNow : "",
            criterioA : "sku",
            buscarA : "",
            bodegaA : "",
            idarticulo : 0,
            articulo : "",
            codigo_art : "",
            sku_art : '',
            precio_art : 0,
            stock_art : 0,
            descripcion_art : "",
            ubicacion_art : '',
            file_art : '',
            condicion_art : 0,
            imagenMinatura_art : '',
            paginationart : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            cantidad : 0

        };
    },
    components : {
        vSelect,
    },
    computed:{
        isActived: function(){
            return this.pagination.current_page;
        },
        pagesNumber: function(){
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
        isActivedArt: function(){
            return this.paginationart.current_page;
        },
        pagesNumberArt: function() {
            if(!this.paginationart.to) {
                return [];
            }

            var from = this.paginationart.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.paginationart.last_page){
                to = this.paginationart.last_page;
            }

            var pagesArray = [];
            while(from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        getFechaCode : function(){
            let me = this;
            let date = "";
            moment.locale('es');
            date = moment().format('YYMMDD');
            me.CodeDate = moment().format('YYMMDD');
            return date;
        },
        gettimeNow(){
            moment.locale('es');
            let me=this;
            var dateNow = moment().format('DD-MMMM-YYYY hh:mm:ss a');
            return dateNow;
        }
    },
    methods: {
        listarEntrada(page,buscar,criterio,estado){
            let me=this;
            var url= '/entrada?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado=' + estado;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayEntrada = respuesta.entradas.data;
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
            me.listarEntrada(page,buscar,criterio);
        },
        convertDateEntrada(date){
            moment.locale('es');
            let me=this;
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            /* console.log(datec); */
            return datec;
        },
        selectProveedor(search,loading){
            let me=this;
            loading(true)

            var url= '/proveedor/selectProveedor?filtro='+search;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                q: search
                me.arrayProveedor = respuesta.proveedores;
                loading(false)
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        getDatosProveedor(val1){
            let me = this;
            me.loading = true;
            me.nombre_proveedor = val1.nombre;
            me.idproveedor = val1.id;
            me.rfc_proveedor = val1.rfc;
            me.contacto_prov = val1.contacto;
            me.tel_contacto = val1.telefono_contacto;
        },
        getLastNum(){
            let me=this;
            var url= '/entrada/nextNum';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                /* console.log('Last Num : ' + (respuesta+1)); */
                me.num_comprobante = (respuesta + 1);
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        nuevoIngreso(){
            this.getLastNum();
            this.listado = 0;
            this.arrayDetalle = [];
            this.idproveedor = 0;
            /* this.num_comprobante = this.sigNum; */
            this.itsNewEn = 0;
        },
        cerrarNuevoIngreso(){
            this.listado = 1;
            this.arrayDetalle = [];
            this.idproveedor = 0;
            this.num_comprobante = '';
            this.itsNewEn = 1;
            this.nombre_proveedor = "";
            this.idproveedor = "";
            this.rfc_proveedor = "";
            this.contacto_prov = "";
            this.tel_contacto = "";
        },
        abrirModal() {
            this.arrayArticulo=[];
            this.modal = 1;
            this.tituloModal = "Seleccionar Artículos";
            this.listarArticulo(1,'','sku','','');
        },
        cerrarModal(){
            this.modal = 0;
            this.buscarA = "";
            this.bodegaA = "";
        },
        buscarArticulo(){
            let me = this;
            var url= '/herramienta/buscarArticulo?filtro='+ me.codigo_art;

            axios.get(url).then(function (response) {
                let respuesta = response.data;
                me.arrayArticulo=respuesta.articulos;

                if(me.arrayArticulo.length > 0){
                    me.idarticulo = me.arrayArticulo[0]['id'];
                    me.codigo_art = me.arrayArticulo[0]['codigo'];
                    me.articulo = me.arrayArticulo[0]['sku'];
                    me.precio_art = me.arrayArticulo[0]['precio_venta'];
                    me.stock_art = me.arrayArticulo[0]['stock'];
                    me.descripcion_art =  me.arrayArticulo[0]['descripcion'];
                    me.ubicacion_art =  me.arrayArticulo[0]['ubicacion'];
                    me.file_art = me.arrayArticulo[0]['file'];
                }else{
                    me.articulo = 'No existe este artículo';
                    me.idarticulo = 0;
                }
            })
            .catch(function (error) {
                console.log(error);
            });


        },
        agregarDetalle(){
            let me = this;
            if(me.idarticulo == 0 || me.cantidad == 0){
            }else{
                if(me.encuentra(me.idarticulo)){
                    swal.fire(
                        'Error!',
                        'El artículo ya esta en el listado.',
                        'warning'
                    );
                    me.codigo_art = "";
                    me.idarticulo = "";
                    me.articulo = "";
                    me.precio_art = "";
                    me.stock_art = 0;
                    me.descripcion_art = "";
                    me.ubicacion_art = "";
                    me.file_art = "";
                    me.cantidad = 0;
                }else{
                    me.arrayDetalle.push({
                        idarticulo       : me.idarticulo,
                        codigo           : me.codigo_art,
                        sku              : me.articulo,
                        descripcion      : me.descripcion_art,
                        ubicacion        : me.ubicacion_art,
                        precio           : me.precio_art,
                        stock            : me.stock_art,
                        cantidad         : me.cantidad
                    });
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Añadido correctamente',
                        showConfirmButton: false,
                        timer: 500
                    });
                    me.idarticulo = "";
                    me.codigo_art = "";
                    me.idarticulo = "";
                    me.articulo = "";
                    me.precio_art = "";
                    me.stock_art = 0;
                    me.descripcion_art = "";
                    me.ubicacion_art = "";
                    me.file_art = "";
                    me.cantidad = 0;
                }
            }
        },
        encuentra(id){
            var sw=0;
            for(var i=0;i<this.arrayDetalle.length;i++){
                if(this.arrayDetalle[i].idarticulo==id){
                    sw=true;
                }
            }
            return sw;
        },
        eliminarDetalle(index){
            let me = this;
            me.arrayDetalle.splice(index,1);
        },
        listarArticulo(page,buscar,criterio,bodega){
            let me=this;
            var url= '/herramienta/listarArticuloEntrada?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&bodega=' + bodega;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayArticulo = respuesta.herramientas.data;
                me.paginationart= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPaginaArt(page,buscar,criterio,bodega){
            let me = this;
            //Actualiza la página actual
            me.paginationart.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarArticulo(page,buscar,criterio,bodega);
        },
        agregarDetalleModal(data =[]){
            let me=this;
            if(me.encuentra(data['id'])){
                swal.fire(
                    'Error!',
                    'El artículo ya esta en el listado.',
                    'warning'
                );
            }
            else{
                me.arrayDetalle.push({
                    idarticulo       : data['id'],
                    codigo           : data['codigo'],
                    sku              : data['sku'],
                    descripcion      : data['descripcion'],
                    ubicacion        : data['ubicacion'],
                    precio           : data['precio_venta'],
                    stock            : data['stock'],
                    cantidad         : 1

                });
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    /* title: 'Añadido correctamente', */
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        },
        validarEntrada() {
            let me = this;
            var art;
            me.errorEntrada = 0;
            me.errorMostrarMsjEntrada = [];


            me.arrayDetalle.map(function(x){
                if(x.cantidad <= 0){
                    art ="La cantidad del articulo " + x.codigo + " no puede ser 0.";
                    me.errorMostrarMsjEntrada.push(art);
                }
            });

            if (me.idproveedor==0) me.errorMostrarMsjEntrada.push("Seleccione un proveedor");
            if (!me.num_comprobante) me.errorMostrarMsjEntrada.push("Ingrese el numero de comprobante");
            if (me.arrayDetalle.length<=0) me.errorMostrarMsjEntrada.push("Introdusca articulos para registrar");
            if (me.errorMostrarMsjEntrada.length) me.errorEntrada = 1;
            return me.errorEntrada;

        },
        registrarEntrada(){

            if (this.validarEntrada()) {
                return;
            }

            let me = this;

            var numcomp = "E-".concat(me.CodeDate,"-",me.num_comprobante);

            axios.post('/entrada/registrar',{
                'idproveedor': this.idproveedor,
                'tipo_comprobante': this.tipo_comprobante,
                'num_comprobante' : numcomp,
                'impuesto' : this.impuesto,
                'total' : this.total,
                'data': this.arrayDetalle
            }).then(function(response) {
                me.cerrarNuevoIngreso();
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Entrada registrada con éxito',
                    showConfirmButton: false,
                    timer: 2000
                });
                me.listarEntrada(1,'','num_comprobante','','');
                me.idproveedor= "";
                me.tipo_comprobante = "INGRESO";
                me.num_comprobante = "";
                me.impuesto = 0.16;
                me.total = 0.0;
                me.arrayDetalle = [];

            })
            .catch(function(error) {
                console.log(error);
            });
        },
        verEntrada(id){
            let me = this;
            me.listado = 2;
            me.itsNewEn = 0;
            var arrayEntradaT = [];
            var url= '/entrada/obtenerCabecera?id=' + id;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                arrayEntradaT = respuesta.entrada;

                var fechaform  = arrayEntradaT[0]['fecha_hora'];

                me.identrada = arrayEntradaT[0]['id'];
                me.proveedor = arrayEntradaT[0]['proveedor'];
                me.tipo_comprobante=arrayEntradaT[0]['tipo_comprobante'];
                me.num_comprobante=arrayEntradaT[0]['num_comprobante'];
                me.user=arrayEntradaT[0]['usuario'];
                me.impuesto = arrayEntradaT[0]['impuesto'];
                me.total = arrayEntradaT[0]['total'];
                me.estado = arrayEntradaT[0]['estado'];
                moment.locale('es');
                me.fecha_hora=moment(fechaform).format('dddd DD MMM YYYY hh:mm:ss a');
            })
            .catch(function (error) {
                console.log(error);
            });

            //Obtener los detalles del ingreso
            var url= '/entrada/obtenerDetalles?id=' + id;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayDetalle = respuesta.detalles;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cancelarEntrada(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de anular esta entrada?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/entrada/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarEntrada(1,'','num_comprobante','','');
                        swal.fire(
                            'Anulado!',
                            'La entrada ha sido anulado con éxito.',
                            'success'
                        );
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        pdfEntrada(id){
            window.open('/entrada/pdf/'+id,'_blank');
        },
    },

    mounted() {
        this.listarEntrada(1,this.buscar, this.criterio,this.estadoIn);
    }
};
</script>
