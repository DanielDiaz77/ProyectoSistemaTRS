<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a> </li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Traslado Abrasivo
                    <button v-if="btnNewTask" type="button" @click="mostrarDetalle()" class="btn btn-secondary" >
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                    <template v-if="listado == 2"><button v-if="entregado == 0" type="button" @click="editarTraslado(traslado_id)"  class="btn btn-sm btn-warning float-right">Editar</button></template>
                    <button v-if="btnNewTask==0" type="button" @click="ocultarDetalle()"  class="btn btn-sm btn-primary float-right mr-3">Volver</button>
                </div>
                <!-- Listado -->
                <template v-if="listado==1">
                    <div class="card-body">
                        <div class="form-inline">
                            <div class="form-group mb-2 col-12">
                                <div class="input-group">
                                    <select class="form-control mb-1" v-model="criterio">
                                        <option value="num_comprobante">No° Comprobante</option>
                                        <option value="fecha_hora">Fecha</option>
                                        <option value="nueva_ubicacion">Ubicaciones</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup.enter="listarTraslado(1,buscar,criterio,estadoTraslado)" class="form-control mb-1" placeholder="Texto a buscar...">
                                </div>
                                <div class="input-group">
                                    <select class="form-control mb-1" v-model="estadoTraslado" @change="listarTraslado(1,buscar,criterio,estadoTraslado)">
                                        <option value="">Activo</option>
                                        <option value="Anulado">Cancelado</option>
                                    </select>
                                    <button type="submit" @click="listarTraslado(1,buscar,criterio,estadoTraslado)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <!-- Table For SM > Devices -->
                        <div class="table-responsive col-md-12 d-none d-sm-none d-md-block">
                            <table class="table table-bordered table-striped table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Realizó</th>
                                        <th>Tipo Comprobante</th>
                                        <th>No° Comprobante</th>
                                        <th>Fecha Hora</th>
                                        <th>Lugar de Traslado</th>
                                        <th>Estado</th>
                                        <th>Entregado</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayTraslado.length">
                                    <tr v-for="traslado in arrayTraslado" :key="traslado.id">
                                        <td>
                                            <div class="form-inline">
                                                <button type="button" class="btn btn-success btn-sm" @click="verTraslado(traslado.id)">
                                                <i class="icon-eye"></i>
                                                </button>&nbsp;
                                                <template v-if="traslado.estado=='Registrado'">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfTraslado(traslado.id)">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                    </button>&nbsp;
                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                        @click="excelTraslado(traslado.id,traslado.num_comprobante)">
                                                        <i class="fa fa-file-excel-o"></i>
                                                    </button>&nbsp;
                                                    </template>
                                                    <template >
                                                        <button type="button" class="btn btn-danger btn-sm" @click="anularTraslado(traslado.id)">
                                                            <i class="icon-trash"></i>
                                                        </button>
                                                    </template>

                                            </div>
                                        </td>
                                        <td v-text="traslado.usuario"></td>
                                        <td v-text="traslado.tipo_comprobante"></td>
                                        <td v-text="traslado.num_comprobante"></td>
                                        <td>{{ convertDateTraslado(traslado.fecha_hora) }}</td>
                                        <td v-text="traslado.nueva_ubicacion"></td>
                                        <td>
                                            <template v-if="traslado.estado != 'Anulado'">
                                                <span class="badge badge-success">Registrado</span>
                                            </template>
                                            <template v-else>
                                                <span class="badge badge-danger">Cancelado</span>
                                            </template>
                                        </td>
                                        <td>
                                            <template v-if="traslado.entregado">
                                                <span class="badge badge-success">100%</span>
                                            </template>
                                            <template v-else>
                                                <span class="badge badge-danger">No entregado</span>
                                            </template>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <strong>NO hay traslados registrados o con ese criterio...</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoTraslado)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoTraslado)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoTraslado)">Sig</a>
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
                        </div>
                        <!-- END Proveedor Section -->
                        <div class="form-group row border">
                            <div class="col-md-2 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Tipo Comprobante (*)</strong></label>
                                    <select v-model="tipo_comprobante" class="form-control">
                                        <option value="">Seleccione</option>
                                        <option value="TRASLADO">TRASLADO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Número de traslado (*)</strong></label>
                                    <div class="d-flex justify-content-center">
                                        <div><input type="number" readonly :value="getFechaCode" class="form-control"/></div>
                                        <div><input type="text" class="form-control" v-model="num_comprobante" placeholder="000xx"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-12 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Ubicacion Anterior</strong><span style="color:red;" v-show="ubicacionant==''">(*Seleccione)</span></label>
                                    <select class="form-control" v-model="ubicacionant" :disabled="arrayDetalle.length!=0" >
                                        <option value="" disabled>Seleccione lugar de traslado</option>
                                        <option value="Del Musico">Del Músico</option>
                                        <option value="Escultores">Escultores</option>
                                        <option value="Sastres">Sastres</option>
                                        <option value="Mecanicos">Mecánicos</option>
                                        <option value="Oficina">Oficina Lazaro</option>
                                        <option value="San Luis">San Luis</option>
                                        <option value="Aguascalientes">Aguascalientes</option>
                                        <option value="Puebla">Puebla</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-12 text-center">
                                <div class="form-group" >
                                    <label for=""><strong>Lugar del traslado</strong><span style="color:red;" v-show="nueva_ubicacion==''">(*Seleccione)</span></label>
                                    <select class="form-control" v-model="nueva_ubicacion" :disabled="arrayDetalle.length!=0">
                                        <option value="" disabled>Seleccione lugar de traslado</option>
                                        <option value="Del Musico">Del Músico</option>
                                        <option value="Escultores">Escultores</option>
                                        <option value="Sastres">Sastres</option>
                                        <option value="Mecanicos">Mecánicos</option>
                                        <option value="Oficina">Oficina Lazaro</option>
                                        <option value="San Luis">San Luis</option>
                                        <option value="Aguascalientes">Aguascalientes</option>
                                        <option value="Puebla">Puebla</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-12 mt-2 text-center" v-if="nueva_ubicacion != ubicacionant">
                                <label for=""><strong>Elegir Artículos</strong></label>&nbsp;
                                <template v-if="nueva_ubicacion == '' "><p class="text-danger font-weight-bold">Selecciona la nueva ubicación</p></template>
                                <template v-else><button @click="abrirModal()" class="btn btn-primary">&bull;&bull;&bull;</button></template>

                            </div>

                            <div class="col-xl-2 col-lg-6 col-md-6 col-12 text-center">
                                        <label for=""><strong>Imagen</strong></label>
                                        <input type="file" :src="imagen" @change="obtenerImagen" class="form-control-file">
                                    </div>
                            <div class="col-sm-2 text-center mt-1">
                            <div class="col-md-12">
                                <div v-show="errorTraslado" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjTraslado" :key="error" v-text="error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>
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
                                            <td v-text="detalle.descripcion"></td>
                                            <td v-text="detalle.ubicacion"></td>
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
                        </div>
                        <!-- Observaciones -->
                        <div class="form-group row d-flex justify-content-around">
                            <div class="col-md-4 text-center">
                                <label for="exampleFormControlTextarea2"><strong>Observaciones</strong></label>
                                <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacion_traslado"></textarea>
                            </div>&nbsp;
                            <div class="col-md-4 text-center d-flex justify-content-center" v-if="showElim">
                                <template v-if="imagenMinatura !='images/traslados/null'">
                                    <lightbox class="m-0" album="" :src="imagen">
                                        <figure>
                                            <img width="300" height="200" class="img-responsive img-fluid imgcenter" :src="imagen" alt="Comprobante del traslado">
                                        </figure>
                                    </lightbox>&nbsp;
                                    <div class="col-1">
                                        <button type="button" class="btn btn-danger btn-circle float-left" aria-label="Eliminar imagen" @click="eliminarImagen(traslado_id,imagen)">
                                            <i class="fa fa-times"></i>
                                        </button>&nbsp;
                                    </div>
                                </template>
                            </div>&nbsp;
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                                <button type="button" @click="registrarTraslado()" class="btn btn-primary">Registrar Traslado</button>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- Fin Nuevo Ingreso -->

                <!-- Ver Ingreso -->
                <template v-else-if="listado==2">
                    <div class="card-body">
                        <div class="form-group row d-flex justify-content-around">
                            <div class="form-group p-2">
                                <label for=""><strong>Proveedor</strong></label>
                                <p v-text="proveedor"></p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Realizó</strong></label>
                                <p v-text="user"></p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Comprobante</strong></label>
                                <p> {{ tipo_comprobante }} - {{ num_comprobante }} </p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Fecha:</strong></label>
                                <p v-text="fecha_hora"></p>
                            </div>
                            <template v-if="usrol != 1">
                                <div class="form-group p-2" v-if="!entregado">
                                    <label for=""><strong>Entregado 100%:</strong> </label>
                                    <div v-if="!entregado">
                                        <toggle-button @change="cambiarEstadoEntrega(idtrasladoabrasivo)" v-model="btnEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Anulado</span>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="form-group p-2">
                                    <label for=""><strong>Entregado 100%:</strong> </label>
                                    <div v-if="estadoVn !='Anulado'">
                                        <toggle-button @change="cambiarEstadoEntrega(idtrasladoabrasivo)" v-model="btnEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Anulado</span>
                                    </div>
                                </div>
                            </template>
                            <div class="form-group p-2">
                                <label for=""><strong>ubicacion Anterior</strong></label>
                                <p v-text="ubicacionant"></p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Trasladados a</strong></label>
                                <p v-text="nueva_ubicacion"></p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Estado</strong></label>
                                <!-- <p v-text="estadoVn"></p> -->
                                <template v-if="estadoVn != 'Anulado'">
                                    <span class="badge badge-success">Registrado</span>
                                </template>
                                <template v-else>
                                    <span class="badge badge-danger">Anulado</span>
                                </template>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <div class="table-responsive col-md-12 d-none d-sm-none d-md-block">
                                <table class="table table-bordered table-striped table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>No°</th>
                                            <th>SKU</th>
                                            <th>Código</th>
                                            <td>Stock</td>
                                            <th>Ubicacion Actual</th>
                                            <th>Cantidad</th>
                                            <th>Descripción</th>
                                            <th>Condicion</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                            <td width="10px">{{ (index + 1) }}</td>
                                            <td v-text="detalle.articulo"></td>
                                            <td v-text="detalle.codigo"></td>
                                            <td v-text="detalle.stock"></td>
                                            <td v-text="detalle.ubicacion"></td>
                                            <td v-text="detalle.cantidad"></td>
                                            <td v-text="detalle.descripcion"></td>
                                            <td>
                                                <div v-if="detalle.condicion == 4">
                                                    <span style="color:#fff" class="badge badge-info">En traslado</span>
                                                </div>
                                                <div v-else-if="detalle.condicion == 1">
                                                    <span class="badge badge-success">Activo</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <strong>NO hay artículos en este detalle...</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row d-flex justify-content-around">
                            <div>
                                <div class="form-group p-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <label for="exampleFormControlTextarea2"><strong>Observaciones</strong></label>
                                        </div>&nbsp;
                                        <div class="mb-2" v-if="estadoVn !='Anulado'">
                                            <template v-if="obsEditable == 0">
                                                <button type="button" class="btn btn-warning btn-sm float-right" @click="editObservacion()">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                            </template>
                                            <template v-else>
                                                <button type="button" class="btn btn-primary btn-sm float-right" @click="actualizarObservacion(traslado_id)">
                                                    <i class="fa fa-floppy-o"></i>
                                                </button>
                                            </template>&nbsp;
                                        </div>
                                    </div>
                                    <textarea class="form-control rounded-0" rows="5" cols="60" maxlength="256" :readonly="!obsEditable"
                                        v-model="observacion_traslado" style="resize: none;"></textarea>
                                </div>
                            </div>
                            <div>
                                <div class="text-center d-flex justify-content-center" v-if="showElim">
                                    <template v-if="imagenMinatura !='/images/traslados/null'">
                                        <div>
                                            <lightbox class="m-0" album="" :src="imagen">
                                                <figure class="mr-2">
                                                    <img width="300" height="200" class="img-responsive img-fluid imgcenter" :src="imagen" alt="Comprobante del traslado">
                                                </figure>
                                            </lightbox>&nbsp;
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-danger btn-circle float-left" aria-label="Eliminar imagen" @click="eliminarImagen(traslado_id,imagen)">
                                                <i class="fa fa-trash"></i>
                                            </button>&nbsp;
                                        </div>
                                    </template>
                                </div>&nbsp;
                                <div class="d-flex justify-content-center" v-if="estadoVn !='Anulado'">
                                    <div>
                                        <label for=""><strong>Actualizar Imagen</strong></label>
                                        <input type="file" :src="imagen" @change="obtenerImagen" class="form-control-file">
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-primary btn-circle" @click="updImage()">
                                            <i class="fa fa-floppy-o"></i>
                                        </button>&nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
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
                                        <select class="form-control" v-model="bodegaA" @keyup.enter="listarArticulo(1,buscarA,criterioA,bodegaA)">
                                            <option value="" disabled>Ubicacion</option>
                                            <option value="">Todas</option>
                                            <option value="Del Musico">Del Músico</option>
                                            <option value="Escultores">Escultores</option>
                                            <option value="Sastres">Sastres</option>
                                            <option value="Mecanicos">Mecánicos</option>
                                            <option value="Tractorista">Tractorista</option>
                                            <option value="San Luis">San Luis</option>
                                            <option value="Oficina">Oficina Lazaro</option>
                                            <option value="Aguascalientes">Aguascalientes</option>
                                            <option value="Puebla">Puebla</option>

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
        <!--Inicio del modal listar articulos-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal2}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
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
                                        <select class="form-control" v-model="bodegaA" @keyup.enter="listarArticulo(1,buscarA,criterioA,bodegaA)">
                                            <option value="" disabled>Ubicacion</option>
                                            <option value="">Todas</option>
                                            <option value="Del Musico">Del Músico</option>
                                            <option value="Escultores">Escultores</option>
                                            <option value="Sastres">Sastres</option>
                                            <option value="Mecanicos">Mecánicos</option>
                                            <option value="Tractorista">Tractorista</option>
                                            <option value="San Luis">San Luis</option>
                                            <option value="Oficina">Oficina Lazaro</option>
                                            <option value="Aguascalientes">Aguascalientes</option>
                                            <option value="Puebla">Puebla</option>

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
                                        <button type="button" @click="actualizarArticulo(articulo)" class="btn btn-success btn-sm">
                                            <i class="icon-check"></i>
                                        </button>
                                    </td>
                                    <td v-text="articulo.codigo"></td>
                                    <td v-text="articulo.sku"></td>
                                    <td v-text="articulo.descripcion"></td>
                                    <td>
                                        <input v-model="articulo.skock" min="0" type="number" value="2" class="form-control">
                                    </td>
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
                            <button type="button" class="btn btn-secondary" @click="cerrarModal2()">Cerrar</button>
                        </div>
                    </div>
                    <div class="modal-footer d-none d-sm-none d-md-block">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal2()">Cerrar</button>
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
import VueBarcode from 'vue-barcode';
import VueLightbox from 'vue-lightbox';
import ToggleButton from 'vue-js-toggle-button'
/* import 'vue-select/dist/vue-select.css'; */
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton)
export default {
    data() {
        return {
            traslado_id : 0,
            user: '',
            idproveedor : 0,
            nombre_proveedor : "",
            proveedor: '',
            user: '',
            idusuario : 0,
            tipo_comprobante : "TRASLADO",
            num_comprobante : '',
            nueva_ubicacion : "",
            ubicacionant: "",
            fecha_hora : '',
            observacion_traslado : "",
            impuesto : 0.16,
            total : 0.0,
            total_parcial : 0.0,
            estado : '',
            arrayDetalle : [],
            arrayTraslado: [],
            arrayArticulo : [],
            arrayProveedor : [],
            criterio : 'num_comprobante',
            buscar : '',
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            showElim : false,
            offset : 3,
            modal: 0,
            modal2: 0,
            tituloModal: "",
            tipoAccion: 0,
            errorTraslado: 0,
            errorMostrarMsjTraslado: [],
            CodeDate : "",
            sigNum : 0,
            listado : 1,
            itsNewEn : 1,
            timeNow : "",
            criterioA : "sku",
            buscarA : "",
            bodegaA : "",
            idabrasivo : 0,
            articulo : "",
            sku_art : '',
            precio_art : 0,
            stock_art : 0,
            descripcion_art : "",
            ubicacion_art : '',
            file_art : '',
            condicion_art : 0,
            imagenMinatura_art : '',
            file_art : '',
            imagenMinatura : '',
            paginationart : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            cantidad : 0,
            btnNewTask : 1,
            file_traslado : '',
            estadoTraslado : "",
            buscarArt : '',
            criterioArt : 'sku',
            bodegaArt : "",
            acabadoArt : "",
            usrol : 0,
            editTraslado : 0,
            entregado : 0,
            estadoVn : "",
            obsEditable : 0,
            btnEntrega : false,
            btnNewTask : 1,



        };
    },
    components : {
        vSelect,
        'barcode': VueBarcode
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
        },
        imagen(){
             return this.imagenMinatura;
        },
    },
    methods: {
        listarTraslado(page,buscar,criterio){
            let me=this;
            me.btnNewTask = 1;
            var url= '/trasladoabrasivo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayTraslado = respuesta.traslados.data;
                me.pagination= respuesta.pagination;
                me.usrol = respuesta.userrol;
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
            me.listarTraslado(page,buscar,criterio);
        },
        convertDateTraslado(date){
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
        },
        getLastNum(){
            let me=this;
                var url= '/trasladoabrasivo/nextNum';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.sigNum = respuesta.SigNum;
                    /* console.log("Next Num = " + me.sigNum); */
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        mostrarDetalle()  {
            this.listado = 0;
            this.getLastNum();
            this.idabrasivo = 0;
            this.idproveedor = 0;
            this.codigo = "";
            this.sku = "";
            this.ubicacion = "";
            this.ubicacionant="";
            this.stock = 0;
            this.descripcion = "";
            this.arrayDetalle = [];
            this.imagenMinatura = 'images/traslados/null';
            /* console.log("Next Num Detalle = " + parseInt(this.sigNum)); */
            this.num_comprobante = (parseInt(this.sigNum)+1);
            this.editTraslado = 0;
        },
        ocultarDetalle(){
            this.listado = 1;
            this.getLastNum();
            this.idabrasivo = 0;
            this.codigo = "";
            this.sku = "";
            this.ubicacion = "";
            this.stock = 0;
            this.descripcion = "";
            this.arrayDetalle = [];
            this.num_comprobante = "";
            this.traslado_id = 0;
            this.user= '';
            this.idproveedor = 0;
            this.nombre = "";
            this.tipo_comprobante = "TRASLADO";
            this.nueva_ubicacion = "";
            this.ubicacionant="";
            this.entregado = 0;
            this.cantidad = 0;
            this.observacion_traslado = "";
            this.file_traslado = '';
            this.arrayDetalle = [];
            this.obsEditable = 0;
            this.imagenMinatura = "";
            this.listarTraslado(1,'','num_comprobante','');
            this.btnNewTask = 1;
            this.btnEntrega = false;
        },
        obtenerImagen(e){
            let img = e.target.files[0];
            /*  console.log(img); */
            this.file_traslado = img;
            this.cargarImagen(img);
        },
        cargarImagen(img){
            let reader = new FileReader();
            reader.onload = (e) => {
                this.imagenMinatura = e.target.result;
                this.file_traslado =  e.target.result;
            }
            reader.readAsDataURL(img);
        },
        abrirModal() {
            this.arrayArticulo=[];
            this.modal = 1;
            this.nueva_ubicacion;
            this.bodegaA = this.nueva_ubicacion;
            this.tituloModal = "Seleccionar Artículos";
            this.listarArticulo(1,'','sku','','');
            this.listarArticulo(1,this.buscarA,this.criterioA,this.bodegaA);
        },
        cerrarModal(){
            this.modal = 0;
            this.modal = 0;
            this.buscarA = "";
            this.bodegaA = "";
            this.errorTraslado = 0;
            this.errorMostrarMsjTraslado = [];
        },
        /*abrirModal2() {
            this.arrayArticulo=[];
            this.modal2 = 1;
            this.ubicacionant;
            this.bodegaA = this.ubicacionant;
            this.tituloModal = "Actualizar el Stock";
            this.listarArticuloStock(1,'','sku','','');
            this.listarArticuloStock(1,this.buscarA,this.criterioA,this.bodegaA);
        },*/
        /*cerrarModal2(){
            this.modal2 = 0;
            this.modal2 = 0;
            this.buscarA = "";
            this.bodegaA = "";
            this.errorTraslado = 0;
            this.errorMostrarMsjTraslado = [];
        },*/
        actualizarArticulo() {

            var page = this.pagination.current_page;
            var crit =  this.criterio;
            var busc = this.buscar;
            var bod = this.bodega;
            let art = this.sku;
            let cdn = this.codigo;

            if (this.validarArticulo()) {
                return;
            }

            let me = this;
            axios.put("/abrasivos/actualizar", {
                'codigo': this.codigo,
                'sku' : this.sku,
                'precio_venta' : this.precio_venta,
                'ubicacion' : this.ubicacion,
                'stock': this.stock,
                'descripcion': this.descripcion,
                'file' : this.file,
                'id': this.articulo_id,
            })
            .then(function(response) {
                Swal.fire(
                    "Actualizado!",
                    `El articulo ${ art } - ${ cdn } ha sido actualizado con éxito`,
                    "success"
                )
                me.closeEdit();
                me.listarArticulo(page,busc,crit,bod,1,0);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        buscarArticulo(){
            let me = this;
            var url= '/abrasivo/buscarArticulo?filtro='+ me.codigo;

            axios.get(url).then(function (response) {
                let respuesta = response.data;
                me.arrayArticulo=respuesta.articulos;

                if(me.arrayArticulo.length > 0){
                    me.idabrasivo       = me.arrayArticulo[0]['id'];
                    me.codigo           = me.arrayArticulo[0]['codigo'];
                    me.articulo         = me.arrayArticulo[0]['sku'];
                    me.precio_art       = me.arrayArticulo[0]['precio_venta'];
                    me.stock_art        = me.arrayArticulo[0]['stock'];
                    me.descripcion_art  =  me.arrayArticulo[0]['descripcion'];
                    me.ubicacion_art    =  me.arrayArticulo[0]['ubicacion'];
                    me.file_art         = me.arrayArticulo[0]['file'];
                }else{
                    me.articulo = 'No existe este artículo';
                    me.idabrasivo = 0;
                }
            })
            .catch(function (error) {
                console.log(error);
            });


        },
        agregarDetalle(data = []){
            let me = this;

            if(me.encuentraUbicacion(data['ubicacion'])){
                    Swal.fire({
                        type: 'error',
                        title: 'Lo siento...',
                        text: 'Este abrasivo ya esta en la ubicacion a la que quieres trasladar!!',
                    })
                    return;
                }

                if(me.encuentra(data['id'])){
                     Swal.fire({
                        type: 'error',
                        title: 'Lo siento...',
                        text: 'Este abrasivo ya esta en el listado!!',
                    })
                }else{
                    me.arrayDetalle.push({
                        idabrasivo       : data['id'],
                        articulo         : data['sku'],
                        codigo           : data['codigo'],
                        precio           : data['precio_venta'],
                        stock            : data['stock'],
                        ubicacion        : data['ubicacion'],
                        file             : data['file'],
                        descripcion      : data['descripcion'],
                        cantidad: 1,
                        descuento : 0
                    });
                    Swal.fire({
                        position: 'top-end',
                        type: 'success',
                        title: `${data['codigo']}`,
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
        },
        encuentra(id){
            var sw=0;
            for(var i=0;i<this.arrayDetalle.length;i++){
                if(this.arrayDetalle[i].idabrasivo==id){
                    sw=true;
                }
            }
            return sw;
        },
        listarArticulo(page,buscar,criterio,bodega){
            let me=this;
            if(me.zona == 'SLP'){
                    bodega = 'San Luis';
                    me.bodegaA = 'San Luis';
                }else{
                    if(bodega == 'San Luis'){
                        bodega = "";
                        me.bodegaA = "";
                    }
                }
                if(me.zona == 'AGS'){
                    bodega = 'Aguascalientes';
                    me.bodegaArt = 'Aguascalientes';
                }else{
                    if(bodega == 'Aguascalientes'){
                        bodega = "";
                        me.bodegaA = "";
                    }
                }
                if(me.zona == 'PBA'){
                    bodega = 'Puebla';
                    me.bodegaArt = 'Puebla';
                }else{
                    if(bodega == 'Puebla'){
                        bodega = "";
                        me.bodegaA = "";
                    }
                }
            var url= '/abrasivo/listarArticulo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&bodega=' + bodega;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayArticulo = respuesta.articulos.data;
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
                    idabrasivo       : data['id'],
                    codigo           : data['codigo'],
                    articulo         : data['sku'],
                    descripcion      : data['descripcion'],
                    ubicacion        : data['ubicacion'],
                    precio           : data['precio_venta'],
                    stock            : data['stock'],
                    cantidad         : 1

                });
                Swal.fire({
                        position: 'top-end',
                        type: 'success',
                        title: `${data['codigo']}`,
                        showConfirmButton: false,
                        timer: 1000
                    });
            }
        },
        eliminarDetalle(index){
            let me = this;
            me.arrayDetalle.splice(index,1);
        },
        validarTraslado() {
            let me = this;
            var art;
            me.errorTraslado = 0;
            me.errorMostrarMsjTraslado = [];


            me.arrayDetalle.map(function(x){
                if(x.cantidad <= 0){
                    art ="La cantidad del articulo " + x.codigo + " no puede ser 0.";
                    me.errorMostrarMsjTraslado.push(art);
                }
            });
            if (me.ubicacionant == me.nueva_ubicacion) me.errorMostrarMsjTraslado.push("La Ubicacion no puede ser la misma");
            if (me.idproveedor==0) me.errorMostrarMsjTraslado.push("Seleccione un proveedor");
            if (!me.num_comprobante) me.errorMostrarMsjTraslado.push("Ingrese el numero de comprobante");
            if (!me.nueva_ubicacion) me.errorMostrarMsjTraslado.push("Seleccione la ubicación del traslado");
            if (!me.ubicacionant) me.errorMostrarMsjTraslado.push("Seleccione la ubicación anterior");
            if (me.arrayDetalle.length<=0) me.errorMostrarMsjTraslado.push("Introduzca articulos para registrar");

            if (me.errorMostrarMsjTraslado.length) me.errorTraslado = 1;

            return me.errorTraslado;

        },
        registrarTraslado(){

            if (this.validarTraslado()) {
                return;
            }

            let me = this;

            var numcomp = "TA-".concat(me.CodeDate,"-",me.num_comprobante);

            axios.post('/trasladoabrasivo/registrar',{
                'idproveedor'     : this.idproveedor,
                'tipo_comprobante': this.tipo_comprobante,
                'num_comprobante' : numcomp,
                'impuesto'        : this.impuesto,
                'total'           : this.total,
                'nueva_ubicacion' : this.nueva_ubicacion,
                'ubicacionant'    : this.ubicacionant,
                'observacion'     : this.observacion_traslado,
                'file'            : this.file_traslado,
                'data'            : this.arrayDetalle
            }).then(function(response) {
                me.ocultarDetalle();
                me.listarTraslado(1,'','num_comprobante','','');
                me.idproveedor= "";
                me.tipo_comprobante = "TRASLADO";
                me.num_comprobante = 0;
                me.idabrasivo = 0;
                me.articulo = "";
                me.cantidad = 0;
                me.precio = 0;
                me.stock = 0;
                me.observacion = "";
                me.file_traslado = "";
                me.nueva_ubicacion = "";
                me.ubicacionant = "";
                me.arrayDetalle = [];
                me.editTraslado = 0;
                Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'Se ha registrado el traslado con éxito!',
                });

            })
            .catch(function(error) {
                console.log(error);
            });
        },
        verTraslado(id){
            let me = this;
            me.listado = 2;
            me.btnNewTask = 0;
            //Obtener los datos del ingreso
            var arrayTrasladoT = [];
            var url= '/trasladoabrasivo/obtenerCabecera?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                arrayTrasladoT = respuesta.traslado;

                /* console.log("Obs Traslado" + arrayTrasladoT[0]['id'] +  " : " + arrayTrasladoT[0]['obstraslado']); */

                var fechaform  = arrayTrasladoT[0]['fecha_hora'];

                var total_parcial = 0;

                me.idtrasladoabrasivo = arrayTrasladoT[0]['id'];
                me.proveedor = arrayTrasladoT[0]['nombre'];
                me.tipo_comprobante=arrayTrasladoT[0]['tipo_comprobante'];
                me.num_comprobante=arrayTrasladoT[0]['num_comprobante'];
                me.user=arrayTrasladoT[0]['usuario'];
                me.nueva_ubicacion = arrayTrasladoT[0]['nueva_ubicacion'];
                me.ubicacionant = arrayTrasladoT[0]['ubicacionant'];
                me.observacion_traslado = arrayTrasladoT[0]['obstraslado'];
                me.impuesto = arrayTrasladoT[0]['impuesto'];
                me.total = arrayTrasladoT[0]['total'];
                me.estado = arrayTrasladoT[0]['estado'];
                me.estadoVn = arrayTrasladoT[0]['estado'];
                me.fecha_hora = arrayTrasladoT[0]['fecha_hora'];
                me.entregado = arrayTrasladoT[0]['entregado'];
                moment.locale('es');
                me.fecha_hora=moment(fechaform).format('dddd DD MMM YYYY hh:mm:ss a');

                if(arrayTrasladoT[0]['entregado'] == 1){
                        me.btnEntrega = true;
                } else {
                    me.btnEntrega = false;
                }

                let hasImg = '/images/traslados/' + arrayTrasladoT[0]['file'];

                if(hasImg != '/images/traslados/null'){
                    me.imagenMinatura = '/images/traslados/'+ arrayTrasladoT[0]['file'];
                    me.showElim = true;
                }else{
                    me.imagenMinatura = '/images/traslados/null';
                    me.showElim = false;
                }

            })
            .catch(function (error) {
                console.log(error);
            });

            //Obtener los detalles del ingreso
            var url= '/trasladoabrasivo/obtenerDetalles?id=' + id;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayDetalle = respuesta.detalles;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        editObservacion(){
            let me = this;
            me.obsEditable = 1;
        },
        actualizarObservacion(id){
            let me = this;
            axios.post('/trasladoabrasivo/actualizarObservacion',{
                'id': id,
                'observacion' : this.observacion_traslado
            }).then(function (response) {
                me.obsEditable = 0;
            }).catch(function (error) {
                console.log(error);
            });
        },
        updImage(){
            let me = this;
            axios.put('/trasladoabrasivo/updImagen',{
                'file': this.file_traslado,
                'id' : this.traslado_id
            }).then(function(response) {

                /* me.ocultarDetalle(); */
                /* me.listarTraslado(1,'','num_comprobante'); */
                Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'La imagen ha sido actualizada!',
                })
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        eliminarImagen(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta de eliminar la imagen de este traslado?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    /* console.log("id: " + id + " IMG: " + file); */
                    let me = this;
                    axios.put('/trasladoabrasivo/eliminarImg', {
                        'id' : id
                    }).then(function(response) {
                        /* me.listarVenta(1,'','num_comprobante'); */
                        me.imagenMinatura = '/images/traslados/null';
                        swalWithBootstrapButtons.fire(
                            "Elimada!",
                            "La imagen ha sido eliminada con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        pdfTraslado(id){
            window.open('/trasladoabrasivo/pdf/'+id);
        },
        excelTraslado(id,num_comp){
                window.open('/trasladoabrasivo/excel/'+ id+'?num_traslado=' + num_comp);
        },
        cambiarEstadoEntrega(id){
            let me = this;
            if(me.btnEntrega == true){
                me.entregado = 1;
            }else{
                me.entregado = 0;
            }
            axios.post('/trasladoabrasivo/cambiarEntrega',{
                'id': id,
                'entregado' : this.entregado
            }).then(function (response) {
                me.verTraslado(id);
                if(me.entregado == 1){
                    Swal.fire(
                        "Completado!",
                        "El traslado ha sido marcado como entregado con éxito.",
                        "success"
                    )
                }else{
                    Swal.fire(
                        "Atención!",
                        "El traslado ha sido marcado como no entregado.",
                        "warning"
                    )
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
        anularTraslado(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de anular este traslado? Este proceso es irreversible!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/trasladoabrasivo/desactivar',{
                        'id': id
                    }).then(function (response) {
                        Swal.fire({
                            type: 'success',
                            title: 'Completado...',
                            text: 'El traslado ha sido anulado con exito!',
                        })
                        me.listarTraslado(1,'','num_comprobante','');

                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
    },

    mounted() {
        this.listarTraslado(1,this.buscar, this.criterio,this.estadoTraslado);
        this.getLastNum();
    }
};
</script>
