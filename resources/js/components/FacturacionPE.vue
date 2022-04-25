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
          <i class="fa fa-align-justify"></i> Facturación Presupuestos Especiales
        </div>
        <!-- Listado -->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-12">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio">
                                <option value="cliente">Cliente</option>
                                <option value="num_comprobante">No° Comprobante</option>
                                <option value="forma_pago">Forma de pago</option>
                            </select>
                            <input type="text" v-model="buscar" @keyup.enter="listarVenta(1,buscar,criterio,estadoVenta,tipo_fact)" class="form-control mb-1" placeholder="Texto a buscar...">
                        </div>
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="estadoVenta" @change="listarVenta(1,buscar,criterio,estadoVenta,tipo_fact)">
                                <option value="1">Facturado</option>
                                <option value="0">No facturado</option>
                            </select>
                            <button type="submit" @click="listarVenta(1,buscar,criterio,estadoVenta,tipo_fact)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>&nbsp;
                        <div class="input-group input-group-sm ml-xl-5">
                            <select class="form-control" id="tipofact" name="tipofact" v-model="tipo_fact" @change="listarVenta(1,buscar,criterio,estadoVenta,tipo_fact)">
                                <option value="Cliente">Cliente</option>
                                <option value="Publico General">Publico General</option>
                            </select>
                            <button class="btn btn-sm btn-info" type="button"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp; Tipo de facturación </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Atendió</th>
                                <th>Cliente</th>
                                <th>RFC</th>
                                <th>No° Comprobante</th>
                                <th>Fecha Hora</th>
                                <th>Total</th>
                                <th>Pagado</th>
                                <th>Forma de pago</th>
                                <th>Facturación</th>
                                <th>Facturado</th>
                                <template v-if="estadoVenta == 1">
                                    <th v-if="estadoVenta" >No° Factura</th>
                                </template>
                                <th>Factura Enviada</th>

                            </tr>
                        </thead>
                        <tbody v-if="arrayProject.length">
                            <tr v-for="project in arrayProject" :key="project.id">
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" @click="verProject(project.id)">
                                        <i class="icon-eye"></i>
                                    </button>&nbsp;
                                    <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfProject(project.id)">
                                        <i class="fa fa-file-pdf-o"></i>
                                    </button>&nbsp;
                                </td>
                                <td v-text="project.usuario"></td>
                                <td v-text="project.nombre"></td>
                                <td v-text="project.rfccliente"></td>
                                <td v-text="project.num_comprobante"></td>
                                <td v-text="project.registro"></td>
                                <td v-text="project.total"></td>
                                <template v-if="project.adeudo == 0">
                                    <td><span class="badge badge-success">100% Pagado</span></td>
                                </template>
                                <template v-else-if="project.total == project.adeudo">
                                    <td><span class="badge badge-danger">No Pagado</span></td>
                                </template>
                                <template v-else-if="(project.total - project.adeudo) < project.total">
                                    <td><span class="badge badge-warning">Pagado Parcialmente</span></td>
                                </template>
                                <td v-text="project.forma_pago"></td>
                                <td v-text="project.tipo_facturacion"></td>
                                <td class="text-center">
                                    <input type="checkbox" :id="'chk'+project.id" v-model="project.facturado"
                                        @change="cambiarFacturacion(project.id,project.facturado,project.num_comprobante)" >
                                    <template v-if="project.facturado">
                                         <label :for="'chk'+project.id">Facturado</label>
                                    </template>
                                    <template v-else>
                                        <label :for="'chk'+project.id">No Facturado</label>
                                    </template>
                                </td>
                                <template v-if="estadoVenta == 1">
                                    <td class="text-center" v-text="project.num_factura" ></td>
                                </template>
                                <td class="text-center">
                                    <input type="checkbox" :id="'chkEn'+project.id" v-model="project.factura_env"
                                        @change="cambiarFacturacionEnv(project.id,project.factura_env,project.num_comprobante)" :disabled="!project.facturado">
                                    <template v-if="project.factura_env">
                                         <label :for="'chkEn'+project.id">Enviada</label>
                                    </template>
                                    <template v-else>
                                        <label :for="'chkEn'+project.id">No Enviada</label>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="14" class="text-center">
                                    <strong>NO hay presupuestos con ese criterio o estado...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoVenta,tipo_fact)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoVenta,tipo_fact)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoVenta,tipo_fact)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!-- Fin Listado -->

         <!-- Ver Venta detalle -->
        <template v-else-if="listado==2">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h4>Cliente </h4>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-column">
                            <div class="p-0"><p><strong v-text="cliente"></strong></p></div>
                            <div class="p-0"><p><u v-text="tipo_cliente"></u></p></div>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-column">
                            <div class="p-0"><p> <strong>Teléfono:</strong> {{ telefono_cliente }}</p></div>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-column">
                            <div class="p-0">
                                <p> <strong>RFC:</strong> {{ rfc_cliente }}
                                <span style="color:red;" v-show="!rfc_cliente">(*Complete esta información)</span>
                                </p>
                            </div>
                            <div class="p-0">
                                <p> <strong>Uso CFDI: </strong> {{ cfdi_cliente }}
                                    <span style="color:red;" v-show="!cfdi_cliente">(*Complete esta información)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-column" v-if="contacto_cliente">
                            <div class="p-0"><p> <strong>Contacto:</strong> {{ contacto_cliente }}</p></div>
                            <div class="p-0"><p> <strong>Tel Contacto: </strong> {{ telcontacto_cliente }}</p></div>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante</strong></label>
                            <p v-text="tipo_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                            <label for=""><strong>Fecha Inicio:</strong></label>
                            <p v-text="inicio"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Fecha Compromiso:</strong></label>
                            <p v-text="fin"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Total del Proyecto:</strong></label>
                            <p v-text="total"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Impuesto</strong></label>
                            <p v-text="impuesto"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Titulo del Proyecto</strong></label>
                            <p v-text="title"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo de Facturación</strong></label>
                            <p v-text="tipo_facturacion"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Contenido</strong></label>
                            <p v-text="content"></p>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for=""><strong>Entregado:</strong> </label>
                                <td v-if="entregado">
                                    <span class="badge badge-success">100%</span>
                                </td>
                                <td v-else-if="entregado_parcial">
                                    <span class="badge badge-warning">Parcial</span>
                                </td>
                                <td v-else>
                                     <span class="badge badge-danger">No entregado</span>
                                </td>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for=""><strong>100% Pagado: </strong> </label>
                            <div v-if="estadoVn == 'Registrado'">
                                <toggle-button disabled v-model="btnPagado" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                            </div>
                            <div v-else>
                                <span class="badge badge-danger">Presupuesto cancelado</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Forma de pago</strong></label>
                            <p v-text="forma_pago"></p>
                        </div>
                    </div>
                    <div class="form-check float-left mr-2">
                        <input class="form-check-input" type="checkbox" id="chkInsta" v-model="instalacion">
                        <label class="form-check-label p-0 m-0" for="chkInsta"><strong>Instalación</strong></label>
                    </div>
                    <div class="form-check float-left">
                        <input class="form-check-input" type="checkbox" id="chkFlete" v-model="flete">
                        <label class="form-check-label p-0 m-0" for="chkFlete"><strong>Flete corre por cuenta del cliente</strong></label>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="col-12 col-md-6 d-flex flex-column">
                        <div class="form-group">
                            <h3><strong> Presupuestos </strong></h3>
                            <button class="btn btn-primary btn-sm float-right" v-if="idcliente" @click="seleccionarPresupuestos(idcliente)">
                                <i class="fa fa-search" aria-hidden="true"></i> Seleccionar Presupuestos
                            </button>
                        </div>
                        <div class="table-responsive" v-if="detallePresupuestos.length">
                            <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                                <thead>
                                    <tr>
                                        <th>No°</th>
                                        <th>Opciones</th>
                                        <th>No° Comprobante</th>
                                        <th>Total</th>
                                        <th>Cliente</th>
                                        <th>Fecha Hora</th>
                                        <th>Atendió</th>
                                    </tr>
                                </thead>
                                <tbody v-if="detallePresupuestos.length">
                                    <tr v-for="(venta,index) in detallePresupuestos" :key="venta.id">
                                        <td width="10px" v-text="index + 1"></td>
                                        <td>
                                            <button @click="eliminarDetallePresupuestos(index)" type="button" class="btn btn-danger btn-sm" v-if="usrol==1">
                                                <i class="icon-close"></i>
                                            </button>&nbsp;
                                            <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfVenta(venta.idventa)">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </button>&nbsp;
                                        </td>
                                        <td v-text="venta.num_comprobante"></td>
                                        <td v-text="venta.total"></td>
                                        <td v-text="venta.cliente"></td>
                                        <td>{{ convertDateVenta(venta.fecha_hora) }}</td>
                                        <td v-text="venta.usuario"></td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <strong>Aún no tienes ventas con este cliente...</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--<div class="form-group row border">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Detalles</th>
                                    <th>Código de material</th>
                                    <th>SKU</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Descuento </th>
                                    <th>Ubicacion</th>
                                    <th>SubTotal</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td>
                                        <button type="button" @click="abrirModal3(index)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;
                                    </td>
                                    <td v-text="detalle.sku"></td>
                                    <td v-text="detalle.codigo"></td>
                                    <td v-text="detalle.cantidad"></td>
                                    <td v-text="detalle.precio"></td>
                                    <td v-text="detalle.descuento"></td>
                                    <td v-text="detalle.ubicacion"></td>
                                    <td>{{ (( (detalle.precio * detalle.cantidad) - detalle.descuento)).toFixed(6) }}</td>
                                </tr>
                                 <tr style="background-color: #CEECF5;">
                                    <td colspan="12" align="right"><strong>Total Parcial:</strong></td>
                                    <td>$ {{total_parcial = (total / divImp).toFixed(6) }}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="12" align="right"><strong>Total Impuesto:</strong></td>
                                    <td>$ {{total_impuesto=((total * impuesto)/(divImp)).toFixed(6)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="12" align="right"><strong>Total Neto:</strong></td>
                                    <td>$ {{ total }} </td>
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
                </div>-->
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="exampleFormControlTextarea2">Observaciones</label>
                        <textarea class="form-control rounded-0" rows="3" maxlength="256" readonly v-model="observacion"></textarea>
                    </div>&nbsp;
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="">Lugar de entrega</label>
                            <p v-text="lugar_entrega"></p>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Tiempo de entrega</label>
                            <p v-text="tiempo_entrega"></p>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-4">
                        <label for="exampleFormControlTextarea2">Observaciones Internas</label>
                        <textarea class="form-control rounded-0" rows="3" maxlength="256" readonly v-model="observacionpriv"></textarea>
                    </div>&nbsp;
                    <!-- Files Upploader -->
                    <div class="col-md-6 mt-3" v-if="facturado == 1">
                        <div class="page-header">
                            <h3 id="timeline">Facturas  {{ num_comprobante }} &nbsp;</h3>
                        </div>
                        <div class="divdocs form-inline" v-if="docsArray.length">
                            <div v-for="file in docsArray" :key="file.id" class="d-flex justify-content-around">
                                <div>
                                    <template v-if="file.tipo != 'pdf'">
                                        <div class="d-flex justify-content-center">
                                            <div>
                                                <lightbox class="m-0" album="" :src="'facturasfiles/'+file.url">
                                                    <figure class="figure">
                                                        <img :src="'facturasfiles/'+file.url" width="150" height="100" class="figure-img img-fluid rounded" alt="File CAPTION">
                                                        <figcaption class="figure-caption text-right" v-text="file.url"></figcaption>
                                                    </figure>
                                                </lightbox>&nbsp;
                                            </div>
                                            <div>
                                                <button @click="eliminarFile(file.id,venta_id)" class="btn btn-transparent text-danger rounded-circle"><i class="fa fa-times fa-2x"></i></button>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="d-flex justify-content-center">
                                            <div>
                                                <figure class="figure">
                                                    <img @click="downloadDoc(file.url)" src="img/PDFICON.png" width="100" height="70" class="figure-img img-fluid rounded" alt="File CAPTION">
                                                    <figcaption class="figure-caption text-right" v-text="file.url"></figcaption>
                                                </figure>
                                            </div>
                                            <div>
                                                <button @click="eliminarFile(file.id,venta_id)" class="btn btn-transparent text-danger rounded-circle"><i class="fa fa-times fa-2x"></i></button>
                                                <button @click="sendMailFactura(venta_id,email_cliente,cliente,file.url)"
                                                    class="btn btn-transparent text-primary rounded-circle" v-if="email_cliente">
                                                    <i class="fa fa-share fa-2x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div v-else style="height: auto !important;">
                            <h5>Sin archivos adjuntos...</h5>
                        </div>
                        <hr>
                        <div>
                            <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Subir Archivos</label>
                                    <input type="file" class="form-control" placeholder="Subir Archivos"
                                        multiple accept="image/png,image/jpeg,image/jpg,application/pdf" @change="fieldChange">
                                </div>
                                    <button v-if="arrayFiles.length" @click="guardarFiles()" type="button" class="btn btn-sm btn-primary">Guardar</button>
                            </form>
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
        <!-- Fin ver Venta detalle-->
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>

    <!--Inicio del modal Visualizar articulo Insertado-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal2}" data-spy="scroll"  role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-info modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal + sku"></h4>
                    <button type="button" class="close" @click="cerrarModal2()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h1 class="text-center" v-text="sku"></h1>
                    <template v-if="file">
                        <lightbox class="m-0" album="" :src="'http://inventariostroystone.com/images/'+file">
                            <img class="img-responsive imgcenter" width="500px" :src="'http://inventariostroystone.com/images/'+file">
                        </lightbox>&nbsp;
                    </template>
                    <table class="table table-bordered table-striped table-sm text-center table-hover">
                        <thead>
                            <tr class="text-center">
                                <th class="text-center" colspan="2">Detalle del artículo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>NO° DE PLACA</strong></td>
                                <td v-text="codigo"></td>
                            </tr>
                            <tr>
                                <td><strong>MATERIAL</strong></td>
                                <td v-text="categoria"></td>
                            </tr>
                            <tr >
                                <td><strong>CODIGO DE MATERIAL</strong></td>
                                <td v-text="sku"></td>
                            </tr>
                            <tr >
                                <td><strong>TERMINADO</strong></td>
                                <td v-text="terminado"></td>
                            </tr>
                            <tr >
                                <td><strong>LARGO</strong></td>
                                <td v-text="largo"></td>
                            </tr>
                            <tr >
                                <td><strong>ALTO</strong></td>
                                <td v-text="alto"></td>
                            </tr>
                            <tr >
                                <td><strong>METROS<sup>2</sup> </strong></td>
                                <td v-text="calcularMts"></td>
                            </tr>
                            <tr >
                                <td><strong>ESPESOR</strong></td>
                                <td v-text="espesor"></td>
                            </tr>
                            <tr >
                                <td><strong>PRECIO</strong></td>
                                <td v-text="precio"></td>
                            </tr>
                            <tr >
                                <td><strong>BODEGA DE DESCARGA</strong></td>
                                <td v-text="ubicacion"></td>
                            </tr>
                            <tr >
                                <td><strong>Stock</strong></td>
                                <td v-text="stock"></td>
                            </tr>
                            <tr >
                                <td><strong>DESCRIPCION</strong></td>
                                <td v-text="descripcion"></td>
                            </tr>
                            <tr >
                                <td><strong>OBSERVACIONES</strong></td>
                                <td v-text="observacion"></td>
                            </tr>
                            <tr >
                                <td><strong>ORIGEN</strong></td>
                                <td v-text="origen"></td>
                            </tr>
                            <tr >
                                <td><strong>CONTENEDOR</strong></td>
                                <td v-text="contenedor"></td>
                            </tr>
                            <tr >
                                <td><strong>ESPESOR</strong></td>
                                <td v-text="espesor"></td>
                            </tr>
                            <tr >
                                <td><strong>FECHA DE LLEGADA</strong></td>
                                <td v-text="fecha_llegada"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <barcode :value="codigo" :options="{formar: 'EAN-13'}">
                                Sin código de barras.
                        </barcode>
                    </div>
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

    <!--Inicio del modal Visualizar articulo detalle listado==2-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal3}" data-spy="scroll"  role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-info modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal + sku"></h4>
                    <button type="button" class="close" @click="cerrarModal3()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h1 class="text-center" v-text="sku"></h1>
                    <template v-if="file">
                        <lightbox class="m-0" album="" :src="'http://inventariostroystone.com/images/'+file">
                            <img class="img-responsive imgcenter" width="500px" :src="'http://inventariostroystone.com/images/'+file">
                        </lightbox>&nbsp;
                    </template>
                    <div v-if="condicion == 1" class="text-center">
                        <span class="badge badge-success">Activo</span>
                    </div>
                    <div v-else-if="condicion == 3" class="text-center">
                        <span class="badge badge-warning">Cortado</span>
                    </div>
                    <div v-else class="text-center">
                        <span class="badge badge-danger">Desactivado</span>
                    </div>&nbsp;
                    <table class="table table-bordered table-striped table-sm text-center table-hover">
                        <thead>
                            <tr class="text-center">
                                <th class="text-center" colspan="2">Detalle del artículo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><strong>NO° DE PLACA</strong></td>
                            <td v-text="codigo"></td>
                        </tr>
                        <tr>
                            <td><strong>MATERIAL</strong></td>
                            <td v-text="categoria"></td>
                        </tr>
                        <tr >
                            <td><strong>CODIGO DE MATERIAL</strong></td>
                            <td v-text="sku"></td>
                        </tr>
                        <tr >
                            <td><strong>TERMINADO</strong></td>
                            <td v-text="terminado"></td>
                        </tr>
                        <tr >
                            <td><strong>LARGO</strong></td>
                            <td v-text="largo"></td>
                        </tr>
                        <tr >
                            <td><strong>ALTO</strong></td>
                            <td v-text="alto"></td>
                        </tr>
                        <tr >
                            <td><strong>METROS<sup>2</sup> </strong></td>
                            <td v-text="metros_cuadrados"></td>
                        </tr>
                        <tr >
                            <td><strong>ESPESOR</strong></td>
                            <td v-text="espesor"> </td>
                        </tr>
                        <tr >
                            <td><strong>PRECIO</strong></td>
                            <td v-text="precio"></td>
                        </tr>
                        <tr >
                            <td><strong>BODEGA DE DESCARGA</strong></td>
                            <td v-text="ubicacion"></td>
                        </tr>
                        <tr >
                            <td><strong>Cantidad</strong></td>
                            <td v-text="cantidad"></td>
                        </tr>
                        <tr >
                            <td><strong>DESCRIPCION</strong></td>
                            <td v-text="descripcion"></td>
                        </tr>
                        <tr >
                            <td><strong>OBSERVACIONES</strong></td>
                            <td v-text="observacion"></td>
                        </tr>
                        <tr >
                            <td><strong>ORIGEN</strong></td>
                            <td v-text="origen"></td>
                        </tr>
                        <tr >
                            <td><strong>CONTENEDOR</strong></td>
                            <td v-text="contenedor"></td>
                        </tr>
                        <tr >
                            <td><strong>ESPESOR</strong></td>
                            <td v-text="espesor"></td>
                        </tr>
                        <tr >
                            <td><strong>FECHA DE LLEGADA</strong></td>
                            <td v-text="fecha_llegada"></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <barcode :value="codigo" :options="{formar: 'EAN-13'}">
                                Sin código de barras.
                        </barcode>
                    </div>
                    <hr class="d-block d-sm-block d-md-none">
                    <div class="float-right d-block d-sm-block d-md-none">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal3()">Cerrar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal3()">Cerrar</button>
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
import vSelect from 'vue-select';
import VueBarcode from 'vue-barcode';
import VueLightbox from 'vue-lightbox';
import moment from 'moment';
import ToggleButton from 'vue-js-toggle-button'
import { data } from 'jquery';
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton);
export default {
    data() {
        return {
            idventa: 0,
            id_project: 0,
            cliente: '',
            idcliente:'',
            tipo_cliente : '',
            telefono_cliente : '',
            rfc_cliente: '',
            cfdi_cliente : '',
            contacto_cliente : '',
            telcontacto_cliente : '',
            user: '',
            tipo_comprobante:'',
            num_comprobante: "",
            impuesto: 0.16,
            descuento : 0,
            moneda : 'Peso Mexicano',
            tipo_cambio : 0,
            observacion : '',
            observacionpriv : '',
            categoria : '',
            idarticulo : 0,
            articulo : "",
            codigo: "",
            idcategoria :0,
            sku : '',
            terminado : '',
            largo : 0,
            alto : 0,
            metros_cuadrados : 0,
            espesor : 0,
            ubicacion : '',
            origen : '',
            contenedor : '',
            fecha_llegada : '',
            file : '',
            flete : 0,
            instalacion : 0,
            imagenMinatura : '',
            arrayCategoria : [],
            condicion : 0,
            precio_venta : 0,
            cantidad : 0,
            total_impuesto : 0.0,
            total_parcial : 0.0,
            divImp: 0.0,
            total: 0.0,
            title:'',
            forma_pago : "De contado",
            tiempo_entrega : "",
            lugar_entrega : "",
            precio: 0.0,
            entregado : 0,
            entregado_parcial : 0,
            tipo_facturacion : "",
            num_cheque : 0,
            fin: '',
            banco : "",
            pagado : 0,
            stock : 0,
            descripcion : "",
            arrayProject : [],
            arrayProjectT : [],
            arrayDetalle : [],
            arrayPresupuestosT:[],
            detallePresupuestos : [],
            listado : 1,
            modal: 0,
            modal2: 0,
            modal3: 0,
            inicio: '',
            ind : '',
            tituloModal: "",
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
            btnEntrega : false,
            btnPagado : false,
            estadoVn : "",
            estadoVenta : "0",
            facturado : 0,
            factura_env : 0,
            tipo_fact : "Cliente",
            usrol : 0,
            arrayFiles : [],
            docsArray : [],
            email_cliente : "",
            content: "",
        };
    },
    components: {
        vSelect,
        'barcode': VueBarcode
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
        },
        imagen(){
            return this.imagenMinatura;
        },
        calcularMts : function(){
            let me=this;
            let resultado = 0;
            resultado = resultado + (me.alto * me.largo);
            me.metros_cuadrados = resultado;
            return resultado;
        },
        metrosTotales : function(){
            let me=this;
            let resultado = 0;
            for(var i=0;i<me.arrayDetalle.length;i++){
                resultado += parseFloat(me.arrayDetalle[i].metros_cuadrados);
            }
            return resultado;
        }
    },
    methods: {
        listarVenta (page,buscar,criterio,estadoVenta,tipoFact){
            let me=this;
            var url= '/projectInvo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='+ estadoVenta + '&tipofact=' + tipoFact;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayProject = respuesta.projects.data;
                me.pagination= respuesta.pagination;
                me.usrol = respuesta.userrol;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio,estadoVenta,tipoFact){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarVenta(page,buscar,criterio,estadoVenta,tipoFact);
        },
        mostrarDetalle(){
            this.listado = 0;
            this.id_project = 0;
            this.idcliente = 0;
            this.nombre_cliente = "";
            this.rfc_cliente = "";
            this.tipo_cliente = "";
            this.telefono_cliente = "";
            this.contacto_cliente = "";
            this.telcontacto_cliente = "";
            this.obs_cliente= "";
            this.cfdi_cliente= "";
            this.idusuario = 0;
            this.num_comprobante = '';
            this.title = '';
            this.content = ''
            this.inicio = '';
            this.fin = '';$
            this.impuesto = 0.16;
            this.total = 0;
            this.adeudo = 0;
            this.forma_pago = 'Efectivo';
            this.lugar_entrega = '';
            this.estado = '';
            this.pagado = 0;
            this.pagado_parcial = 0;
            this.entregado = 0;
            this.entregado_parcial = 0;
            this.flete = 0;
            this.instalacion = 0;
            this.area = 'GDL';
            this.tipo_facturacion = '';
            this.observacion = '';
            this.observacionpriv = '';
            this.errorProject =0;
            this.errorMostrarMsjProject = [];
            this.num_comprobante = 0;
            this.arrayPresupuestosT = [];
            this.detallePresupuestos = [];
            this.editProjectCont = 0;
            this.arrayDepositos = [];
            this.editProjectCont = 0;
            this.arrayFiles = [];
            this.docsArray = [];
        },
        pdfVenta(id){
            window.open('/venta/pdf/'+id);
        },
        ocultarDetalle(){
            this.listado = 1;
            this.codigo = "";
            this.idarticulo = 0;
            this.articulo = "";
            this.sku = "";
            this.precio_venta = 0;
            this.precio = 0;
            this.cantidad = 0;
            this.file = '';
            this.ubicacion = '';
            this.moneda = 'Peso Mexicano';
            this.tipo_cambio = '0';
            this.stock = 0;
            this.cliente = 0;
            this.categoria = 0;
            this.observacion = "";
            this.observacionpriv = "";
            this.arrayDetalle = [];
            this.num_comprobante = 0;
            this.entregado = 0;
            this.entregado_parcial = 0;
            this.tipo_facturacion = "";
            this.num_cheque = 0;
            this.banco = "";
            this.pagado = 0;
            this.btnEntrega =  false;
            this.btnPagado = false;
            this.facturado = 0;
            this.tipo_cliente = '';
            this.telefono_cliente = '';
            this.rfc_cliente= '';
            this.cfdi_cliente = '';
            this.contacto_cliente = '';
            this.telcontacto_cliente = '';
            this.email_cliente = "";

        },
        convertDateVenta(date){
            let me=this;
            var datec = moment(date).format('MMM DD YYYY HH:mm:ss');
            return datec;
        },
        getPresupuestosCliente(idcliente,page,buscar,criterio){
            let me = this;
            var url= '/venta/getVentasClienteProject?idcliente=' + idcliente + '&page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayPresupuestosT = respuesta.ventas.data;
                me.paginationPre = respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        getVentasProject(id){
            let me = this;
            var url= '/project/getSales?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.detallePresupuestos = respuesta.ventas;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        verProject(id){
            let me = this;
            me.listado = 2;

            //Obtener los datos del ingreso
            var arrayVentaT=[];
            var url= '/project/obtenerCabecera?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                arrayVentaT = respuesta.projects;

                var total_parcial = 0;

                var fechaforma  = arrayVentaT[0]['inicio'];
                 var fechaform  = arrayVentaT[0]['fin'];

                me.project_id = arrayVentaT[0]['id'];
                me.cliente = arrayVentaT[0]['cliente'];
                me.rfc_cliente = arrayVentaT[0]['rfc'];
                me.tipo_cliente = arrayVentaT[0]['tipo'];
                me.contacto_cliente = arrayVentaT[0]['contacto'];
                me.telcontacto_cliente = arrayVentaT[0]['tel_contacto'];
                me.telefono_cliente = arrayVentaT[0]['telefono'];
                me.cfdi_cliente = arrayVentaT[0]['cfdi'];
                me.tipo_comprobante=arrayVentaT[0]['tipo_comprobante'];
                me.num_comprobante=arrayVentaT[0]['num_comprobante'];
                me.user=arrayVentaT[0]['usuario'];
                me.content = arrayVentaT[0]['content']
                me.impuesto = arrayVentaT[0]['impuesto'];
                me.total = arrayVentaT[0]['total'];
                me.flete = arrayVentaT[0]['flete']
                me.instalacion = arrayVentaT[0]['instalacion']
                me.title = arrayVentaT[0]['title']
                me.forma_pago = arrayVentaT[0]['forma_pago'];
                me.lugar_entrega = arrayVentaT[0]['lugar_entrega'];
                me.tiempo_entrega = arrayVentaT[0]['tiempo_entrega'];
                me.entregado = arrayVentaT[0]['entregado'];
                me.entregado_parcial =  arrayVentaT[0]['entrega_parcial'];
                me.observacion = arrayVentaT[0]['observacion'];
                me.observacionpriv = arrayVentaT[0]['observacionpriv'];
                me.estadoVn = arrayVentaT[0]['estado'];
                me.tipo_facturacion = arrayVentaT[0]['tipo_facturacion'];
                me.pagado = arrayVentaT[0]['pagado'];
                me.facturado = arrayVentaT[0]['facturado'];
                me.email_cliente =  arrayVentaT[0]['EmailC'];
                moment.locale('es');
                me.inicio=moment(fechaform).format('llll');
                me.fin=moment(fechaforma).format('llll');
                me.getVentasProject(id);

                 var imp =   parseFloat(me.impuesto = arrayVentaT[0]['impuesto']);


                me.divImp = imp + 1;

                if(me.entregado ==1){
                    me.btnEntrega = true;
                }

                if(me.pagado == 1){
                    me.btnPagado = true;
                }

            })
            .catch(function (error) {
                console.log(error);
            });

            //Obtener los detalles del ingreso
            /*var url= '/project/obtenerDetalles?id=';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayDetalle = respuesta.detalles;
            })
            .catch(function (error) {
                console.log(error);
            });


            this.getDocs(id);*/
        },
        abrirModal2(index){
            let me = this;
            me.ind = index;
            me.modal2 = 1;
            me.tituloModal      = "Detalles Artículo ";
            me.sku              = me.arrayDetalle[index]['articulo'];
            me.codigo           = me.arrayDetalle[index]['codigo'];
            me.ubicacion        = me.arrayDetalle[index]['ubicacion'];
            me.precio_venta     = me.arrayDetalle[index]['precio_venta'];
            me.stock            = me.arrayDetalle[index]['stock'];
            me.file             = me.arrayDetalle[index]['file'];
            me.descripcion      = me.arrayDetalle[index]['descripcion'];
            me.observacion      = me.arrayDetalle[index]['observacion'];
        },
        cerrarModal2() {
            this.modal2 = 0;
            this.sku = '';
            this.codigo = '';
            this.ubicacion = '';
            this.precio_venta = 0;
            this.stock = 0;
            this.file = '';
            this.observacion = '';
            this.descripcion = '';
            this.ind = '';
        },
        abrirModal3(index){
            let me = this;
            me.ind = index;
            me.modal3 = 1;
            me.tituloModal      = "Artículo ";
            me.sku              = me.arrayDetalle[index]['sku'];
            me.codigo           = me.arrayDetalle[index]['codigo'];
            me.precio           = me.arrayDetalle[index]['precio'];
            me.cantidad         = me.arrayDetalle[index]['cantidad'];
            me.file             = me.arrayDetalle[index]['file'];
            me.descripcion      = me.arrayDetalle[index]['descripcion'];
            me.observacion      = me.arrayDetalle[index]['observacion'];
            me.condicion        = me.arrayDetalle[index]['condicion'];

        },
        cerrarModal3() {
            this.modal3 = 0;
            this.sku = '';
            this.codigo = '';
            this.precio_venta = 0;
            this.stock = 0;
            this.file = '';
            this.descripcion = '';
            this.ind = '';
        },
        pdfProject(id){
            window.open('/project/pdf/'+id);
        },
        cambiarFacturacion(id,estado,pr){
            if(estado == true){
                Swal.fire({
                    title: 'Facturado',
                    text:  `Ingrese el número de factura del presupuesto ${ pr }`,
                    input: 'text',
                    inputValue : '',
                    position: 'center',
                    inputPlaceholder: 'Número de factura',
                    validationMessage : 'El número de factura es requerido',
                    inputAttributes: {
                        required: true
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Guardar',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: (data) => {
                    },
                    allowOutsideClick: () => Swal.isLoading()
                    }).then((result) => {
                    let numFact = result.value;
                    if (result.value) {
                        let me = this;
                        var factip = me.tipo_fact;
                        var pageac = me.pagination.current_page;
                        var estadovt = me.estadoVenta;
                        if(estado == true){
                            me.facturado = 1;
                        }else{
                            me.facturado = 0;
                        }
                        axios.put('/project/cambiarFacturacion',{
                            'id': id,
                            'estado' : this.facturado,
                            'numFact' : numFact
                        }).then(function (response) {
                            if(estado == 1){
                                swal.fire(
                                'Completado!',
                                'El presupuesto '+ pr + ' ha sido registrado con facturado.',
                                'success')
                            }else{
                                swal.fire(
                                'Atención!',
                                'El presupuesto '+ pr +' ha sido registrado como no facturado.',
                                'warning')
                            }
                            me.listarVenta(pageac,'','',estadovt,factip);
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }else if(result.dismiss === Swal.DismissReason.cancel){
                        this.listarVenta(pageac,'','',estadovt,factip);
                    }
                });
            }else{
                let me = this;
                var factip = me.tipo_fact;
                var pageac = me.pagination.current_page;
                var estadovt = me.estadoVenta;
                if(estado == true){
                    me.facturado = 1;
                }else{
                    me.facturado = 0;
                }
                axios.put('/project/cambiarFacturacion',{
                    'id': id,
                    'estado' : this.facturado,
                    'numFact' : ''
                }).then(function (response) {
                    if(estado == 1){
                        swal.fire(
                        'Completado!',
                        'El presupuesto '+ pr + ' ha sido registrado con facturado.',
                        'success')
                    }else{
                        swal.fire(
                        'Atención!',
                        'El presupuesto '+ pr +' ha sido registrado como no facturado.',
                        'warning')
                    }
                    me.listarVenta(pageac,'','',estadovt,factip);
                }).catch(function (error) {
                    console.log(error);
                });
            }

        },
        cambiarFacturacionEnv(id,estado,pr){
            /* console.log('Cambiar el estadoenv ' + estado + ' de la venta ' + id); */
            let me = this;

            var factip = me.tipo_fact;
            var pageac = me.pagination.current_page;
            var estadovt = me.estadoVenta;

            if(estado == true){
                me.factura_env = 1;
            }else{
                me.factura_env = 0;
            }

            axios.put('/project/cambiarFacturacionEnv',{
                'id': id,
                'estadoEn' : this.factura_env
            }).then(function (response) {
                if(estado == 1){
                    swal.fire(
                    'Completado!',
                    'La factura del presupuesto '+ pr + ' ha sido registrado con enviada.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'La factura del presupuesto '+ pr +' ha sido registrado como no enviada.',
                    'warning')
                }
                me.listarVenta(pageac,'','',estadovt,factip);
            }).catch(function (error) {
                console.log(error);
            });

        },
        eliminarFile(id,idventa){
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
                    axios.put('/ventaherramienta/eliminarDoc', {
                        'id' : id
                    }).then(function(response) {
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El documento ha sido eliminada con éxito.",
                            "success"
                        );
                        me.getDocs(idventa);
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        downloadDoc(file){
            window.open('facturasfiles/'+file);
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
            var venta = this.venta_id;
            axios.put('/project/filesupplo',{
                'id' : this.venta_id,
                'filesdata': this.arrayFiles
            }).then(function(response) {
                swal.fire(
                'Completado!',
                'Los archivos fueron guardados con éxito.',
                'success');
                me.arrayFiles = [];
                me.getDocs(venta);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        getDocs(idventa){
            let me = this;
            var url= '/project/getDocs?id=' + idventa;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.docsArray = respuesta.documentos;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        sendMailFactura(venta_id,email,cliente,url){
            let me = this;
            axios.post('/project/enviarFacturaMail',{
                'id'      : venta_id,
                'mail'    : email,
                'name'    : cliente,
                'fileUrl' : url
            }).then(function (response) {
                Swal.fire({
                    type: 'success',
                    title: 'Enviado...',
                    text: `La factura de ${ cliente } se envio correctamente al correo ${ email }`,
                })
            }).catch(function (error) {
                console.log(error);
            });
        }
    },
    mounted() {
        this.listarVenta(1,this.buscar, this.criterio,this.estadoVenta,this.tipo_fact);
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
    @media (min-width: 600px){
        .btnagregar{
            margin-top: 2rem;
        }
    }
    .selectDetalle {
        background: white;
        border: none;
        text-align: center;

  }
</style>
