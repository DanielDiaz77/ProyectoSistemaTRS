<template>
  <main class="main">

    <!-- Breadcrumb -->
    <ol class="breadcrumb  mb-0">
      <li class="breadcrumb-item"><a href="/">Escritorio</a> </li>
    </ol>
    <div class="container-fluid p-1">
      <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header p-2">
                  <i class="fa fa-align-justify"></i> Artículos Editados
               <!--  <button type="button" @click="abrirModal('articulo','registrar')" class="btn btn-secondary" v-if="showNew">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button> -->
                <template v-if="listado == 2">
                    <button type="button" @click="AskcortarPlaca()" class="btn btn-warning btn-sm float-right" v-if="stock >= 1 && usrol == 1 ">
                        <i class="icon-crop"></i>&nbsp;Cortar
                    </button>
                </template>
                <template v-else-if="listado == 1">
                    <div class="form-inline ml-5 float-right">

                        <div class="form-group">
                            <div class="input-group input-group-sm mt-1 mt-sm-0 ml-md-2 ml-lg-5">
                                <button @click="abrirModal3()" class="btn btn-outline-success btn-sm">Reporte <i class="fa fa-file-excel-o"></i></button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <!-- Listado -->
            <template v-if="listado == 1">
                <div class="card-body">
                    <div class="form-inline">
                        <div class="form-group col-12">
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-6 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Criterios</span>
                                </div>
                                <select class="form-control" v-model="criterio">
                                    <option value="sku">Código de material</option>
                                    <option value="codigo">No° de placa</option>
                                    <option value="contenedor">No° de contenedor</option>
                                </select>
                                <input type="text" v-model="buscar" @keyup.enter="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)" class="form-control" placeholder="Texto a buscar">
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Material</span>
                                </div>
                                <select class="form-control" v-model="categoriaFilt" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)">
                                    <option value="0">Seleccione un material</option>
                                    <option v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                </select>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Terminado</span>
                                </div>
                                <input type="text" v-model="acabado" @keyup.enter="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)" class="form-control" placeholder="Terminado">
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Ubicación</span>
                                </div>
                                <select class="form-control" v-model="bodega" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)">
                                    <template v-if="zona=='GDL'">
                                        <option value="">Todas</option>
                                        <option value="Del Musico">Del Músico</option>
                                        <option value="Escultores">Escultores</option>
                                        <option value="Sastres">Sastres</option>
                                        <option value="Mecanicos">Mecánicos</option>
                                    </template>
                                    <template v-else-if="zona== 'SLP'">
                                        <option value="San Luis">San Luis</option>
                                    </template>
                                    <template v-else-if="zona == 'AGS'">
                                        <option value="Aguascalientes">Aguascalientes</option>
                                    </template>
                                    <template v-else-if="zona == 'PBA'">
                                        <option value="Puebla">Puebla</option>
                                    </template>
                                </select>
                                <!-- <button type="submit" @click="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)" class="btn btn-sm btn-primary"><i class="fa fa-search"></i>Buscar</button> -->
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Usuarios</span>
                                </div>
                                <select class="form-control" v-model="buscador" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)">
                                    <template v-if="zona=='GDL'">
                                        <option value="1">Sistemas</option>
                                        <option value="7">ClaudiaRojas</option>
                                        <option value="331">Recepcion</option>
                                        <option value="328">AlmaceneroAuxiliar</option>
                                        <option value="2060">atencionacliente</option>
                                        <option value="4168">Atencionacliente02</option>
                                        <option value="4326">Atencionacliente03</option>
                                        <option value="4672">Atencionacliente04</option>
                                        <option value="416">AuxiliarVentas</option>
                                        <option value="1545">AuxiliarVentas02</option>
                                    </template>
                                    <template v-else-if="zona== 'SLP'">
                                        <option value="283">VentasSanLuis</option>
                                    </template>
                                    <template v-else-if="zona == 'AGS'">
                                        <option value="4929">VentasAguascalientes</option>
                                    </template>
                                </select>
                                <!-- <button type="submit" @click="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)" class="btn btn-sm btn-primary"><i class="fa fa-search"></i>Buscar</button> -->
                            </div>

                            <!--<div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Usuarios</span>
                                </div>
                                <input type="text" v-model="buscador" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)" class="form-control" placeholder="Texto a buscar">
                            </div>-->
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                               <!--  <button class="btn btn-sm btn-danger" type="button"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; Area</button> -->
                               <div class="input-group-append">
                                    <span class="input-group-text" style="background-color:red;color:white;"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; Area</span>
                                </div>
                                <select class="form-control" v-model="zona" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)">
                                    <option value="GDL">Guadalajara</option>
                                    <option value="SLP">San Luis</option>
                                    <option value="AGS">Aguascalientes</option>
                                    <option value="PBA">Puebla</option>
                                </select>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3" v-if="estadoArt==1">
                                <div class="mb-2 mb-md-2 mb-lg-1 mb-xl-0 mt-1 mb-md-1 mb-lg-0 mb-xl-0">
                                    <button type="submit" @click="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)" class="btn btn-sm btn-primary mr-2"><i class="fa fa-search"></i>Buscar</button>
                                    <button @click="listarExcelFiltros(criterio,buscar,acabado,bodega,zona)" class="btn btn-success btn-sm">Exportar Consulta <i class="fa fa-file-excel-o"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive col-md-12" >
                        <table class="table table-bordered table-striped table-sm text-center table-hover" >
                            <thead>
                            <tr class="text-center">
                                <th>Opciones</th>
                                <th>Usuario</th>
                                <th>Actualizado</th>
                                <th>No. Placa</th>
                                <th>Código de Material</th>
                                <th>Material</th>
                                <th>Largo</th>
                                <th>Alto</th>
                                <th>Metros<sup>2</sup></th>
                                <th>Terminado</th>
                                <th>Contenedor</th>
                                <th>Bodega de descarga</th>
                                <th>Estado</th>
                                <th>Stock</th>
                                <th>Correccion</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayArticulo.length">
                                <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                    <td >
                                        <div class="form-inline" >
                                            <template v-if="articulo.condicion == 1 && articulo.stock > 0">
                                                <button type="button" @click="editArticulo(articulo)" class="btn btn-warning btn-sm" v-if="usrol == 1">
                                                    <i class="icon-pencil"></i>
                                                </button> &nbsp;
                                            </template>
                                            <button type="button" @click="verArticulo(articulo)" class="btn btn-success btn-sm">
                                                <i class="icon-eye"></i>
                                            </button> &nbsp;
                                            <template v-if="articulo.file">
                                                <lightbox class="m-0" album="" :src="'https://drive.google.com/uc?id='+articulo.file">
                                                    <button type="button" class="btn btn-outline-success btn-sm">
                                                        <i class="fa fa-picture-o"></i>
                                                    </button>&nbsp;
                                                </lightbox>
                                            </template>
                                            <template v-if="articulo.condicion == 1 && articulo.salida == 1">
                                                <button type="button" class="btn btn-outline-dark">
                                                     <i class="fa fa-star" aria-hidden="true"></i>
                                                </button> &nbsp;
                                            </template>
                                            <template v-if="articulo.condicion == 1 && articulo.relice == 1">
                                                <button type="button"  class="btn btn-primary btn-sm">
                                                    <i class="fa-solid fa-circle-p"><strong>R</strong></i>
                                                </button> &nbsp;
                                            </template>
                                            <template v-if="articulo.condicion == 1 && articulo.detalle == 1">
                                                <button type="button" class="btn btn-info btn-sm" >
                                                    <i class="fa-solid fa-circle-p"><strong>Det</strong></i>
                                                </button> &nbsp;
                                            </template>
                                            <template v-if="articulo.condicion == 1 && articulo.pulido == 1">
                                                <button type="button"  class="btn btn-warning btn-sm">
                                                    <i class="fa-solid fa-circle-p"><strong>MalPu</strong></i>
                                                </button> &nbsp;
                                            </template>
                                        </div>
                                    </td>
                                    <td v-text="articulo.usuario" ></td>
                                    <td v-text="articulo.modificado"> </td>
                                    <td  v-text="articulo.codigo"></td>
                                    <td v-text="articulo.sku"></td>
                                    <td v-text="articulo.nombre_categoria"></td>
                                    <td v-text="articulo.largo"></td>
                                    <td v-text="articulo.alto"></td>
                                    <td v-text="articulo.metros_cuadrados"></td>
                                    <td v-text="articulo.terminado"></td>
                                    <td v-text="articulo.contenedor"></td>
                                    <td v-text="articulo.ubicacion"></td>
                                    <td>
                                        <div v-if="articulo.condicion == 1">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else-if="articulo.condicion == 3">
                                            <span class="badge badge-warning">Cortado</span>
                                        </div>
                                        <div v-else-if="articulo.condicion == 4">
                                            <span style="color:#fff" class="badge badge-info">En traslado</span>
                                        </div>
                                        <div v-else-if="articulo.condicion == 5">
                                            <span style="color:#fff" class="badge badge-secondary">Por ingresar</span>
                                        </div>
                                        <div v-else-if="articulo.condicion == 6">
                                            <span class="badge badge-dark">Oculto</span>
                                        </div>
                                        <div v-else-if="articulo.condicion == 0">
                                            <span style="color:#fff" class="badge badge-danger">Desactivado</span>
                                        </div>
                                    </td>
                                    <td v-text="articulo.stock"></td>
                                    <td>
                                        <div v-if="articulo.correccion==''">
                                            <span class="badge badge-info">No Actualizado</span>
                                        </div>
                                        <div v-else-if="articulo.correccion=='Disminucion de Medida'">
                                            <span class="badge badge-warning">Disminucion de Medida</span>
                                        </div>
                                        <div v-else-if="articulo.correccion=='Aumento de medida'">
                                             <span class="badge badge-success">Aumento de Medida</span>
                                        </div>
                                        <div v-else-if="articulo.correccion=='Disminucion por Detalle'">
                                            <span class="badge badge-danger">Disminucion por Detalle</span>
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
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,bodega,acabado,estadoArt,categoriaFilt)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,bodega,acabado,estadoArt,categoriaFilt)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,bodega,acabado,estadoArt,categoriaFilt)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </template>
            <!-- Fin Listado -->
            <!-- Edit Articulo -->
            <template v-if="listado == 0">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-12 text-center">
                           <h3 v-text="tituloModal"></h3>
                        </div>&nbsp;
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <div v-if="isEdition && estado">
                            <label for=""><strong>Comprometido: </strong></label>
                            <toggle-button @change="cambiarComprometido(articulo_id)" v-model="btnComprometido" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                        </div>&nbsp;
                        <div v-if="usuario" class="mt-0 mt-md-1">
                            <label for=""><strong>Actualizo: </strong> {{ usuario }} </label>
                            <!-- <p v-text="usuario"></p> -->
                        </div>&nbsp;
                    </div>
                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="row">
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
                            <div class="input-group input-group-sm col-12 col-lg-4 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Observaciones</span>
                                </div>
                                <textarea class="form-control rounded-0" style="resize: none;" rows="3" maxlength="256" v-model="observacion"></textarea>
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <barcode :value="codigo" :options="{format: 'EAN-13'}">
                                    Generando código de barras.
                                </barcode>
                            </div>
                            <div class="col-12 text-center" v-if="detalle == 0 ">
                                <h3 >Inspeccion de Material</h3>{{codigo}}
                            </div>&nbsp;
                            <div class="col-12 text-center" v-if="estado">
                                <template v-if="relice == 0">
                                    <button type="button"  @click="cambiarEstadoMaterial(articulo_id)"  class="btn btn-outline-primary">
                                        <i class="fa-solid fa-circle-p"><strong>Relice</strong></i>
                                    </button> &nbsp;
                                </template>
                                <template v-if="detalle == 0">
                                    <button type="button" @click="cambiarDetalle(articulo_id)" class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-circle-p"><strong>Detalle</strong></i>
                                    </button> &nbsp;
                                </template>
                                <template v-if="pulido == 0">
                                    <button type="button"  @click="cambiarPulido(articulo_id)" class="btn btn-outline-info">
                                        <i class="fa-solid fa-circle-p"><strong>Mal Pulido</strong></i>
                                    </button> &nbsp;
                                </template>

                            </div>
                        </div>
                        <div v-show="errorArticulo" class="form-group row div-error">
                            <div class="text-center text-error">
                            <div v-for="error in errorMostrarMsjArticulo" :key="error" v-text="error"></div>
                            </div>
                        </div>
                        <div class="form-group row" v-if="arrayImagenes.length">
                            <div class="col-12 d-flex justify-content-start b-b-1">
                                <div><h3>Imagenes Actuales</h3></div>
                            </div>
                            <div class="col-12 col-md-12 mt-2">
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
                                                    <button class="btn btn-danger btn-sm btnElimImg" type="button" @click="eliminarImgLink(img.id,articulo_id)">Eliminar</button>
                                                    <div class="form-group form-check" v-if="img.url">
                                                        <input type="checkbox" class="form-check-input" :id="'chkPrt'+img.id" :checked="img.direction==file"
                                                            @change="selectCover(img.direction)">
                                                        <label class="form-check-label" :for="'chkPrt'+img.direction">Portada</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                    <div class="input-group input-group-sm col-10">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Enlace {{ index + 1 }} </span>
                                        </div>
                                        <input type="text" v-model="link.url" class="form-control" placeholder="Enlace GoogleDrive" @change="getImageDrive(index,link.url)"/>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm mr-1" @click="getImageDrive(index,link.url)" v-if="link.url != ''">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" @click="eliminarLink(index)">&times;</button>
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
                            <button type="button" @click="closeEdit()"  class="btn btn-secondary">Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="actualizarArticulo()">Actualizar</button>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Fin Edit Articulo -->
            <!-- Ver Articulo -->
            <template v-if="listado==2">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-12 text-center">
                            <h3> Actualizado: {{ usuario }}</h3>
                            <h3 v-text="tituloModal"></h3>
                        </div>&nbsp;
                    </div>
                    <div class="col-12 d-flex justify-content-end" v-if="estado  && salida == 0">
                        <button type="button" class="btn btn-info" @click="cambiarSalida(articulo_id) ">
                             <i class="fa fa-star" aria-hidden="true"></i> Exhibida
                        </button>
                    </div>
                    <div class="col-12 d-flex justify-content-end" v-else-if="estado && salida == 1">
                        <button type="button" class="btn btn-danger" @click="quitarSalida(articulo_id) ">
                             <i class="fa fa-star" aria-hidden="true"></i> Anular Exhibida
                        </button>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <div v-if="isEdition && estado">
                            <label for=""><strong>Comprometido: </strong></label>
                            <toggle-button @change="cambiarComprometido(articulo_id)" v-model="btnComprometido" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                        </div>&nbsp;
                        <div v-if="usuario" class="mt-0 mt-md-1">
                            <label for=""><strong>Actualizo: </strong> {{ usuario }} </label>
                            <!-- <p v-text="usuario"></p> -->
                        </div>&nbsp;
                    </div>
                    <div class="row">
                        <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Código de material</span>
                            </div>
                            <p class="form-control" v-text="sku"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">No° de placa</span>
                            </div>
                           <p class="form-control" v-text="codigo"></p>
                        </div>

                        <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Material</span>
                            </div>
                            <p class="form-control" v-text="nombre_categoria"></p>
                        </div>

                        <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Terminado</span>
                            </div>
                           <p class="form-control" v-text="terminado"></p>
                        </div>
                         <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Ubicación</span>
                            </div>
                            <p class="form-control" v-text="ubicacion"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-4 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Fecha de llegada</span>
                            </div>
                            <p class="form-control" v-text="fecha_llegada"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-4  mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Contenedor</span>
                            </div>
                            <p class="form-control" v-text="contenedor"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Origen</span>
                            </div>
                           <p class="form-control" v-text="origen"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3  mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Precio m<sup>2</sup></span>
                            </div>
                            <p class="form-control" v-text="precio_venta"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Stock</span>
                            </div>
                            <p class="form-control" v-text="stock"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Moficado</span>
                            </div>
                            <p class="form-control" v-text="modificado"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Espesor</span>
                            </div>
                            <p class="form-control" v-text="espesor"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Largo</span>
                            </div>
                            <p class="form-control" v-text="largo"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Alto</span>
                            </div>
                            <p class="form-control" v-text="alto"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Metros<sup>2</sup></span>
                            </div>
                            <p class="form-control" v-text="metros_cuadrados"></p>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-3 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Correccion</span>
                            </div>
                             <p class="form-control" v-text="correccion"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group input-group-sm col-12 col-lg-4 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Descripción</span>
                            </div>
                            <textarea class="form-control rounded-0" style="background: #fff;" rows="3" maxlength="256" v-text="descripcion" disabled></textarea>
                        </div>
                        <div class="input-group input-group-sm col-12 col-lg-4 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Observaciones</span>
                            </div>
                            <!-- <textarea class="form-control rounded-0" style="resize: none;" rows="3" maxlength="256" v-model="observacion"></textarea> -->
                            <textarea class="form-control rounded-0" style="background: #fff;" rows="3" maxlength="256" v-text="observacion" disabled></textarea>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <barcode :value="codigo" :options="{format: 'EAN-13'}">
                                Generando código de barras.
                            </barcode>
                        </div>
                    </div>
                    <div class="container px-4">
                        <h3 id="timeline">Inspeccion de Material {{ codigo}}
                                &nbsp;
                            </h3>
                        <div class="row gx-5">
                            <div class="col" v-if="relice == 1">
                                <div class="p-3 border bg-light border-primary" ><span><strong>Material marcado con relice.</strong></span>
                                    <button type="button" class="btn btn-primary btn-circle" @click="desactivarRelice(articulo_id)">
                                        <i class="fa fa-bolt" aria-hidden="true">
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <div class="col" v-if="detalle == 1">
                                <div class="p-3 border bg-light border-warning"><span><strong>Material marcado con Detalle.</strong></span>
                                    <button type="button" class="btn btn-warning btn-circle" @click="desactivarDetalle(articulo_id)">
                                        <i class="fa fa-exclamation-triangle" aria-hidden="true">
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <div class="col" v-if="pulido == 1">
                                <div class="p-3 border bg-light border-danger"><span><strong>Material marcado como Mal Pulido.</strong></span>
                                 <button type="button" class="btn btn-danger btn-circle" @click="desactivarPulido(articulo_id)">
                                        <i class="fa fa-maxcdn" aria-hidden="true">
                                        </i>
                                    </button>
                                </div>
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
                                                <button class="btn btn-danger btn-sm btnElimImg" @click="eliminarImgLink(img.id,articulo_id)">Eliminar</button>
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
                            <button type="button" @click="closeVer()" class="btn btn-secondary">Cerrar</button>
                            <!-- <button type="button" class="btn btn-primary" @click="actualizarArticulo()">Actualizar</button> -->
                        </div>
                    </div>
                </div>
            </template>
            <!-- Fin Ver Articulo -->
        </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>

    <!-- Modal exportar excel -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal3}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-success modal-md " role="document">
            <div class="modal-content content-export">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal3()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body ">
                    <!-- <h3 class="mb-3">Generar reporte de ventas</h3> -->
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Ubicacion: </strong></label>
                            <select class="form-control" v-model="bodegaReporte">
                                <option value="" disabled>Seleccione una ubicación</option>
                                <option v-for="bodega in arrayUbicaciones" :key="bodega.ubicacion" :value="bodega.ubicacion" v-text="bodega.ubicacion"></option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        <div v-if="bodegaReporte">
                            <button type="button" class="btn btn-primary ml-5" @click="listarExcel(bodegaReporte)">Disponible</button>
                        </div>
                        <div v-if="bodegaReporte">
                            <button type="button" class="btn btn-primary ml-5" @click="listarExcelVendido(bodegaReporte)">No Entregado</button>
                        </div>
                        <div v-if="bodegaReporte && usrol == 1">
                            <button type="button" class="btn btn-primary ml-5" @click="listarExcelOculto(bodegaReporte)">Oculto</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal3()">Cerrar</button>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin exportar excel -->

  </main>
</template>
<script>

import VueBarcode from 'vue-barcode';
import VueLightbox from 'vue-lightbox';
import ToggleButton from 'vue-js-toggle-button';
import moment from 'moment';
import VueClipboard from 'vue-clipboard2';
import $ from 'jquery';
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton);
Vue.use(VueClipboard);
export default {
    data() {
        return {
            articulo_id: 0,
            idcategoria :0,
            nombre_categoria : '',
            codigo : '',
            sku : '',
            terminado : '',
            largo : 0,
            alto : 0,
            metros_cuadrados : 0,
            espesor : 2,
            precio_venta : 0,
            ubicacion : '',
            usedit: 0,
            stock : 1,
            comprometido : 0,
            salida : 0,
            usuario : '',
            descripcion: '',
            observacion : '',
            origen : '',
            contenedor: '',
            fecha_llegada : '',
            file : '',
            actualizado: '',
            estado : 0,
            imagenMinatura : '',
            arrayArticulo: [],
            modal: 0,
            modal2: 0,
            modal3: 0,
            modal5: 0,
            modal6: 0,
            tituloModal: '',
            tipoAccion: 0,
            errorArticulo: 0,
            errorMostrarMsjArticulo: [],
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            paginationartocul : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            criterio : 'sku',
            bodega : '',
            acabado : '',
            modificado: '',
            categoriaFilt : 0,
            buscarA : '',
            criterioA : 'sku',
            estadoOcul: '',
            buscarO : '',
            criterioO : 'sku',
            totres : 0,
            sumaMts : 0,
            totales: 0,
            metrosTotales: 0,
            buscar : '',
            arrayCategoria : [],
            btnComprometido : '',
            btnSalida : '',
            isEdition : false,
            showElim : false,
            estadoArt : 1,
            arrayUbicaciones : [],
            bodegaReporte : "",
            listado : 1,
            showNew : false,
            remoFile : false,
            zona : "GDL",
            cutAvi : true,
            codigoA : "",
            codigoB : "",
            largoA : 0,
            largoB : 0,
            altoA : 0,
            altoB : 0,
            metros_cuadradosA : 0,
            metros_cuadradosB : 0,
            savedA : 0,
            savedB : 0,
            precioA : 0,
            precioB : 0,
            arrayLinks : [],
            arrayImagenes : [],
            usrol : 0,
            usrol : 0,
            btnOculto: false,
            condicion:0,
            articulos: [],
            buscador:'',
            timeout:0,
            salida:0,
            setTimeoutBuscador:'',
            buscador: '',
            user:'',
            pulido: 0,
            relice:0,
            detalle:0,
            autoing : 0,
            editar : 0,
            inspeccion: 0,
            correccion: "",

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
            imagen(){
                return this.imagenMinatura;
            },
            calcularMtsA : function(){
                let me = this;
                let resultado = 0;
                resultado = resultado + (me.altoA * me.largoA);
                me.metros_cuadradosA = resultado;
                return resultado;
            },
            calcularMtsB : function(){
                let me=this;
                let resultado = 0;
                resultado = resultado + (me.altoB * me.largoB);
                me.metros_cuadradosB = resultado;
                return resultado;
            },
            calcularMtsRestantes : function(){
                let me=this;
                let resultado = 0;
                resultado = me.metros_cuadrados - (me.metros_cuadradosA + me.metros_cuadradosB);
                return resultado;
            },
            isActivedArtOcl: function(){
            return this.paginationartocul.current_page;
            },
            pagesNumberArtOcl: function() {
                if(!this.paginationartocul.to) {
                    return [];
                }

                var from = this.paginationartocul.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2);
                if(to >= this.paginationartocul.last_page){
                    to = this.paginationartocul.last_page;
                }

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            },
    },
    methods:{
        listarArticulo (page,buscar,criterio,bodega,acabado,estado,idcategoria){
            let me=this;
            me.selectCategoria();
            if(me.zona == 'SLP'){
                bodega = 'San Luis';
                me.bodega = 'San Luis';
            }else{
                if(bodega == 'San Luis'){
                    bodega = "";
                    me.bodega = "";
                }

            }if(me.zona == 'AGS'){
                bodega = 'Aguascalientes';
                me.bodega = 'Aguascalientes';
            }else{
                if(bodega == 'Aguascalientes'){
                    bodega = "";
                    me.bodega = "";
                }
            }
            if(me.zona == 'PBA'){
                bodega = 'Puebla';
                me.bodega = 'Puebla';
            }else{
                if(bodega == 'Puebla'){
                    bodega = "";
                    me.bodega = "";
                }
            }

            var url= '/articulo/listarArticuloActualizado?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&bodega='
            + bodega + '&acabado=' + acabado + '&estado=' + estado + '&idcategoria=' + idcategoria;
            axios.get(url, {
                params:{
                    buscador: this.buscador,
                }
            }).then(function (response) {
                var respuesta= response.data;
                me.arrayArticulo = respuesta.articulos.data;
                me.pagination= respuesta.pagination;
                me.totres = respuesta.total;
                me.usrol = respuesta.usrol;
                me.sumaMts = respuesta.sumaMts[0]['metros'];
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
        cambiarPagina(page,buscar,criterio,bodega,acabado,estado,idcategoria){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarArticulo(page,buscar,criterio,bodega,acabado,estado,idcategoria);
        },
        registrarArticulo(){

                if (this.validarArticulo()){
                    return;
                }
                let me = this;

                axios.post("/articulo/registrar",{
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
                    'file' : this.file
                }).then(function (response) {
                    me.listarArticulo(1,'','sku','','',1,0);
                }).catch(function (error) {
                    console.log(error);
                });
        },
        validarArticulo() {
            this.errorArticulo = 0;
            this.errorMostrarMsjArticulo = [];

            if (this.idcategoria==0) this.errorMostrarMsjArticulo.push("Selecciona una categoría.");
            if (!this.sku) this.errorMostrarMsjArticulo.push("El código del material no puede estar vacío.");
            if (!this.terminado) this.errorMostrarMsjArticulo.push("El terminado del artículo no puede estar vacío.");
            if (!this.largo) this.errorMostrarMsjArticulo.push("El largo del artículo no puede estar vacío.");
            if (!this.alto) this.errorMostrarMsjArticulo.push("El alto del artículo no puede estar vacío.");
            if (!this.metros_cuadrados) this.errorMostrarMsjArticulo.push("Los metros cuadrados del artículo no pueden estar vacíos.");
            if (!this.espesor) this.errorMostrarMsjArticulo.push("El espesor del artículo no puede estar vacío.");
            if (!this.ubicacion) this.errorMostrarMsjArticulo.push("Seleccione una bodega de descarga");
            if (!this.stock) this.errorMostrarMsjArticulo.push("El stock del artículo debe ser un número y no puede estar vacío.");
            if (this.errorMostrarMsjArticulo.length) this.errorArticulo = 1;

            return this.errorArticulo;
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

            if(this.arrayLinks.length)
                if(!this.file)
                   this.file = this.arrayLinks[0]['direction'];

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
                'enlaces' : this.arrayLinks,
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
        verArticulo(data = []){
            //this.selectCategoria();
            this.listado = 2;
            this.showNew = false;
            this.tituloModal = `${ data['sku'] } - ${ data['codigo'] }`;
            this.articulo_id = data['id'];
            this.idcategoria = data['idcategoria'];
            this.nombre_categoria = data['nombre_categoria'];
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
            this.salida = data['salida'];
            this.usuario = data['usuario'];
            this.isEdition = true;
            this.file = data['file'];
            this.relice = data['relice'];
            this.detalle = data['detalle'];
            this.pulido = data['pulido'];
            this.modificado = data['modificado'];
            this.correccion = data['correccion'];

            if(this.comprometido == 1){
                this.btnComprometido = true;
            }else{
                this.btnComprometido = false;
            }
            if(this.salida== 1){
                this.btnSalida = true;
            }else{
                this.btnSalida = false;
            }
            if(this.condicion== 6){
                this.btnOculto = true;
            }else{
                this.btnOculto   = false;
            }

            if(data['file']){
                this.imagenMinatura = 'https://drive.google.com/uc?id='+data['file'];
            }else{
                this.imagenMinatura = '';
            }

            this.getLinks(data['id']);
            /* let hasImg = 'images/' + data['file'];

            if(data['file']){
                this.showElim = true;
                this.imagenMinatura = 'images/' + data['file'];
                this.remoFile = false;
            }else{
                this.showElim = false;
                this.imagenMinatura = 'images/null';
                this.remoFile = false;
            } */
        },
        closeVer(){
            let pagec = this.pagination.current_page;
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
            this.imagenMinatura = '';
            this.btnComprometido = '';
            this.comprometido = 0;
            this.usuario = '';
            this.isEdition = false;
            this.file = "";
            this.showElim = false;
            this.tituloModal = "";
            this.nombre_categoria = "";
            this.cutAvi = true;
            this.codigoA = "";
            this.codigoB = "";
            this.largoA = 0;
            this.largoB = 0;
            this.altoA = 0;
            this.altoB = 0;
            this.metros_cuadradosA = 0;
            this.metros_cuadradosB = 0;
            this.savedA = 0;
            this.savedB = 0;
            this.modificado = "";
            this.listarArticulo(pagec,this.buscar,this.criterio,this.bodega,this.acabado,this.estadoArt,this.categoriaFilt);
        },
        eliminarLink(index){
            let me = this;
            me.arrayLinks.splice(index,1);
        },
        getImageDrive(index,url){
            var convert1 = url.split("d/");
            var convert2 = convert1[1].split("/");
            this.arrayLinks[index]['direction'] = convert2[0];
        },
        getLinks(id){
            let me = this;
            var url= '/articulo/getLinks?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayImagenes = respuesta.links;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        eliminarImgLink(id,idarticulo){
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
                         me.getLinks(idarticulo);
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
        cambiarComprometido(id){
          let me = this;
            if(me.btnComprometido == true){
                me.comprometido = 1;
            }else{
                me.comprometido = 0;
            }
            axios.put('/articulo/cambiarComprometido',{
                'id': id,
                'comprometido' : this.comprometido
            }).then(function (response) {
                me.listarArticulo(1,'','sku','','',1,0);
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarSalida(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de marcar el articulo Exhibido?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/articulo/cambiarSalida', {
                        'id' : id
                    }).then(function(response) {
                        me.listado == 1;
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Salida!",
                            "El articulo esta listo para salir.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        editArticulo(data = []){
            this.selectCategoria();
            this.listado = 0;
            this.showNew = false;
            this.tituloModal = `Actualizar Artículo ${ data['sku'] } - ${ data['codigo'] }`;
            this.articulo_id = data['id'];
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
            this.isEdition = true;
            this.file = data['file'];
            this.autoing = data['autoing'];
            this.correccion = data['correccion'];
            if(this.comprometido == 1){
                this.btnComprometido = true;
            }else{
                this.btnComprometido = false;
            }
            let hasImg = 'images/' + data['file'];
            if(data['file']){
                this.imagenMinatura = 'https://drive.google.com/uc?id='+data['file'];
            }else{
                this.imagenMinatura = '';
            }
            this.getLinks(data['id']);
            /* if(data['file']){
                this.showElim = true;
                this.imagenMinatura = 'images/' + data['file'];
                this.remoFile = false;
            }else{
                this.showElim = false;
                this.imagenMinatura = 'images/null';
                this.remoFile = false;
            } */
        },
        closeEdit(){
            let pagec = this.pagination.current_page;
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
            this.imagenMinatura = '';
            this.btnComprometido = '';
            this.comprometido = 0;
            this.usuario = '';
            this.isEdition = false;
            this.file = "";
            this.showElim = false;
            this.tituloModal = "";
            this.listarArticulo(pagec,this.buscar,this.criterio,this.bodega,this.acabado,this.estadoArt,this.categoriaFilt);
            this.arrayLinks = [];
        },
        cambiarEstadoMaterial(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de marcar este articulo con Relice?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/articulo/estadoMaterial', {
                        'id' : id
                    }).then(function(response) {
                        me.listado == 1;
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Relisado!",
                            "El articulo esta marcado con relice.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        cambiarDetalle(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de marcar este articulo con Detalle?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/articulo/cambiarDetalle', {
                        'id' : id
                    }).then(function(response) {
                        me.listado == 1;
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Detalle!",
                            "El articulo esta marcado con detalle.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        cambiarPulido(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de marcar este articulo como Mal Pulido?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/articulo/malPulido', {
                        'id' : id
                    }).then(function(response) {
                        me.listado == 1;
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Mal Pulido!",
                            "El articulo esta marcado como mal Pulido.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        desactivarRelice(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger",
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Estas seguro de desmarcar este articulo con relice?",
                type: "warning",
                showCancelButton: true,
                confirmButton: "Aceptar!",
                cancelButton: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if(result.value){
                    let me = this;
                    axios.put('/articulo/anularRelice', {
                        'id' : id
                    }).then(function(response) {
                        me.listado == 1;
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "Artículo desmarcado con relice.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });

                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        desactivarDetalle(id){
            const swalWithBootstrapButtons  = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger",
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Estas seguro de desmarcar este articulo con Detalle?",
                type: "warning",
                showCancelButton: true,
                confirmButton: "Aceptar!",
                cancelButton: "Canacelar!",
                reverseButtons: true
            })
            .then(result => {
                if(result.value){
                    let me = this;
                    axios.put('/articulo/anularDetalle', {
                        'id' : id
                    }).then(function(response){
                        me.listado == 1;
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "Articulo desmarcado con relice.",
                            "success"
                        )

                    }).catch(function(error){
                        console.log(error);
                    });
                }else if( result.dismiss === swal.DismissReason.cancel){

                }
            })
        },
        desactivarPulido(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger",
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Estas seguro de desmarcar este articulo como mal pulido?",
                type: "warning",
                showCancelButton: true,
                confirmButton: "Aceptar!",
                cancelButton: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if(result.value){
                    let me = this;
                    axios.put('/articulo/anularPulido', {
                        'id': id
                    }).then(function(response){
                        me.listado == 1;
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "Articulo desmarcado como Mal Pulido.",
                            "success"
                        )
                    }).catch(function(error){
                        console.log(error);
                    });
                }else if(result.dismiss === swal.DismissReason.cancel){

                }
            })
        },

    },
    mounted () {
         this.listarArticulo(1,this.buscar, this.criterio,this.bodega,this.acabado,this.estadoArt,this.categoriaFilt);
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
    .imgcenter {
        display:flex;
        margin:0 auto;
    }
    .modal-body{
        height: 550px;
        width: 100%;
        overflow-y: auto;
    }
    .btn-circle {
        width: 30px;
        height: 30px;
        padding: 6px 0px;
        border-radius: 15px;
        text-align: center;
        font-size: 13px;
        line-height: 1.42857;
    }
    .imglink{
        max-width : 200px !important;
        max-height: 150px !important;
    }
    .btnElimImg{
        width: 50%;
        font-size: 12px;
        float: right;
    }
</style>
