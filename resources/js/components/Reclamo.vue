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

                        <i class="fa fa-align-justify"></i> Reclamos
                    <button v-if="btnNewTask" type="button" @click="mostrarDetalle()" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                    <template v-if="listado == 2 && condicion == 3 || 4"><button type="button" @click="editarReclamo(reclamo_id)"  class="btn btn-sm btn-warning float-right">Editar Estado</button></template>
                    <button v-if="btnNewTask==0" type="button" @click="ocultarDetalle()"  class="btn btn-sm btn-primary float-right mr-3">Volver</button>

                </div>
                <!-- Listado principal -->
                <template v-if="listado==1">
                    <div class="card-body">
                        <div class="form-inline">
                            <div class="form-group mb-2 col-12">
                                <div class="input-group">
                                    <select class="form-control mb-1" v-model="criterio">
                                        <option value="num_comprobante">No° Comprobante</option>
                                        <option value="fecha_hora">Fecha</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup.enter="listarReclamo(1,buscar,criterio,estadoReclamo)" class="form-control mb-1" placeholder="Texto a buscar...">
                                </div>
                                <div class="input-group">
                                    <select class="form-control mb-1" v-model="estadoReclamo" @change="listarReclamo(1,buscar,criterio,estadoReclamo)">
                                        <option value="">Activo</option>
                                        <option value="Pasivo">Pasivo</option>
                                        <option value="Anulado">Anulado</option>
                                    </select>
                                    <button type="submit" @click="listarReclamo(1,buscar,criterio,estadoReclamo)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Realizó</th>
                                        <th>Tipo Comprobante</th>
                                        <th>No° Comprobante</th>
                                        <th>Fecha Hora</th>
                                        <th>Estado</th>
                                        <th>Condicion</th>
                                        <th>N° de Credito</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayReclamo.length">
                                    <tr v-for="reclamo in arrayReclamo" :key="reclamo.id">
                                        <td>
                                            <div class="form-inline">
                                                <button type="button" class="btn btn-success btn-sm" @click="verReclamo(reclamo.id)">
                                                <i class="icon-eye"></i>
                                                </button>&nbsp;
                                                <template v-if="reclamo.estado !='Anulado'">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfReclamo(reclamo.id)">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                    </button>&nbsp;
                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                        @click="excelReclamo(reclamo.id,reclamo.num_comprobante)">
                                                        <i class="fa fa-file-excel-o"></i>
                                                    </button>&nbsp;
                                                    </template>
                                                    <template v-if="usrol == 1">
                                                        <button type="button" class="btn btn-danger btn-sm" @click="anularReclamo(reclamo.id)">
                                                            <i class="icon-trash"></i>
                                                        </button>
                                                    </template>

                                            </div>
                                        </td>
                                        <td v-text="reclamo.usuario"></td>
                                        <td v-text="reclamo.tipo_comprobante"></td>
                                        <td v-text="reclamo.num_comprobante"></td>
                                        <td>{{ convertDateReclamo(reclamo.fecha_hora) }}</td>
                                        <td>
                                            <template v-if="reclamo.estado == 'Activo'">
                                                <span class="badge badge-success">Activo</span>
                                            </template>
                                            <template v-else-if="reclamo.estado == 'Pasivo'">
                                                <span class="badge badge-warning">Pasivo</span>
                                            </template>
                                            <template v-else-if="reclamo.estado == 'Anulado'">
                                                <span class="badge badge-danger">Anulado</span>
                                            </template>
                                        </td>
                                        <td>
                                            <template v-if="reclamo.condicion == 1 && reclamo.estado == 'Activo'">
                                                <span class="badge badge-warning">Pendiente</span>
                                            </template>
                                            <template v-else-if="reclamo.condicion == 2 && reclamo.estado == 'Activo'">
                                                <span class="badge badge-primary">En Proceso</span>
                                            </template>
                                            <template v-else-if="reclamo.condicion == 3 && reclamo.estado == 'Pasivo'">
                                                <span class="badge badge-success">Atendido</span>
                                            </template>
                                            <template v-else-if="reclamo.condicion == 4 && reclamo.estado == 'Pasivo'">
                                                <span class="badge badge-danger">No procedio</span>
                                            </template>
                                        </td>
                                        <td>
                                            <template v-if="reclamo.condicion == 2">
                                                <button type="button" class="btn btn-primary btn-sm" @click="abrirModal6(reclamo.id)">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                            </template>
                                        </td>
                                        <template v-if="estadoReclamo == 'Pasivo' && reclamo.condicion == 3">
                                            <td class="text-center" v-text="reclamo.boleta" ></td>
                                        </template>
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
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoReclamo)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoReclamo)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoReclamo)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </template>
                <!-- Nueva Reclamo -->
                <template v-if="listado==0">
                    <div class="card-body">
                        <!-- Formulario 1 -->
                        <div class="form-group row border">
                            <div class="col-xl-2 col-lg-6 col-md-6 col-12 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Tipo Comprobante (*)</strong></label>
                                    <select v-model="tipo_comprobante" class="form-control ml-2">
                                        <option value="">Seleccione</option>
                                        <option value="RECLAMO">RECLAMO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-12 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Número de Reclamo (*)</strong></label>
                                    <div class="d-flex justify-content-center">
                                        <div><input type="number" readonly :value="getFechaCode" class="form-control"/></div>
                                        <div><input type="text" class="form-control" v-model="num_comprobante" placeholder="000xx"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-12 mt-2 text-center">
                                <label for=""><strong>Elegir Artículos</strong></label>&nbsp;
                                <button @click="abrirModal()" class="btn btn-primary">&bull;&bull;&bull;</button>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-12 text-center">
                                <label for=""><strong>Imagen</strong></label>
                                <input type="file" :src="imagen" @change="obtenerImagen" class="form-control-file">
                            </div>
                            <div class="col-md-12 text-center">
                                <div v-show="errorReclamo" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjReclamo" :key="error" v-text="error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tabla detalle -->
                        <div class="form-group row border">
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>No°</th>
                                            <th>Opciones</th>
                                            <th>Material</th>
                                            <th>SKU</th>
                                            <th>No° Placa</th>
                                            <th>Terminado</th>
                                            <th>Espesor</th>
                                            <th>largo</th>
                                            <th>Alto</th>
                                            <th>Metros <sup>2</sup></th>
                                            <th>Contenedor</th>
                                            <th>Ubicacion</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                            <td width="10px">{{ (index + 1) }}</td>
                                            <td>
                                                <div class="form-inline">
                                                    <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                                        <i class="icon-close"></i>
                                                    </button>&nbsp;
                                                    <template>
                                                        <button type="button" @click="editArticulo(articulo)" class="btn btn-warning btn-sm" >
                                                        <i class="icon-pencil"></i>
                                                        </button> &nbsp;
                                                    </template>
                                                </div>
                                            </td>
                                            <td v-text="detalle.categoria"></td>
                                            <td v-text="detalle.articulo"></td>
                                            <td v-text="detalle.codigo"></td>
                                            <td v-text="detalle.terminado"></td>
                                            <td v-text="detalle.espesor"></td>
                                            <td v-text="detalle.largo"></td>
                                            <td v-text="detalle.alto"></td>
                                            <td v-text="detalle.metros_cuadrados"></td>
                                            <td v-text="detalle.contenedor"></td>
                                            <td v-text="detalle.ubicacion"></td>
                                            <td v-text="detalle.cantidad"></td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="13" class="text-center">
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
                                <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacion_reclamo"></textarea>
                            </div>&nbsp;
                            <div class="col-md-4 text-center d-flex justify-content-center" v-if="showElim">
                                <template v-if="imagenMinatura !='images/traslados/null'">
                                    <lightbox class="m-0" album="" :src="imagen">
                                        <figure>
                                            <img width="300" height="200" class="img-responsive img-fluid imgcenter" :src="imagen" alt="Comprobante del reclamo">
                                        </figure>
                                    </lightbox>&nbsp;
                                    <div class="col-1">
                                        <button type="button" class="btn btn-danger btn-circle float-left" aria-label="Eliminar imagen" @click="eliminarImagen(reclamo_id,imagen)">
                                            <i class="fa fa-times"></i>
                                        </button>&nbsp;
                                    </div>
                                </template>
                            </div>&nbsp;
                        </div>
                        <!-- Buttons Cerrar & Registrar -->
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                                <button v-if="editReclamo == 0" type="button" class="btn btn-primary" @click="registrarReclamo()">Registrar Reclamo</button>
                                <button v-if="editReclamo == 1" type="button" class="btn btn-primary" @click="actualizarReclamo()">Editar Reclamo</button>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- Ver Reclamo -->
                <template v-if="listado==2">
                    <div class="card-body">
                        <div class="form-group row d-flex justify-content-around">
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
                                <p> {{ formatedDate(fecha_hora) }} </p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Estado</strong></label>
                                <template v-if="estadoVn == 'Activo' && condicion == 1">
                                    <span class="badge badge-success">Activo</span>
                                </template>
                                <template v-else-if="estadoVn == 'Activo' && condicion == 2">
                                    <span class="badge badge-success">Activo</span>
                                </template>
                                <template v-else-if="estadoVn == 'Pasivo' && condicion == 3">
                                    <span class="badge badge-danger">Pasivo</span>
                                </template>
                                <template v-else-if="estadoVn == 'Pasivo' && condicion == 4">
                                    <span class="badge badge-danger">Pasivo</span>
                                </template>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Condicion</strong></label>
                                 <template v-if="condicion == 1 && estadoVn == 'Activo'">
                                    <span class="badge badge-warning">Pendiente</span>
                                </template>
                                <template v-else-if="condicion == 2 && estadoVn == 'Activo'">
                                    <span class="badge badge-primary">En Proceso</span>
                                </template>
                                <template v-else-if="condicion == 3 && estadoVn == 'Pasivo'">
                                    <span class="badge badge-success">Atendido</span>
                                </template>
                                <template v-else-if="condicion == 4 && estadoVn == 'Pasivo'">
                                    <span class="badge badge-danger">No procedio</span>
                                </template>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>No°</th>
                                            <th>Material</th>
                                            <th>SKU</th>
                                            <th>No° Placa</th>
                                            <th>Terminado</th>
                                            <th>Espesor</th>
                                            <th>largo</th>
                                            <th>Alto</th>
                                            <th>Metros <sup>2</sup></th>
                                            <th>Ubicacion</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                            <td width="10px">{{ (index + 1) }}</td>
                                            <td v-text="detalle.categoria"></td>
                                            <td v-text="detalle.articulo"></td>
                                            <td v-text="detalle.codigo"></td>
                                            <td v-text="detalle.terminado"></td>
                                            <td v-text="detalle.espesor"></td>
                                            <td v-text="detalle.largo"></td>
                                            <td v-text="detalle.alto"></td>
                                            <td v-text="detalle.metros_cuadrados"></td>
                                            <td v-text="detalle.ubicacion"></td>
                                            <td v-text="detalle.cantidad"></td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="12" class="text-center">
                                                <strong>NO hay artículos agregados...</strong>
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
                                                <button type="button" class="btn btn-primary btn-sm float-right" @click="actualizarObservacion(reclamo_id)">
                                                    <i class="fa fa-floppy-o"></i>
                                                </button>
                                            </template>&nbsp;
                                        </div>
                                    </div>
                                    <textarea class="form-control rounded-0" rows="5" cols="60" maxlength="256" :readonly="!obsEditable"
                                        v-model="observacion_reclamo" style="resize: none;"></textarea>
                                </div>
                            </div>
                            <div>
                                <div class="text-center d-flex justify-content-center" v-if="showElim">
                                    <template v-if="imagenMinatura !='images/traslados/null'">
                                        <div>
                                            <lightbox class="m-0" album="" :src="imagen">
                                                <figure class="mr-2">
                                                    <img width="300" height="200" class="img-responsive img-fluid imgcenter" :src="imagen" alt="Comprobante del reclamo">
                                                </figure>
                                            </lightbox>&nbsp;
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-danger btn-circle float-left" aria-label="Eliminar imagen" @click="eliminarImagen(reclamo_id,imagen)">
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
                <!-- Edit Articulo -->
                <template v-if="listado == 3">
                    <div class="card-body">
                        <div class="form-group row d-flex justify-content-around">
                            <div class="form-group p-2">
                                <label for=""><strong>Realizó</strong></label>
                                <p v-text="user"></p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong> Tipo Comprobante</strong></label>
                                <p v-text="tipo_comprobante"></p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Numero de Comprobante</strong></label>
                                <p v-text="num_comprobante"></p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Fecha:</strong></label>
                                <p> {{ formatedDate(fecha_hora) }} </p>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Estado</strong></label>
                                <template v-if="estadoVn == 'Activo' && condicion == 1">
                                    <span class="badge badge-success">Activo</span>
                                </template>
                                <template v-else-if="estadoVn == 'Activo' && condicion == 2">
                                    <span class="badge badge-success">Activo</span>
                                </template>
                                <template v-else-if="estadoVn == 'Pasivo' && condicion == 3">
                                    <span class="badge badge-danger">Pasivo</span>
                                </template>
                                <template v-else-if="estadoVn == 'Pasivo' && condicion == 4">
                                    <span class="badge badge-danger">Pasivo</span>
                                </template>
                            </div>
                            <div class="form-group p-2">
                                <label for=""><strong>Condicion</strong></label>
                                <template v-if="condicion == 1 && estadoVn == 'Activo'">
                                    <button type="button" class="btn btn-primary" @click="cambiarProceso(reclamo_id)">En Proceso</button>
                                </template>
                                <template >
                                    <div v-if="condicion == 2 && estadoVn == 'Activo'">
                                        <button type="button" class="btn btn-success" @click="cambiarEstado()">Atendido</button>
                                         <button type="button" class="btn btn-danger" @click="cambiarNoprocede()">No procedio</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="closeEdit()"  class="btn btn-secondary">Cerrar</button>
                                <button type="button" class="btn btn-primary" @click="actualizarArticulo()">Actualizar</button>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- Fin Edit Articulo -->
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
                        <div class="form-inline">
                            <div class="form-group col-12">
                                <div class="input-group input-group-sm col-12 col-lg-8 col-xl-8 mb-3 p-1">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Criterios</span>
                                    </div>
                                    <select class="form-control" v-model="criterioArt">
                                        <option value="sku">Código de material</option>
                                        <option value="codigo">No° de placa</option>
                                        <option value="descripcion">Descripción</option>
                                    </select>
                                    <input type="text" v-model="buscarArt" @keyup.enter="listarArticulo(1,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)" class="form-control" placeholder="Texto a buscar">
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-4 col-xl-4 mb-3 p-1">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Material</span>
                                    </div>
                                    <select class="form-control" v-model="categoriaFilt" @change="listarArticulo(1,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)">
                                        <option value="0">Seleccione un material</option>
                                        <option v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-6 col-xl-4 mb-3 p-1">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Terminado</span>
                                    </div>
                                    <input type="text" v-model="acabadoArt" @keyup.enter="listarArticulo(1,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)" class="form-control" placeholder="Terminado">
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-6 col-xl-4 mb-3 p-1">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Ubicación</span>
                                    </div>
                                    <select class="form-control" v-model="bodegaArt" @change="listarArticulo(1,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)">
                                        <template v-if="zona=='GDL'">
                                            <option value="">Todas</option>
                                            <option value="Del Musico">Del Músico</option>
                                            <option value="Escultores">Escultores</option>
                                            <option value="Sastres">Sastres</option>
                                            <option value="Mecanicos">Mecánicos</option>
                                            <option value="Tractorista">Tractorista</option>
                                        </template>
                                        <template v-else-if="zona== 'SLP'">
                                            <option value="San Luis">San Luis</option>
                                        </template>
                                        <template v-else-if="zona== 'AGS'">
                                            <option value="Aguascalientes">Aguascalientes</option>
                                        </template>
                                        <template v-else-if="zona== 'PBA'">
                                            <option value="Puebla">Puebla</option>
                                        </template>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3 p-1">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="background-color:red;color:white;"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; Area</span>
                                    </div>
                                    <select class="form-control" v-model="zona" @change="listarArticulo(1,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)">
                                        <option value="GDL">Guadalajara</option>
                                        <option value="SLP">San Luis</option>
                                        <option value="AGS">Aguascalientes</option>
                                        <option value="PBA">puebla</option>
                                    </select>
                                </div>
                                 <button type="submit" @click="listarArticulo(1,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)" class="btn btn-sm btn-primary col-1 mb-3 p-1"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm text-center table-hover">
                                <thead>
                                <tr class="text-center">
                                    <th>Opciones</th>
                                    <th>No° Placa</th>
                                    <th>Código de material</th>
                                    <th>Material</th>
                                    <th>Largo</th>
                                    <th>Alto</th>
                                    <th>Metros<sup>2</sup></th>
                                    <th>Stock</th>
                                    <th>Ubicacion</th>
                                    <th>Terminado</th>
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
                                    <td v-text="articulo.nombre_categoria"></td>
                                    <td v-text="articulo.largo"></td>
                                    <td v-text="articulo.alto"></td>
                                    <td v-text="articulo.metros_cuadrados"></td>
                                    <td v-text="articulo.stock"></td>
                                    <td v-text="articulo.ubicacion"></td>
                                    <td v-text="articulo.terminado"></td>
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
                                </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="11" class="text-center">
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
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page - 1,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumberArt" :key="page" :class="[page == isActivedArt ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(page,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="paginationart.current_page < paginationart.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page + 1,buscarArt,criterioArt,bodegaArt,acabadoArt,categoriaFilt)">Sig</a>
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
        <!--Inicio Modal Actualizar Articulos-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal2}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal2()" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <!-- Filtros Modal Articulos -->
                        <div class="form-inline">
                            <div class="form-group col-12">
                                <div class="col-12 text-center">
                                    <h3 v-text="tituloModal"></h3>
                                </div>&nbsp;
                                <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Material</span>
                                    </div>
                                    <select class="form-control" v-model="idcategoria">
                                        <option value="0" disabled>Seleccione un material</option>
                                        <option v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Código de material</span>
                                    </div>
                                    <input type="text" v-model="sku" class="form-control" placeholder="Código de material"/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">No° de placa</span>
                                    </div>
                                    <input type="text" v-model="codigo" class="form-control" placeholder="Num de placa"/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Terminado</span>
                                    </div>
                                    <select class="form-control" v-model="terminado">
                                        <option value='' disabled>Seleccione un de terminado</option>
                                        <option value="Pulido">Pulido</option>
                                        <option value="Al Corte">Al Corte</option>
                                        <option value="Leather">Leather</option>
                                        <option value="Mate">Mate</option>
                                        <option value="Seda">Seda</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3" v-if="usrol== 1">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Largo</span>
                                    </div>
                                    <input type="number" v-model="largo" min="1" class="form-control" placeholder=""/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3" v-if="usrol== 1" >
                                    <div class="input-group-append">
                                        <span class="input-group-text">Alto</span>
                                    </div>
                                    <input type="number" min="1" v-model="alto" class="form-control" placeholder=""/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Metros<sup>2</sup></span>
                                    </div>
                                    <input type="number" readonly :value="calcularMts" class="form-control"/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Correccion</span>
                                    </div>
                                    <select class="form-control" v-model="correccion">
                                        <option value="" disabled>No actualizado</option>
                                        <option value="Disminucion de Medida">Disminucion de Medida</option>
                                        <option value="Aumento de medida">Aumento de medida</option>
                                        <option value="Disminucion por Detalle">Disminucion por Detalle</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Espesor</span>
                                    </div>
                                    <input type="number" min="1" v-model="espesor" class="form-control" placeholder=""/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Bodega de descarga</span>
                                    </div>
                                    <select class="form-control" v-model="ubicacion">
                                        <option value="" disabled>Seleccione una bodega de descarga</option>
                                        <option value="Del Musico">Del Músico</option>
                                        <option value="Escultores">Escultores</option>
                                        <option value="Sastres">Sastres</option>
                                        <option value="Mecanicos">Mecánicos</option>
                                        <option value="San Luis">San Luis</option>
                                        <option value="Aguascalientes">Aguascalientes</option>
                                        <option value="Puebla">Puebla</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Contenedor</span>
                                    </div>
                                    <input type="text" v-model="contenedor" class="form-control" placeholder="Contenedor"/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Fecha de llegada</span>
                                    </div>
                                    <input type="date" v-model="fecha_llegada" class="form-control" placeholder="Fecha de llegada"/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Origen</span>
                                    </div>
                                    <input type="text" v-model="origen" class="form-control" placeholder="Origen"/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Precio m<sup>2</sup></span>
                                    </div>
                                    <input type="number" min="1" value="0" step="any" v-model="precio_venta" class="form-control" placeholder=""/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Stock</span>
                                    </div>
                                    <input type="number" min="1" v-model="stock" class="form-control" placeholder="" disabled/>
                                </div>

                                <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Imagen</span>
                                    </div>
                                    <input type="text" v-model="file" class="form-control" placeholder="ID Drive Imagen"/>
                                </div>
                                <div class="input-group input-group-sm col-12 col-lg-4 mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Descripción</span>
                                    </div>
                                    <textarea class="form-control rounded-0" style="resize: none;" rows="3" maxlength="256" v-model="descripcion"></textarea>
                                </div>

                            </div>
                        </div>
                        <hr class="d-block d-sm-block d-md-none">
                        <div class="float-right d-block d-sm-block d-md-none">
                            <button type="button" class="btn btn-danger" @click="cerrarModal2()">Cerrar</button>
                        </div>
                    </div>
                    <div class="modal-footer d-none d-sm-none d-md-block">
                        <button type="button" class="btn btn-secondary" @click="actualizarArticulo()">Actualizar</button>
                    </div>
                </div>
            <!-- /.modal-content -->
            </div>
        <!-- /.modal-dialog -->
        </div>
         <!--Fin del modal-->
        <!-- Modal crear abono -->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal6}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-md " role="document">
                <div class="modal-content content-deposit">
                    <div class="modal-body ">
                        <div class="row d-flex justify-content-around">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <h5 for=""><strong>Folio</strong></h5>
                                    <input type="text"  class="form-control" v-model="folio" placeholder="000xx">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 justify-content-center d-flex">
                                <button type="button" class="btn btn-primary mr-2" @click="guardarFolio(reclamo_id,)">Guardar</button>
                                <button type="button" class="btn btn-secondary" @click="cerrarModal6(reclamo_id)">Cancelar</button>
                            </div>
                        </div>
                    </div>
                <!--  <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal6(venta_id)">Cancelar</button>
                    </div> -->
                </div>
            <!-- /.modal-content -->
            </div>
        <!-- /.modal-dialog -->
        </div>
        <!-- Fin Modal crear abono -->
    </main>

</template>

<script>
import VueBarcode from 'vue-barcode';
import VueLightbox from 'vue-lightbox';
import moment from 'moment';
import ToggleButton from 'vue-js-toggle-button'
import { get } from 'jquery';
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton)
    export default {
        data(){
            return {
                //traslado
                reclamo_id : 0,
                user: '',
                tipo_comprobante : "Reclamo",
                num_comprobante : "",
                entregado : 0,
                cantidad : 0,
                observacion_reclamo : "",
                file_reclamo : '',
                arrayReclamo : [],
                arrayDetalle : [],
                arrayArticuloT : [],
                estadoVn : "",
                autoing : 0,
                fecha_hora : "",
                folio: "",
                obsEditable : 0,
                condicion: 1,
                //traslado back
                CodeDate : "",
                errorReclamo: 0,
                errorMostrarMsjReclamo: [],

                //traslado front
                pagination : {
                    'total'        : 0,
                    'current_page' : 0,
                    'per_page'     : 0,
                    'last_page'    : 0,
                    'from'         : 0,
                    'to'           : 0,
                },
                offset : 3,
                criterio : 'num_comprobante',
                buscar : '',
                estadoReclamo : "",
                btnEntrega : false,
                sigNum : 0,
                listado : 1,
                //Articulos Info
                tituloModal: "",
                modal: 0,
                modal2: 0,
                modal6: 0,
                articulo: 0,
                articulo_id: 0,
                id_articulo : 0,
                idcategoria :0,
                codigo: "",
                sku : "",
                terminado : '',
                largo : 0,
                alto : 0,
                metros_cuadrados : 0,
                espesor : 2,
                precio_venta : 0,
                ubicacion : '',
                origen : '',
                contenedor: '',
                fecha_llegada : '',
                file : '',
                estado : 0,
                file_art : '',
                imagenMinatura : '',
                arrayCategoria : [],
                condicion : 0,
                stock : 0,
                descripcion : "",
                observacion_art : "",
                arrayArticulo : [],
                errorArticulo: 0,
                errorMostrarMsjArticulo: [],
                paginationart : {
                    'total'        : 0,
                    'current_page' : 0,
                    'per_page'     : 0,
                    'last_page'    : 0,
                    'from'         : 0,
                    'to'           : 0,
                },
                buscarArt : '',
                criterioArt : 'sku',
                bodegaArt : "",
                acabadoArt : "",
                categoriaFilt : 0,
                zona : "GDL",
                areaUs : "",
                showElim : false,
                btnNewTask : 1,
                usrol : 0,
                editReclamo : 0,
                correccion: '',
                buscador:'',
                timeout:0,
                setTimeoutBuscador:''


            };
        },
        components: {
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
            calcularMts : function(){
                let me=this;
                let resultado = 0;
                resultado = resultado + (me.alto * me.largo);
                me.metros_cuadrados = resultado;
                return resultado;
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
            imagen(){
                return this.imagenMinatura;
            }
        },
        methods: {
            listarReclamo(page,buscar,criterio,estadoReclamo){
                let me=this;
                me.btnNewTask = 1;
                var url= '/reclamo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='+ estadoReclamo;
                axios.get(url,{
                    params:{
                        buscador: this.buscador,
                    }
                }).then(function (response){
                    var respuesta= response.data;
                    me.arrayReclamo = respuesta.reclamos.data;
                    me.pagination= respuesta.pagination;
                    me.usrol = respuesta.userrol;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            buscarBodega(){

                clearTimeout( this.setTimeoutBuscador)
                this.setTimeoutBuscador = setTimeout(this.listarReclamo(1,'','num_comprobante',''), 360);
            },
            cambiarPagina(page,buscar,criterio,estadoReclamo){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarReclamo(page,buscar,criterio,estadoReclamo);
            },
            convertDateReclamo(date){
                moment.locale('es');
                let me=this;
                var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
                /* console.log(datec); */
                return datec;
            },
            getLastNum(){
                let me=this;
                var url= '/reclamo/nextNum';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.sigNum = respuesta.SigNum;
                    /* console.log("Next Num = " + me.sigNum); */
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectCategoria(){
                let me=this;
                var url= '/categoria/selectCategoria';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayCategoria = respuesta.categorias;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            mostrarDetalle(){
                this.listado = 0;
                this.getLastNum();
                this.idarticulo = 0;
                this.idcategoria = 0;
                this.codigo = "";
                this.sku = "";
                this.tipo_comprobante = "RECLAMO"
                this.idcategoria = 0;
                this.terminado = "";
                this.largo = 0;
                this.alto = 0;
                this.metros_cuadrados = 0;
                this.ubicacion = "";
                this.stock = 0;
                this.descripcion = "";
                this.arrayDetalle = [];
                this.imagenMinatura = 'images/traslados/null';
                this.getLastNum();
                this.selectCategoria();
                this.editReclamo = 0;
            },
            ocultarDetalle(){
                this.listado = 1;
                this.getLastNum();
                this.idarticulo = 0;
                this.idcategoria = 0;
                this.codigo = "";
                this.sku = "";
                this.idcategoria = 0;
                this.terminado = "";
                this.largo = 0;
                this.alto = 0;
                this.metros_cuadrados = 0;
                this.ubicacion = "";
                this.stock = 0;
                this.descripcion = "";
                this.arrayDetalle = [];
                this.num_comprobante = "";
                this.reclamo_id = 0;
                this.user= '';
                this.tipo_comprobante = "RECLAMO";
                this.cantidad = 0;
                this.observacion_reclamo = "";
                this.file_reclamo = '';
                this.arrayDetalle = [];
                this.obsEditable = 0;
                this.imagenMinatura = "";
                this.listarReclamo(1,'','num_comprobante','');
                this.btnNewTask = 1;
                this.btnEntrega = false;
            },
            obtenerImagen(e){
                let img = e.target.files[0];
                /*  console.log(img); */
                this.file_reclamo = img;
                this.cargarImagen(img);
            },
            cargarImagen(img){
                let reader = new FileReader();
                reader.onload = (e) => {
                    this.imagenMinatura = e.target.result;
                    this.file_reclamo =  e.target.result;
                }
                reader.readAsDataURL(img);
            },
            cerrarModal() {
                this.modal = 0;
                this.buscarArt = "";
                this.bodegaArt = "";
                this.acabadoArt = "";
                this.errorTraslado = 0;
                this.errorMostrarMsjTraslado = [];
            },
            abrirModal() {
                this.arrayArticulo=[];
                this.modal = 1;
                this.tituloModal = "Seleccionar Artículos";
                this.listarArticulo(1,'','sku','','');
                this.selectCategoria();
                this.listarArticulo(1,this.buscarArt,this.criterioArt,this.bodegaArt,this.acabadoArt,this.categoriaFilt);
            },
            editArticulo(data = []){

                this.selectCategoria();
                var tarticulo = this.articulo_id;
                tarticulo = data['id'];

                this.listado = 3;
                this.showNew = false;
                this.tituloModal = `Actualizar Artículo ${ data['sku'] } - ${ data['codigo'] }`;
                this.idcategoria = data['idcategoria'];
                this.codigo = data['codigo'];
                this.sku = data['sku'];
                this.terminado = data['terminado'];
                this.largo = data['largo'];
                this.alto = data['alto'];
                this.metros_cuadrados = data['metros_cuadrados'];
                this.espesor = data['espesor'];
                this.precio_venta = data['precio_venta'];
                this.ubicacion = data['ubicacion'];
                this.stock = data['stock'];
                this.descripcion= data['descripcion'];
                this.observacion = data['observacion'];
                this.origen = data['origen'];
                this.contenedor = data['contenedor'];
                this.fecha_llegada = data['fecha_llegada'];
                this.estado = data['condicion'];
                this.comprometido = data['comprometido'];
                this.usuario = data['usuario'];
                this.correccion = data['correccion'];
                this.isEdition = true;
                this.file = data['file'];
                this.autoing = data['autoing'];
                if(this.comprometido == 1){
                    this.btnComprometido = true;
                }else{
                    this.btnComprometido = false;
                }

            },
            closeEdit(){
                this.showNew = true;
                this.listado = 1;
                this.tituloModal = "";
                this.idcategoria = 0;
                this.codigo = '';
                this.sku = '';
                this.terminado = '';
                this.largo = 0;
                this.alto = 0;
                this.metros_cuadrados = 0;
                this.espesor = 0;
                this.precio_venta  = 0;
                this.ubicacion = '';
                this.stock = 1;
                this.descripcion= '';
                this.observacion = '';
                this.origen = '';
                this.contenedor  = '';
                this.fecha_llegada = '';
                this.file = '';
                this.errorArticulo = 0;
                this.btnComprometido = '';
                this.comprometido = 0;
                this.usuario = '';
                this.isEdition = false;
                this.file = "";
                this.showElim = false;
                this.tituloModal = "";
                this.mostrarDetalle();

            },
            actualizarArticulo() {

            var page = this.pagination.current_page;
            var crit =  this.criterio;
            var busc = this.buscar;
            var bod = this.bodega;
            var aca = this.acabado;
            let art = this.sku;
            let cdn = this.codigo;

            if (this.validarArticulo()) {
                return;
            }

            let me = this;
            axios.put("/articulo/actualizar", {
                'idcategoria': this.idcategoria,
                'codigo': this.codigo,
                'sku' : this.sku,
                'terminado' : this.terminado,
                'largo' : this.largo,
                'alto' : this.alto,
                'metros_cuadrados' : this.metros_cuadrados,
                'espesor' : this.espesor,
                'precio_venta' : this.precio_venta,
                'ubicacion' : this.ubicacion,
                'stock': this.stock,
                'descripcion': this.descripcion,
                'observacion' : this.observacion,
                'origen' : this.origen,
                'contenedor' : this.contenedor,
                'fecha_llegada' : this.fecha_llegada,
                'file' : this.file,
                'id': this.articulo_id,
                'correccion' : this.correccion,
            })
            .then(function(response) {
                Swal.fire(
                    "Actualizado!",
                    `El articulo ${ art } - ${ cdn } ha sido actualizado con éxito`,
                    "success"
                )
                me.closeEdit();
                me.listarArticulo(page,busc,crit,bod,aca,1,0);
            })
            .catch(function(error) {
                console.log(error);
            });
            },
            selectCategoria(){
                let me=this;
                var url= '/categoria/selectCategoria';

                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayCategoria = respuesta.categorias;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarArticulo(page,buscar,criterio,bodega,acabado,idcategoria){
                let me=this;
                if(me.zona == 'SLP'){
                    bodega = 'San Luis';
                    me.bodegaArt = 'San Luis';
                }else{
                    if(bodega == 'San Luis'){
                        bodega = "";
                        me.bodegaArt = "";
                    }
                }
                if(me.zona == 'AGS'){
                    bodega = 'Aguascalientes';
                    me.bodegaArt = 'Aguascalientes';
                }else{
                    if(bodega == 'Aguascalientes'){
                        bodega = "";
                        me.bodegaArt = "";
                    }
                }
                if(me.zona == 'PBA'){
                    bodega = 'Puebla';
                    me.bodegaArt = 'Puebla';
                }else{
                    if(bodega == 'Puebla'){
                        bodega = "";
                        me.bodegaArt = "";
                    }
                }
                var url= '/articulo/listarArticuloVenta?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio
                + '&bodega=' + bodega + '&acabado=' + acabado + '&idcategoria=' + idcategoria;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayArticulo = respuesta.articulos.data;
                    me.paginationart= respuesta.pagination;
                    me.areaUs = respuesta.userarea;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPaginaArt(page,buscar,criterio,bodega,acabado,idcategoria){
                let me = this;
                //Actualiza la página actual
                me.paginationart.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarArticulo(page,buscar,criterio,bodega,acabado,idcategoria);
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
            agregarDetalleModal(data =[]){
                let me=this;

                if(me.encuentra(data['id'])){
                    Swal.fire({
                        type: 'error',
                        title: 'Lo siento...',
                        text: 'Esta de placa ya esta en el listado!!',
                    })
                }
                else{
                    me.arrayDetalle.push({
                        idarticulo       : data['id'],
                        articulo         : data['sku'],
                        codigo           : data['codigo'],
                        idcategoria      : data['idcategoria'],
                        categoria        : data['nombre_categoria'],
                        largo            : data['largo'],
                        alto             : data['alto'],
                        metros_cuadrados : data['metros_cuadrados'],
                        terminado        : data['terminado'],
                        espesor          : data['espesor'],
                        precio           : data['precio_venta'],
                        stock            : data['stock'],
                        ubicacion        : data['ubicacion'],
                        categoria        : data['nombre_categoria'],
                        origen           : data['origen'],
                        contenedor       : data['contenedor'],
                        file             : data['file'],
                        descripcion      : data['descripcion'],
                        observacion      : data['observacion'],
                        fecha_llegada    : data['fecha_llegada'],
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
            eliminarDetalle(index){
                let me = this;
                me.arrayDetalle.splice(index,1);
            },
            validarReclamo() {
                let me = this;
                var art;

                me.errorReclamo = 0;
                me.errorMostrarMsjReclamo = [];

                me.arrayDetalle.map(function(x){
                    if(x.cantidad > x.stock){
                        art ="La cantidad del articulo " + x.codigo + " supera las cantidades disponibles.";
                        me.errorMostrarMsjReclamo.push(art);
                    }
                });

                if (!me.num_comprobante) me.errorMostrarMsjReclamo.push("Ingrese el numero de comprobante");
                if (me.arrayDetalle.length<=0) me.errorMostrarMsjReclamo.push("Introdusca articulos para el Reclamo");

                if (me.errorMostrarMsjReclamo.length) me.errorReclamo = 1;

                return me.errorReclamo;
            },
            registrarReclamo(){

                if (this.validarReclamo()) {
                    return;
                }

                let me = this;

                var numcomp = "RC-".concat(me.CodeDate,"-",me.num_comprobante);

                axios.post('/reclamo/registrar',{
                    'tipo_comprobante': this.tipo_comprobante,
                    'num_comprobante' : numcomp,
                    'estado'          : this.estado,
                    'condicion'       : this.condicion,
                    'autoing'        : autoing,
                    'observacion'     : this.observacion_reclamo,
                    'file'            : this.file_reclamo,
                    'data': this.arrayDetalle
                }).then(function(response) {
                    me.ocultarDetalle();
                    me.listarReclamo(1,'','num_comprobante','');
                    me.tipo_comprobante = "RECLAMO";
                    me.num_comprobante = 0;
                    me.idarticulo = 0;
                    me.articulo = "";
                    me.cantidad = 0;
                    me.precio = 0;
                    me.stock = 0;
                    me.observacion = "";
                    me.file_reclamo = "";
                    me.arrayDetalle = [];
                    me.editReclamo = 0;
                    Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'Se ha registrado el ingreso con éxito!',
                });
                })
                .catch(function(error) {
                    console.log(error);
                });
            },
            verReclamo(id){
                let me = this;
                me.listado = 2;
                me.btnNewTask = 0;
                //Obtener los datos del ingreso
                var arrayReclamoT=[];
                var url= '/reclamo/obtenerCabecera?id=' + id;

                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    arrayReclamoT = respuesta.reclamo;



                    var fechaform  = arrayReclamoT[0]['fecha_hora'];

                    var total_parcial = 0;

                    me.reclamo_id = arrayReclamoT[0]['id'];
                    me.tipo_comprobante=arrayReclamoT[0]['tipo_comprobante'];
                    me.num_comprobante=arrayReclamoT[0]['num_comprobante'];
                    me.user=arrayReclamoT[0]['usuario'];
                    me.observacion_Reclamo = arrayReclamoT[0]['obsreclamo'];
                    me.estadoVn = arrayReclamoT[0]['estado'];
                    me.fecha_hora = arrayReclamoT[0]['fecha_hora'];
                    me.condicion = arrayReclamoT[0]['condicion'];

                    let hasImg = '/images/traslados/' + arrayReclamoT[0]['file'];

                    if(hasImg != '/images/traslados/null'){
                        me.imagenMinatura = '/images/traslados/'+ arrayReclamoT[0]['file'];
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
                var url= '/reclamo/obtenerDetalles?id=' + id;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayDetalle = respuesta.detalles;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            formatedDate(date){
                moment.locale('es');
                let me=this;
                var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
                /* console.log(datec); */
                return datec;
            },
            editObservacion(){
                let me = this;
                me.obsEditable = 1;
            },
            actualizarObservacion(id){
                let me = this;
                axios.post('/reclamo/actualizarObservacion',{
                    'id': id,
                    'observacion' : this.observacion_reclamo
                }).then(function (response) {
                    me.obsEditable = 0;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            updImage(){
                let me = this;
                axios.put('/reclamo/updImagen',{
                    'file': this.file_reclamo,
                    'id' : this.reclamo_id
                }).then(function(response) {

                    /* me.ocultarDetalle(); */
                    /* me.listarReclamo(1,'','num_comprobante'); */
                    Swal.fire({
                        type: 'success',
                        title: 'Completado...',
                        text: 'La imagen ha sido actualizada!',
                    })
                    verReclamo(id);
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
                        axios.put('/reclamo/eliminarImg', {
                            'id' : id
                        }).then(function(response) {
                            /* me.listarVenta(1,'','num_comprobante'); */
                            me.imagenMinatura = '/images/traslados/null';
                            swalWithBootstrapButtons.fire(
                                "Elimada!",
                                "La imagen ha sido eliminada con éxito.",
                                "success"
                            )
                            verReclamo(id);
                        }).catch(function(error) {
                            console.log(error);
                        });
                    }else if (result.dismiss === swal.DismissReason.cancel){
                    }
                })
            },
            cambiarEstadoEntrega(id){
                let me = this;
                if(me.btnEntrega == true){
                    me.entregado = 1;
                }else{
                    me.entregado = 0;
                }
                axios.post('/reclamo/cambiarEntrega',{
                    'id': id,
                    'entregado' : this.entregado
                }).then(function (response) {
                    me.verReclamo(id);
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
            anularReclamo(id) {
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
                        axios.put('/reclamo/desactivar',{
                            'id': id
                        }).then(function (response) {
                            Swal.fire({
                                type: 'success',
                                title: 'Completado...',
                                text: 'El traslado ha sido anulado con exito!',
                            })
                            me.listarReclamo(1,'','num_comprobante','');

                        }).catch(function (error) {
                            console.log(error);
                        });
                    }else if (result.dismiss === swal.DismissReason.cancel){
                    }
                })
            },
            pdfReclamo(id){
                window.open('/reclamo/pdf/'+id);
            },
            excelReclamo(id,num_comp){
                 window.open('/reclamo/excel/'+ id+'?num_traslado=' + num_comp);
            },
            editarReclamo(id){

                let me = this;
                me.listado = 3;
                me.editReclamo = 1;
                var arrayReclamoT=[];
                var url= '/reclamo/obtenerCabecera?id=' + id;

                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    arrayReclamoT = respuesta.reclamo;



                    var fechaform  = arrayReclamoT[0]['fecha_hora'];

                    var total_parcial = 0;

                    me.reclamo_id = arrayReclamoT[0]['id'];
                    me.tipo_comprobante=arrayReclamoT[0]['tipo_comprobante'];
                    me.num_comprobante=arrayReclamoT[0]['num_comprobante'];
                    me.user=arrayReclamoT[0]['usuario'];
                    me.observacion_reclamo = arrayReclamoT[0]['obsreclamo'];
                    me.estadoVn = arrayReclamoT[0]['estado'];
                    me.fecha_hora = arrayReclamoT[0]['fecha_hora'];
                    me.condicion = arrayReclamoT[0]['condicion'];


                    if (arrayReclamoT[0]['file'] !== null) {
                        me.imagenMinatura = '/images/traslados/'+ arrayReclamoT[0]['file'];
                        me.showElim = true;
                    }


                })
                .catch(function (error) {
                    console.log(error);
                });

                //Obtener los detalles del reclamo
                var url= '/reclamo/obtenerDetalles?id=' + id;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayDetalle = respuesta.detalles;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            actualizarReclamo(){
                if (this.validarReclamo()) {
                    return;
                }

                let me = this;

                let compro = this.num_comprobante;

                axios.post('/reclamo/actualizar',{
                    'id'              : this.reclamo_id,
                    'tipo_comprobante': this.tipo_comprobante,
                    'num_comprobante' : this.num_comprobante,
                    'estado'          : this.estado,
                    'condicion'       : this.condicion,
                    'observacion'     : this.observacion_reclamo,
                    'file'            : this.file_reclamo,
                    'data': this.arrayDetalle
                }).then(function(response) {
                    Swal.fire({
                        type: 'success',
                        title: 'Completado...',
                        text: `El Reclamo ${compro} ha sido actualizado con éxito!!`,
                    });
                    me.ocultarDetalle();
                    me.listarReclamo(1,'','num_comprobante','');
                    me.tipo_comprobante = "RECLAMO";
                    me.num_comprobante = 0;
                    me.idarticulo = 0;
                    me.articulo = "";
                    me.cantidad = 0;
                    me.precio = 0;
                    me.stock = 0;
                    me.observacion = "";
                    me.file_reclamo = "";
                    me.arrayDetalle = [];
                    me.editReclamo = 0;


                })
                .catch(function(error) {
                    console.log(error);
                });
            },
            cambiarProceso(id) {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: "¿Esta seguro de cambiar este Reclamo? Este proceso es irreversible!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Aceptar!",
                    cancelButtonText: "Cancelar!",
                    reverseButtons: true
                })
                .then(result => {
                    if (result.value) {
                        let me = this;
                        var obs = "Reclamo en Proceso";
                        axios.put('/reclamo/updProceso',{
                            'id': id,
                            'observacion' : obs,
                        }).then(function (response) {
                            Swal.fire({
                                type: 'success',
                                title: 'Completado...',
                                text: 'El Reclamo ha sido marcado como en Proceso con exito!',
                            })
                            me.closeEdit();
                            me.listarReclamo(1,'','num_comprobante','');

                        }).catch(function (error) {
                            console.log(error);
                        });
                    }else if (result.dismiss === swal.DismissReason.cancel){
                    }
                })
            },
            cambiarEstado(){
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: "¿Esta seguro de cambiar este Reclamo? Este proceso es irreversible!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Aceptar!",
                    cancelButtonText: "Cancelar!",
                    reverseButtons: true
                })
                .then(result => {
                    if (result.value) {
                        let me = this;
                        var obs = "El Reclamo esta Atendido al 100%";
                        axios.put('/reclamo/atendido',{
                            'id': id,
                            'observacion' : obs,
                        }).then(function (response) {
                            Swal.fire({
                                type: 'success',
                                title: 'Completado...',
                                text: 'El Reclamo ha sido marcado como en Proceso con exito!',
                            })
                            me.closeEdit();
                            me.listarReclamo(1,'','num_comprobante','');

                        }).catch(function (error) {
                            console.log(error);
                        });
                    }else if (result.dismiss === swal.DismissReason.cancel){
                    }
                })
            },
            cambiarNoprocede(){
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: "¿Esta seguro de cambiar este Reclamo? Este proceso es irreversible!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Aceptar!",
                    cancelButtonText: "Cancelar!",
                    reverseButtons: true
                })
                .then(result => {
                    if (result.value) {
                        let me = this;
                        var obs = "El Reclamo No Procedio";
                        axios.put('/reclamo/noProcedio',{
                            'id': id,
                            'observacion' : obs,
                        }).then(function (response) {
                            Swal.fire({
                                type: 'success',
                                title: 'Completado...',
                                text: 'El Reclamo ha sido marcado como en Proceso con exito!',
                            })
                            me.closeEdit();
                            me.listarReclamo(1,'','num_comprobante','');

                        }).catch(function (error) {
                            console.log(error);
                        });
                    }else if (result.dismiss === swal.DismissReason.cancel){
                    }
                })
            },
            abrirModal6(){
                let me = this;

                me.modal6 = 1
                me.folio = ""
            },
            cerrarModal6(){
                this.modal6 = 0

                this.listarReclamo(1,'','sku','','');
            },
            guardarFolio(){
                let me = this;

                var numcopm = "F-".concat(me.CodeDate,"-",me.folio);

                axios.put('/reclamo/storeFolio',{
                    'id' : me.id,
                    'condicion': me.condicion,
                    'estado': me.estado,
                    'folio' : numcopm,

                }).then(function(response) {
                    Swal.fire({
                        type: 'succes',
                        title: 'Completado...',
                        text: 'Se añadio el Folio exitosamente!',
                    });
                    cerrarModal6();
                    listarReclamo(1,'','sku','','');
                })
                .catch(function(error){
                    console.log(error);
                    Swal.fire({
                        type: 'error',
                        title: 'Error...',
                        text: 'Ocurrio un error!',
                    });
                });
            },


        },
        mounted() {
            this.listarReclamo(1,this.buscar, this.criterio,this.estadoReclamo);
            this.getLastNum();
        }
    }
</script>
