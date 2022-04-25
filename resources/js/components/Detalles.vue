
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
                  <i class="fa fa-align-justify"></i> Inspeccion de Material
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
                            <div class="input-group input-group-sm mt-1 mt-sm-0 ml-md-2 ml-lg-5" v-if="usrol== 1">
                                <button @click="abrirModal6()" class="btn  btn btn-dark btn-sm"><i class="icon-eye"></i></button>
                            </div>

                            <div class="input-group input-group-sm">
                                <label class="mb-1 ml-sm-5" for=""><strong>Total: </strong>&nbsp; {{ totres }} </label>
                            </div>
                            <div class="input-group input-group-sm mr-2">
                                <label class="mb-1 ml-sm-5" for=""><strong>Metros <sup>2</sup>: </strong>&nbsp; {{ sumaMts }} </label>
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
                                <input type="text" v-model="buscar" @keyup.enter="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)" class="form-control" placeholder="Texto a buscar">
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Material</span>
                                </div>
                                <select class="form-control" v-model="categoriaFilt" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)">
                                    <option value="0">Seleccione un material</option>
                                    <option v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                </select>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Terminado</span>
                                </div>
                                <input type="text" v-model="acabado" @keyup.enter="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)" class="form-control" placeholder="Terminado">
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Ubicación</span>
                                </div>
                                <select class="form-control" v-model="bodega" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)">
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
                                    <span class="input-group-text">Estados</span>
                                </div>
                                <select class="form-control" id="tipofact" name="tipofact" v-model="estadoArt" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)">
                                    <option value="1">Disponible</option>
                                    <option value="6" v-if="usrol == 1">Oculto</option>
                                </select>
                                <!-- <button class="btn btn-sm btn-info" type="button"><i class="fa fa-search" aria-hidden="true"></i>&nbsp; Filtros</button> -->
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Detalles</span>
                                </div>
                                <select class="form-control" id="tipofact" name="tipofact" v-model="buscador" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,detalle,categoriaFilt)">
                                    <option value="1">Limpio</option>
                                    <option value="2">Relice</option>
                                    <option value="3">Detalle</option>
                                    <option value="4">Mal Pulido</option>
                                </select>
                                <button class="btn btn-light mb-1" @click="buscarDetalle()"><i style="color:red;" class="fa fa-search"></i></button>
                                <!-- <button class="btn btn-sm btn-info" type="button"><i class="fa fa-search" aria-hidden="true"></i>&nbsp; Filtros</button> -->
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                               <!--  <button class="btn btn-sm btn-danger" type="button"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; Area</button> -->
                               <div class="input-group-append">
                                    <span class="input-group-text" style="background-color:red;color:white;"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; Area</span>
                                </div>
                                <select class="form-control" v-model="zona" @change="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)">
                                    <option value="GDL">Guadalajara</option>
                                    <option value="SLP">San Luis</option>
                                    <option value="AGS">Aguascalientes</option>
                                    <option value="PBA">Puebla</option>
                                </select>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3" v-if="estadoArt==1">
                                <div class="mb-2 mb-md-2 mb-lg-1 mb-xl-0 mt-1 mb-md-1 mb-lg-0 mb-xl-0">
                                    <button type="submit" @click="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)" class="btn btn-sm btn-primary mr-2"><i class="fa fa-search"></i>Buscar</button>
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
                                <th>No. Placa</th>
                                <th>Código de Material</th>
                                <th>Material</th>
                                <th>Largo</th>
                                <th>Alto</th>
                                <th>Metros<sup>2</sup></th>
                                <th>Espesor</th>
                                <th>Terminado</th>
                                <th>Bodega de descarga</th>
                                <th>Estado</th>
                                <th>Stock</th>
                                <th>Inspeccion</th>
                                <th>Modificacion</th>
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
                                            <template v-if="usrol == 1">
                                                <template v-if="articulo.condicion == 1 && estadoArt == 1">
                                                    <button type="button" class="btn btn-danger btn-sm" @click="desactivarArticulo(articulo.id)">
                                                        <i class="icon-trash"></i>
                                                    </button>
                                                </template>
                                                <template v-else-if="articulo.condicion == 0 && estadoArt == 1">
                                                    <button type="button" class="btn btn-info btn-sm" @click="activarArticulo(articulo.id)">
                                                        <i class="icon-check"></i>
                                                    </button>
                                                </template>
                                                <template v-else-if="articulo.condicion == 6">
                                                    <button type="button" class="btn btn-info btn-sm" @click="activarArticulo(articulo.id)">
                                                        <i class="icon-check"></i>
                                                    </button>
                                                </template>
                                                <template v-else></template>&nbsp;
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
                                            <button type="button" @click="doCopy(articulo)" class="btn btn-primary btn-sm">
                                                <i class="fa fa-clipboard" aria-hidden="true"></i>
                                            </button> &nbsp;
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
                                    <td  v-text="articulo.codigo"></td>
                                    <td v-text="articulo.sku"></td>
                                    <td v-text="articulo.nombre_categoria"></td>
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
                                        <div v-else-if="articulo.condicion == 6">
                                            <span class="badge badge-dark">Oculto</span>
                                        </div>
                                    </td>
                                    <td v-text="articulo.stock"></td>
                                    <td>
                                        <div v-if="articulo.inspeccion==1">
                                            <span class="badge badge-success">Material Limpio</span>
                                        </div>
                                        <div v-else-if="articulo.inspeccion==2">
                                            <span class="badge badge-danger">Material con Relice</span>
                                        </div>
                                        <div v-else-if="articulo.inspeccion==3">
                                            <span class="badge badge-warning">Material con Detalle</span>
                                        </div>
                                        <div v-else-if="articulo.inspeccion==4">
                                            <span class="badge badge-secondary">Material mal pulido</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div v-if="articulo.correccion=='No actualizado'">
                                            <span class="badge badge-info">No Actualizado</span>
                                        </div>
                                        <div v-if="articulo.correccion=='Disminucion de Medida'">
                                            <span class="badge badge-warning">Disminucion de Medida</span>
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
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,bodega,acabado,estadoArt,detalleArt,categoriaFilt)">Sig</a>
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
                                <input type="text" v-model="contenedor" class="form-control" placeholder="Contenedor" disabled/>
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
                           <h3 v-text="tituloModal"></h3>
                        </div>&nbsp;
                    </div>
                    <div class="col-12 d-flex justify-content-end" v-if="estado  && salida == 0">
                        <button type="button" class="btn btn-info" @click="cambiarSalida(articulo_id) ">
                             <i class="fa fa-star" aria-hidden="true"></i> Salida
                        </button>
                    </div>
                    <div class="col-12 d-flex justify-content-end" v-else-if="estado && salida == 1">
                        <button type="button" class="btn btn-danger" @click="quitarSalida(articulo_id) ">
                             <i class="fa fa-star" aria-hidden="true"></i> Anular Salida
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
                    <div class="col-12 d-flex justify-content-end">
                        <div v-if="isEdition && estado">
                            <label for=""><strong>Ocultar: </strong></label>
                            <toggle-button @change="ocultarArticulo(articulo_id)" v-model="btnOculto" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
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
                    <div class="row">
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
            <!-- Cortar Placa -->
            <template v-if="listado == 3">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-12 text-center">
                           <h3 v-text="tituloModal"></h3>
                        </div>&nbsp;
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm text-center table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>No° Placa</th>
                                        <th>Código de material</th>
                                        <th>Material</th>
                                        <th>Largo</th>
                                        <th>Alto</th>
                                        <th>Metros<sup>2</sup></th>
                                        <th>Espesor</th>
                                        <th>Terminado</th>
                                        <th>Stock</th>
                                        <th>Precio m<sup>2</sup></th>
                                        <th>Ubicacion</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td v-text="codigo"></td>
                                        <td v-text="sku"></td>
                                        <td v-text="nombre_categoria"></td>
                                        <td v-text="largo"></td>
                                        <td v-text="alto"></td>
                                        <td v-text="metros_cuadrados"></td>
                                        <td v-text="espesor"></td>
                                        <td v-text="terminado"></td>
                                        <td v-text="stock"></td>
                                        <td v-text="precio_venta"></td>
                                        <td v-text="ubicacion"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 text-center">
                           <h3 style="color:red;">Metros restantes: {{ calcularMtsRestantes }}</h3>
                           <!-- <input type="number" readonly :value="calcularMtsRestantes" class="form-control"/> -->
                        </div>&nbsp;
                    </div>
                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="row mb-2">
                            <div class="col-12 col-sm-12 col-lg-6 border m-0">
                                <h4 class="text-center pt-2">Placa A</h4>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-lg d-flex flex-column">
                                       <div>
                                            <span style="color:red;" v-show="codigoA==''">(*Ingrese el código, recuerde debe ser único)</span>
                                       </div>
                                       <div>
                                           <div class="input-group input-group-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">No° de placa</span>
                                                </div>
                                                <input type="text" v-model="codigoA" class="form-control" placeholder="Código de material"/>
                                            </div>
                                       </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-lg d-flex flex-column">
                                       <div>
                                            <span style="color:red;" v-show="metros_cuadradosA==''">(*Ingrese las medidas correctamente)</span>
                                       </div>
                                       <div>
                                           <div class="input-group input-group-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Metros<sup>2</sup></span>
                                                </div>
                                                <input type="number" readonly :value="calcularMtsA" class="form-control"/>
                                            </div>
                                       </div>
                                    </div>
                                </div>
                                <div class="row mt-2 mb-2">
                                    <div class="col-12 col-sm-12 col-lg-6 d-flex flex-column">
                                       <div>
                                            <span style="color:red;" v-show="largoA==''">(*Ingrese el largo de la mitad A)</span>
                                       </div>
                                       <div>
                                           <div class="input-group input-group-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Largo</span>
                                                </div>
                                                <input type="text" v-model="largoA" class="form-control" placeholder="Código de material"/>
                                            </div>
                                       </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-lg-6 d-flex flex-column">
                                       <div>
                                            <span style="color:red;" v-show="altoA==''">(*Ingrese el alto de la mitad A)</span>
                                       </div>
                                       <div>
                                           <div class="input-group input-group-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Alto</span>
                                                </div>
                                                <input type="text" v-model="altoA" class="form-control" placeholder="Código de material"/>
                                            </div>
                                       </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary float-right" v-if="!savedA" @click="registrarArticuloA()">Guardar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-lg-6 border m-0">
                                <h4 class="text-center pt-2">Placa B</h4>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-lg d-flex flex-column">
                                       <div>
                                            <span style="color:red;" v-show="codigoB==''">(*Ingrese el código, recuerde debe ser único)</span>
                                       </div>
                                       <div>
                                           <div class="input-group input-group-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">No° de placa</span>
                                                </div>
                                                <input type="text" v-model="codigoB" class="form-control" placeholder="Código de material"/>
                                            </div>
                                       </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-lg d-flex flex-column">
                                       <div>
                                            <span style="color:red;" v-show="metros_cuadradosB==''">(*Ingrese las medidas correctamente)</span>
                                       </div>
                                       <div>
                                           <div class="input-group input-group-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Metros<sup>2</sup></span>
                                                </div>
                                                <input type="number" readonly :value="calcularMtsB" class="form-control"/>
                                            </div>
                                       </div>
                                    </div>
                                </div>
                                <div class="row mt-2 mb-2">
                                    <div class="col-12 col-sm-12 col-lg-6 d-flex flex-column">
                                       <div>
                                            <span style="color:red;" v-show="largoB==''">(*Ingrese el largo de la mitad A)</span>
                                       </div>
                                       <div>
                                           <div class="input-group input-group-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Largo</span>
                                                </div>
                                                <input type="text" v-model="largoB" class="form-control" placeholder="Código de material"/>
                                            </div>
                                       </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-lg-6 d-flex flex-column">
                                       <div>
                                            <span style="color:red;" v-show="altoB==''">(*Ingrese el alto de la mitad A)</span>
                                       </div>
                                       <div>
                                           <div class="input-group input-group-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Alto</span>
                                                </div>
                                                <input type="text" v-model="altoB" class="form-control" placeholder="Código de material"/>
                                            </div>
                                       </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary float-right" v-if="!savedB" @click="registrarArticuloB()">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-center">
                                <h5>La razón del corte de la placa es?</h5>
                            </div>&nbsp;
                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Observaciones</span>
                                </div>
                                <textarea class="form-control rounded-0"  rows="3" maxlength="256" v-model="observacion"></textarea>
                            </div>
                        </div>
                    </form>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" v-if="cutAvi" @click="closeVer()"  class="btn btn-secondary">Cancelar</button>
                            <button type="button" class="btn btn-primary" @click="updateCortado()">Guardar</button>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Fin Cortar Placa -->
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
    <!--Inicio del modal listar articulos-cotizados-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal5}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal5()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterioA">
                                    <option value="sku">Código de material</option>
                                    <option value="codigo">No° de Placa</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" v-model="buscarA" @keyup.enter="listarArticuloCotizado(1,buscarA,criterioA)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarArticuloCotizado(1,buscarA,criterioA)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>&nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-center table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No° Placa</th>
                                <th>Código de material</th>
                                <th>Material</th>
                                <th>Metros<sup>2</sup></th>
                                <th>Terminado</th>
                                <th>Ubicacion</th>
                                <th>No° Cotización</th>
                                <th>Cantidad</th>
                                <th>Stock Dispo.</th>
                                <th>Cliente</th>
                                <th>Comprometido</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayArticulo.length">
                                <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                    <td v-text="articulo.codigo"></td>
                                    <td v-text="articulo.sku"></td>
                                    <td v-text="articulo.nombre_categoria"></td>
                                    <td v-text="articulo.metros_cuadrados"></td>
                                    <td v-text="articulo.terminado"></td>
                                    <td v-text="articulo.ubicacion"></td>
                                    <td v-text="articulo.cotizacion"></td>
                                    <td v-text="articulo.cantidad"></td>
                                    <th v-text="articulo.stock"></th>
                                    <td v-text="articulo.cliente"></td>
                                    <td>
                                    <div v-if="articulo.cantidad > 1 == articulo.comprometido">
                                        <span class="badge badge-success">SI</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">NO</span>
                                    </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <strong>NO hay artículos cotizados o con ese criterio...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginacion MODAL Cotizados -->
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="paginationartcot.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtCot(paginationartcot.current_page - 1,buscarA,criterioA)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumberArtCot" :key="page" :class="[page == isActivedArtCot ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtCot(page,buscarA,criterioA)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="paginationartcot.current_page < paginationartcot.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtCot(paginationartcot.current_page + 1,buscarA,criterioA)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal5()">Cerrar</button>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
    <!--Inicio del modal listar articulos-ocultos-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal6}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                      <div class="input-group input-group-sm">
                                <label class="mb-1 ml-sm-5" for=""><strong>Total: </strong>&nbsp; {{ totales }} </label>
                        </div>
                        <div class="input-group input-group-sm mr-2">
                                <label class="mb-1 ml-sm-5" for=""><strong>Metros <sup>2</sup>: </strong>&nbsp; {{ metrosTotales }} </label>
                        </div>
                    <button type="button" class="close" @click="cerrarModal6()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-6 mb-3">
                                <button class="btn btn-light mb-1" ><i style="color:red;" class="fa fa-search"></i> Filtro</button>
                                <input type="text" v-model="buscador" @keyup.enter="buscarArticuloOculto()" class="form-control mb-1" placeholder="Buscar...">
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Ubicación</span>
                                </div>
                                <select class="form-control" v-model="bodega" @change="listarArticuloOculto(1,buscarO,criterioO,estadoOcul,bodega)">
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
                                    <template v-else-if="zona== 'AGS'">
                                        <option value="Aguascalientes">Aguascalientes</option>
                                    </template>
                                    <template v-else-if="zona== 'PBA'">
                                        <option value="Puebla">Puebla</option>
                                    </template>
                                </select>
                                <!-- <button type="submit" @click="listarArticulo(1,buscar,criterio,bodega,acabado,estadoArt,categoriaFilt)" class="btn btn-sm btn-primary"><i class="fa fa-search"></i>Buscar</button> -->
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3">
                               <!--  <button class="btn btn-sm btn-danger" type="button"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; Area</button> -->
                               <div class="input-group-append">
                                    <span class="input-group-text" style="background-color:red;color:white;"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; Area</span>
                                </div>
                                <select class="form-control" v-model="zona" @change="listarArticuloOculto(1,buscarO,criterioO,estadoOcul,bodega)">
                                    <option value="GDL">Guadalajara</option>
                                    <option value="SLP">San Luis</option>
                                    <option value="AGS">Aguascalientes</option>
                                    <option value="PBA">Puebla</option>
                                </select>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3" v-if="estadoArt==1">
                                <div class="mb-2 mb-md-2 mb-lg-1 mb-xl-0 mt-1 mb-md-1 mb-lg-0 mb-xl-0">
                                    <button type="submit" @click="listarArticuloOculto(1,buscarO,criterioO,estadoOcul,bodega)" class="btn btn-sm btn-primary mr-2"><i class="fa fa-search"></i>Buscar</button>
                                </div>
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
                                <th>Metros<sup>2</sup></th>
                                <th>Terminado</th>
                                <th>Ubicacion</th>
                                <th>Cantidad</th>
                                <th>Stock Dispo.</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayArticulo.length">
                                <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                    <td>
                                        <div class="form-inline">
                                            <button type="button" @click="verArticulo(articulo)" class="btn btn-success btn-sm">
                                                <i class="icon-eye"></i>
                                            </button> &nbsp;
                                            <template v-if="articulo.condicion == 6 && estadoArt == 1">
                                                <button type="button" class="btn btn-info btn-sm" @click="activarArticulo(articulo.id)">
                                                    <i class="icon-check"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                    <td v-text="articulo.codigo"></td>
                                    <td v-text="articulo.sku"></td>
                                    <td v-text="articulo.nombre_categoria"></td>
                                    <td v-text="articulo.metros_cuadrados"></td>
                                    <td v-text="articulo.terminado"></td>
                                    <td v-text="articulo.ubicacion"></td>
                                    <td v-text="articulo.cantidad"></td>
                                    <th v-text="articulo.stock"></th>
                                    <td >
                                        <span class="badge badge-dark" v-if="articulo.condicion== 6">Oculto</span>
                                    </td>

                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <strong>NO hay artículos ocultos...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginacion MODAL Cotizados -->
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="paginationartocul.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtOcl(paginationartocul.current_page - 1,buscarO,criterioO,estadoOcul,bodega)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumberArtCot" :key="page" :class="[page == isActivedArtOcl ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtOcl(page,buscarO,criterioO,estadoOcul,bodega)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="paginationartocul.current_page <  paginationartocul.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtOcl( paginationartocul.current_page + 1,buscarO,criterioO,estadoOcul,bodega)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal6()">Cerrar</button>
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
        return{
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
            estado : 0,
            imagenMinatura : '',
            arrayArticulo: [],
            modal: 0,
            modal2: 0,
            modal3: 0,
            modal5: 0,
            modal6: 0,
            correccion: '',
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
            paginationartcot : {
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
            x: 0,
            y: 0,
            isDrawing: false,
            canvas: null,
            offset : 3,
            criterio : 'sku',
            bodega : '',
            acabado : '',
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
            detalleArt : 1,
            dañado : 1,
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
            inspeccion:0,
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
            buscador: 1,
            timeout:0,
            setTimeoutBuscador:''
        };
    },
    components: {
        'barcode': VueBarcode,

    },
    computed: {
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
            isActivedArtCot: function(){
            return this.paginationartcot.current_page;
            },
            pagesNumberArtCot: function() {
                if(!this.paginationartcot.to) {
                    return [];
                }

                var from = this.paginationartcot.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2);
                if(to >= this.paginationartcot.last_page){
                    to = this.paginationartcot.last_page;
                }

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
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
    methods: {
        listarArticulo (page,buscar,criterio,bodega,acabado,estado,detalle,idcategoria){
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

            var url= '/articulo/listarArticuloDetalle?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&bodega='
            + bodega + '&acabado=' + acabado + '&estado=' + estado + '&detalle='+ detalle + '&idcategoria=' + idcategoria;
            axios.get(url, {
                params:{
                    buscador : this.buscador,
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
        buscarDetalle(){

            clearTimeout( this.setTimeoutBuscador)
            this.setTimeoutBuscador = setTimeout(this.listarArticulo(1,'','sku','','','',0), 360);
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
        cambiarPagina(page,buscar,criterio,bodega,acabado,estado,detalle,idcategoria){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarArticulo(page,buscar,criterio,bodega,acabado,estado,detalle,idcategoria);
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
        drawLine(x1, y1, x2, y2) {
            var c = document.getElementById("myCanvas");
            this.canvas = c.getContext('2d');

            c.beginPath();
            c.strokeStyle = 'black';
            c.lineWidth = 1;
            c.moveTo(x1, y1);
            c.lineTo(x2, y2);
            c.stroke();
            c.closePath();
        },
        beginDrawing(e) {
            this.x = e.offsetX;
            this.y = e.offsetY;
            this.isDrawing = true;
        },
        keepDrawing(e) {
            if (this.isDrawing === true) {
                this.drawLine(this.x, this.y, e.offsetX, e.offsetY);
                this.x = e.offsetX;
                this.y = e.offsetY;
            }
        },
        stopDrawing(e) {
            if (this.isDrawing === true) {
                this.drawLine(this.x, this.y, e.offsetX, e.offsetY);
                this.x = 0;
                this.y = 0;
                this.isDrawing = false;
            }
        },
        obtenerImagen(e){
            let img = e.target.files[0];
           /*  console.log(img); */
            this.file = img;
            this.cargarImagen(img);
        },
        cargarImagen(img){
            let reader = new FileReader();
            reader.onload = (e) => {
                this.imagenMinatura = e.target.result;
                this.file =  e.target.result;
                this.remoFile = true;
            }
            reader.readAsDataURL(img);
        },
        limpiarFile(){
            this.imagenMinatura = "images/null";
            this.file =  "";
            this.remoFile = false;
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
                'inspeccion' : this.inspeccion,
                'correccion'  : this.correccion,
                'enlaces' : this.arrayLinks
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
        desactivarArticulo(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de desactivar este artículo?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/articulo/desactivar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarArticulo(1,'','sku','','',1,0);
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
        activarArticulo(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de activar esta artículo?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/articulo/activar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "Artículo activado con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        quitarSalida(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de desmarcar este artículo como Exhibido?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/articulo/anularSalida', {
                        'id' : id
                    }).then(function(response) {
                        me.listado == 1;
                        me.listarArticulo(1,'','sku','','',1,0);
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "Artículo activado con éxito.",
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
                        'id' : id,
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
                        'id' : id,
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
                        'id' : id,
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
            this.listarArticulo(pagec,this.buscar,this.criterio,this.bodega,this.acabado,this.estadoArt,this.categoriaFilt);
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
            this.correccion = data['correccion'];
            this.file = data['file'];
            this.autoing = data['autoing'];
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
    },
    mounted() {
        this.listarArticulo(1,this.buscar, this.criterio,this.bodega,this.acabado,this.estadoArt,this.detalle,this.categoriaFilt);
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
    #myCanvas {
        border: 1px solid grey;
    }
</style>
