<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/">Escritorio</a> </li>
    </ol>
    <div class="container-fluid p-1">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> Presupuesto Especial
               <button type="button" @click="mostrarDetalle()" class="btn btn-secondary" v-if="listado==1 && usrol != 4">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
          <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary float-right" v-if="listado==2">Volver</button>
          <div class="form-inline ml-5 float-right" v-if="usrol== 1">
                <div class="input-group input-group-sm mt-1 mt-sm-0 ml-md-2 ml-lg-5" >
                    <button @click="abrirModal10()" class="btn btn-outline-success btn-sm">Abonos<i class="fa fa-file-excel-o"></i></button>
                </div>
          </div>
        </div>
        <!-- Listado -->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-11">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio">
                                <option value="cliente">Cliente</option>
                                <option value="num_comprobante">No° Comprobante</option>
                                <option value="fecha_hora">Fecha</option>
                                <option value="forma_pago">Forma de pago</option>
                            </select>
                            <input type="text" v-model="buscar" @keyup.enter="listarProject(1,buscar,criterio,estadoProj,entregaProj)" class="form-control mb-1" placeholder="Texto a buscar...">
                        </div>
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="estadoProj" @change="listarProject(1,buscar,criterio,estadoProj,entregaProj)">
                                <option value="">Todo</option>
                                <option value="Registradas">Activa</option>
                                <option value="Anuladas">Cancelada</option>
                            </select>
                            <button type="submit" @click="listarProject(1,buscar,criterio,estadoProj,entregaProj)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div class="input-group input-group-sm ml-sm-2 mr-sm-1 ml-md-2 ml-lg-5" v-if="estadoProj!='Anulada'">
                            <select class="form-control" id="tipofact" name="tipofact" v-model="entregaProj" @change="listarProject(1,buscar,criterio,estadoProj,entregaProj)">
                                <option value="">Todo</option>
                                <option value="completa">100%</option>
                                <option value="parcial">Parcial</option>
                                <option value="no_entregado">No entregado</option>
                            </select>
                            <button class="btn btn-sm btn-warning" type="button"><i class="fa fa-truck" aria-hidden="true"></i>&nbsp; Entregas </button>
                        </div>
                    </div>
                     <div class="input-group input-group-sm mt-1 mt-sm-0 ml-md-2 ml-lg-5" v-if="estadoProj!='Anuladas'">
                            <button @click="abrirModal5()" class="btn btn-success btn-sm">Reporte <i class="fa fa-file-excel-o"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Atendió</th>
                                <th>Área</th>
                                <th>Cliente</th>
                                <th>Tipo Comprobante</th>
                                <th>No° Comprobante</th>
                                <th>Fecha Compromiso</th>
                                <th>Valor del proyecto</th>
                                <th>Forma de pago</th>
                                <th>Facturación</th>
                                <th v-if="usrol == 1">Facturado</th>
                                <th>Entregado</th>
                                <th>100% Pagado</th>
                                <th>Estado</th>
                                <th>Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayProject.length">
                            <tr v-for="project in arrayProject" :key="project.id">
                                <td>
                                    <div class="form-inline">
                                        <button type="button" class="btn btn-success btn-sm" @click="verProject(project)">
                                        <i class="icon-eye"></i>
                                        </button>&nbsp;
                                        <template v-if="usrol == 1">
                                           <template v-if="project.total == project.adeudo">
                                                <button type="button" v-if="project.estado == 'Registrado'" class="btn btn-danger btn-sm" @click="desactivarProject(project.id)">
                                                    <i class="icon-trash"></i>
                                                </button>
                                           </template>
                                        </template>
                                        <button type="button" class="btn btn-outline-danger btn-sm" v-if="usrol != 4" @click="pdf(project.id)">
                                        <i class="fa fa-file-pdf-o"></i>
                                        </button>&nbsp;

                                    </div>
                                </td>
                                <td v-text="project.usuario"></td>
                                <td v-text="project.area"></td>
                                <td v-text="project.cliente"></td>
                                <td v-text="project.tipo_comprobante"></td>
                                <td v-text="project.num_comprobante"></td>
                                <td>{{ convertDateVenta(project.fin) }}</td>
                                <td v-text="project.total"></td>
                                <td v-text="project.forma_pago"></td>
                                <td v-text="project.tipo_facturacion"></td>
                                <template v-if="usrol == 1">
                                    <td v-if="project.facturado == 1">
                                        <span class="badge badge-success" v-if="project.tipo_facturacion == 'Cliente'">Facturado</span>
                                    </td>
                                    <td v-else-if="project.facturado == 0">
                                        <span class="badge badge-danger" v-if="project.tipo_facturacion == 'Cliente'"> No Facturado</span>
                                         <span class="badge badge-info" v-if="project.tipo_facturacion == 'Publico General'">Facturado General</span>
                                    </td>
                                </template>
                                <td v-if="project.entregado">
                                    <span class="badge badge-success">100%</span>
                                </td>
                                <td v-else-if="project.entregado_parcial">
                                    <span class="badge badge-warning">Parcial</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">No entregado</span>
                                </td>
                                <template v-if="project.adeudo == 0">
                                    <td><span class="badge badge-success">100% Pagado</span></td>
                                </template>
                                <template v-else-if="project.total == project.adeudo">
                                    <td><span class="badge badge-danger">No Pagado</span></td>
                                </template>
                                <template v-else-if="(project.total - project.adeudo) < project.total">
                                    <td><span class="badge badge-warning">Pagado Parcialmente</span></td>
                                </template>
                                <td v-if="project.estado =='Registrado'">
                                    <span class="badge badge-success">Activa</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">Cancelada</span>
                                </td>
                                <td>{{ convertDateVenta(project.registro) }}</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="13" class="text-center">
                                    <strong>NO hay presupuestos con ese criterio o estado...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoProj,entregaProj)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoProj,entregaProj)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoProj,entregaProj)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!-- Fin Listado -->

        <!-- Nuevo Project -->
        <template v-else-if="listado==0">
            <div class="card-body">
                <!-- Cliente -->
                <div class="form-group row border">
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Cliente (*)</strong></label>
                                <v-select :on-search="selectCliente" label="nombre" :options="arrayCliente" placeholder="Buscar clientes..." :onChange="getDatosCliente">
                                </v-select>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="nombre_cliente">
                        <div class="form-group">
                            <label for=""><strong>Cliente: </strong></label>
                            <h6 for=""><strong v-text="nombre_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="telefono_cliente">
                        <div class="form-group">
                            <label for=""><strong>Teléfono</strong></label>
                            <h6 for=""><strong v-text="telefono_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                     <div class="col-md-2 text-center sinpadding" v-if="tipo_cliente">
                        <div class="form-group">
                            <label for=""><strong>Tipo de cliente</strong></label>
                            <h6 for=""><strong v-text="tipo_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="rfc_cliente">
                        <div class="form-group">
                            <label for=""><strong>RFC</strong></label>
                            <h6 for=""><strong v-text="rfc_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="cfdi_cliente">
                        <div class="form-group">
                            <label for=""><strong>Uso del CFDI</strong></label>
                            <h6 for=""><strong v-text="cfdi_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="contacto_cliente">
                        <div class="form-group">
                            <label for=""><strong>Contacto</strong></label>
                            <h6 for=""><strong> {{contacto_cliente}} | {{telcontacto_cliente}}</strong></h6>
                        </div>
                    </div>&nbsp;

                    <div class="col-md-3 text-center sinpadding" v-if="obs_cliente">
                        <div class="form-group">
                            <label for=""><strong>Observaciones</strong></label>
                            <h6 for=""><strong> {{obs_cliente}}</strong></h6>
                        </div>
                    </div>&nbsp;
                </div>
                <div class="form-group row border">
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Número de presupuesto (*)</strong></label>
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
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Forma de pago </strong><span style="color:red;" v-show="forma_pago==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="forma_pago" v-if="otroFormPay == false">
                                <option value='' disabled>Seleccione la forma de pago</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Mixto">Mixto</option>
                            </select>
                            <div class="form-check float-left mt-1">
                                <input class="form-check-input" type="checkbox" id="chkOtherPay" v-model="otroFormPay">
                                <label class="form-check-label p-0 m-0" for="chkOtherPay"><strong>Otro</strong></label>
                            </div>
                            <textarea class="form-control rounded-0" rows="2" maxlength="256" v-model="forma_pago" v-if="otroFormPay == true"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Lugar de entrega </strong><span style="color:red;" v-show="lugar_entrega==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="lugar_entrega" v-if="otroLugEntr == false">
                                <option value='' disabled>Seleccione el lugar de entrega</option>
                                <option value="LAB TROYSTONE">LAB TROYSTONE</option>
                                <option value="LAB TROYSTONE S.L.P.">LAB TROYSTONE S.L.P.</option>
                                <option value="LAB TROYSTONE A.G.S.">LAB TROYSTONE A.G.S.</option>
                                <option value="LAB TROYSTONE P.U.E.B.L.A">LAB TROYSTONE P.B.A.</option>
                            </select>
                            <div class="form-check float-left mt-1">
                                <input class="form-check-input" type="checkbox" id="chkEntreOtro" v-model="otroLugEntr">
                                <label class="form-check-label p-0 m-0" for="chkEntreOtro"><strong>Otro</strong></label>
                            </div>
                            <!-- <input type="text" v-model="lugar_entrega" class="form-control col-md" placeholder="Escribe la dirección de entrega"/> -->
                            <textarea class="form-control rounded-0" rows="2" maxlength="256" v-model="lugar_entrega" v-if="otroLugEntr == true"></textarea>
                        </div>

                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo de facturación </strong><span style="color:red;" v-show="tipo_facturacion==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="tipo_facturacion">
                                <option value='' disabled>Seleccione el tipo de facturación</option>
                                <option value="Publico General">Publico General</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mt-3">
                        <div class="form-check float-left mr-2">
                            <input class="form-check-input" type="checkbox" id="chkInsta" v-model="instalacion">
                            <label class="form-check-label p-0 m-0" for="chkInsta"><strong>Instalación</strong></label>
                        </div>
                        <div class="form-check float-left">
                            <input class="form-check-input" type="checkbox" id="chkFlete" v-model="flete">
                            <label class="form-check-label p-0 m-0" for="chkFlete"><strong>Flete corre por cuenta del cliente</strong></label>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for="dateIn"><strong>Inicio </strong><span style="color:red;" v-show="inicio==''">(*Seleccione)</span></label>
                            <date-picker name="dateIn" id="dateIn" v-model="inicio" :config="optionsDP"></date-picker>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for="dateFn"><strong>Fecha Promesa </strong><span style="color:red;" v-show="fin==''">(*Seleccione)</span></label>
                            <date-picker name="dateFn" id="dateFn" v-model="fin" :config="optionsDP"></date-picker>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><i style="color:red;" class="fa fa-map-marker"></i><strong> Area</strong><span style="color:red;" v-show="area==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="area">
                                <option value='' disabled>Seleccione el area del proyecto</option>
                                <option value="GDL">Guadalajara</option>
                                <option value="SLP">San Luís Potosí</option>
                                <option value="AGS">Aguascalientes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><i style="color:green;" class="fa fa-money"></i><strong> Valor del proyecto</strong><span style="color:red;" v-show="total==''">(*Ingrese)</span></label>
                            <input type="number" step="any" class="form-control" v-model="total" placeholder="Escriba el valor del proyecto">
                        </div>
                    </div>

                    <div class="col-md-12 text-center">
                        <div v-show="errorProject" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjProject" :key="error" v-text="error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6 d-flex flex-column">
                        <h3><strong> Proyecto </strong></h3>
                        <div class="form-group">
                            <label for=""><strong> Nombre </strong><span style="color:red;" v-show="title==''">(*Ingrese)</span></label>
                            <input type="text" class="form-control" v-model="title" placeholder="Escriba el nombre del proyecto">
                        </div>
                        <div class="form-group">
                            <label for=""><strong> Descripción </strong></label>
                             <template class="justify-content-center">
                                <editor :options="editorOptions" mode="wysiwyg" v-model="content" Width="100%"/>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
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
                                            <button @click="eliminarDetallePresupuestos(index)" type="button" class="btn btn-danger btn-sm">
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
                <div class="form-group row d-flex justify-content-around">
                    <div class="col-12 col-md text-center">
                        <label for="exampleFormControlTextarea2"><strong>Observaciones</strong></label>
                        <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacion"></textarea>
                    </div>&nbsp;
                    <div class="col-12 col-md text-center">
                        <label for="exampleFormControlTextarea2"><strong>Observaciones Internas</strong></label>
                        <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacionpriv"></textarea>
                    </div>&nbsp;
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="registrarPresupuestoEspecial()">Registrar</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin Nuevo Project -->

         <!-- Ver Project -->
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
                        <div class="d-flex flex-column" v-if="tipo_facturacion == 'Cliente'">
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
                <div class="form-group row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante</strong></label>
                            <p v-text="tipo_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Número de Presupuesto</strong></label>
                            <p v-text="num_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" v-if="usrol != 4">
                            <label for=""><strong>Valor del Proyecto:</strong></label>
                            <p v-text="total"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Registrado por:</strong></label>
                            <p v-text="usuario"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Titular:</strong></label>
                            <p v-text="titular"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Fecha Compromiso:</strong></label>
                            <p> {{ convertDateVenta(fin)  }} </p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo de facturación</strong></label>
                            <p v-text="tipo_facturacion"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" v-if="usrol != 4">
                            <label for=""><strong>Forma de pago</strong></label>
                            <p v-text="forma_pago"></p>
                        </div>
                    </div>
                    <!-- Status Entregas -->
                    <template v-if="usrol != 1">
                         <div class="col-md-2" v-if="adeudo == 0  && entregado == 0">
                            <div class="form-group" v-if="usrol != 4">
                                <label for=""><strong>Entregado Parcial:</strong> </label>
                                <div v-if="pagado">
                                    <toggle-button @change="cambiarEstadoEntregaParcial(id_project)" v-model="btnEntregaParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Pendiente de pago</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!--Cambios en pagos solo Admin puede cambiar a no entregado-->
                        <div class="col-md-2" v-if="!btnEntrega && usrol != 1 ">
                            <div class="form-group" v-if="usrol != 4">
                                <label for=""><strong>Entregado 100%:</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoEntrega(id_project)" v-model="btnEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                            </div>
                        </div>
                         <div class="col-md-2" v-else-if=" usrol == 1">
                            <div class="form-group" v-if="usrol != 4">
                                <label for=""><strong>Entregado 100%:</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoEntrega(id_project)" v-model="btnEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                            </div>
                        </div>
                        <!--Fin de los cambios-->
                        <div class="col-md-2" v-if="!entregado">
                            <div class="form-group">
                                <label for=""><strong>Entregado Parcial:</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoEntregaParcial(id_project)" v-model="btnEntregaParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                            </div>
                        </div>
                    <!-- Status Pagos -->
                    <template v-if="usrol != 1">
                        <div class="col-md-2">
                            <div class="form-group" v-if="!pagado">
                                <label for=""><strong>Pagado Parcialmente: </strong> </label>
                                <div v-if="estado == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagadoParcial(id_project,adeudo)" v-model="btnPagadoParcial"
                                        :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" :disabled="pagado_parcial == 1"/>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!--Cambios en pagos solo Admin puede cambiar a no pagado 100%-->
                    <template v-if="usrol == 1">
                        <div class="col-md-2" v-if="!btnPagado && usrol != 1">
                            <div class="form-group">
                                <label for=""><strong>100% Pagado: </strong> </label>
                                <div v-if="estado == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagado(id_project)" v-model="btnPagado" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" v-else-if="usrol == 1">
                            <div class="form-group">
                                <label for=""><strong>100% Pagado: </strong> </label>
                                <div v-if="estado == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagado(id_project)" v-model="btnPagado" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!--Fin de los Cambios-->
                        <div class="col-md-2">
                            <div class="form-group" v-if="!pagado">
                                <label for=""><strong>Pagado Parcialmente: </strong> </label>
                                <div v-if="estado == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagadoParcial(id_project,adeudo)"
                                        v-model="btnPagadoParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" :disabled="pagado_parcial == 1"/>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>

                    <div :class="{'col-md-7': pagado_parcial,  'col-md-2': !pagado_parcial}" v-if="usrol != 4">
                        <template v-if="adeudo > 0 ">
                            <p class="float-right" style="font-size: 20px;"><span class="badge badge-danger">Adeudo: </span> {{ adeudo }}</p>
                        </template>
                        <template v-else>
                            <p class="float-right" style="font-size: 20px;"><span class="badge badge-success">Pagado al 100 %</span></p>
                        </template>
                    </div>
                    <div class="col-5" v-if="usrol != 4">
                        <div id="accordion" v-if="pagado_parcial" class="mb-2">
                            <div class="card m-0">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" style="text-decoration:none; color:#000;" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            <strong>Abonos</strong>
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body p-0">
                                        <button class="btn btn-primary btn-sm float-right m-2" @click="cambiarEstadoPagadoParcial(id_project,adeudo)" v-if="adeudo > 0">Nuevo</button>
                                        <div class="table-responsive col-12 p-0">
                                            <table class="table table-bordered table-striped table-sm table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="10px">No°</th>
                                                        <th width="20px" v-if="usrol == 1">Opciones</th>
                                                        <th>Forma de pago</th>
                                                        <th>Fecha</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody v-if="arrayDepositos.length">
                                                    <tr v-for="(deposito,index) in arrayDepositos" :key="deposito.id">
                                                        <td width="10px" v-text="index + 1"></td>
                                                        <td v-if="usrol == 1">
                                                            <button type="button" class="btn btn-light btn-sm" @click="deleteDeposit(deposito.id,id_project,deposito.total)">
                                                                <i class="icon-trash"></i>
                                                            </button> &nbsp;
                                                        </td>
                                                        <td v-text="deposito.forma_pago"></td>
                                                        <td>{{ convertDateVenta(deposito.fecha) }}</td>
                                                        <td v-text="deposito.total"></td>
                                                    </tr>
                                                    <tr v-if="usrol == 1" style="background-color: #CEECF5;" >
                                                        <td colspan="5" align="right"><strong>Abonado:</strong></td>
                                                        <td>$ {{ calcularAbonos }}</td>
                                                    </tr>
                                                    <tr v-else style="background-color: #CEECF5;" >
                                                        <td colspan="3" align="right"><strong>Abonado:</strong></td>
                                                        <td>$ {{ calcularAbonos }}</td>
                                                    </tr>

                                                    <tr v-if="usrol == 1" style="background-color: #CEECF5;" >
                                                        <td colspan="5" align="right"><strong>Adeudo:</strong></td>
                                                        <td>$ {{ adeudo }} </td>
                                                    </tr>
                                                    <tr v-else style="background-color: #CEECF5;" >
                                                        <td colspan="3" align="right"><strong>Adeudo:</strong></td>
                                                        <td>$ {{ adeudo }} </td>
                                                    </tr>
                                                </tbody>
                                                <tbody v-else>
                                                    <tr v-if="usrol == 1">
                                                        <td colspan="5" class="text-center">
                                                            <strong>NO hay abonos registrados...</strong>
                                                        </td>
                                                    </tr>
                                                    <tr v-else>
                                                        <td colspan="4" class="text-center">
                                                            <strong>NO hay abonos registrados...</strong>
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-between">
                        <div>
                            <div class="form-check float-left mr-2">
                                <input class="form-check-input" type="checkbox" id="chkInsta" v-model="instalacion" :disabled="!editProjectCont">
                                <label class="form-check-label p-0 m-0" for="chkInsta"><strong>Instalación</strong></label>
                            </div>
                            <div class="form-check float-left">
                                <input class="form-check-input" type="checkbox" id="chkFlete" v-model="flete" :disabled="!editProjectCont">
                                <label class="form-check-label p-0 m-0" for="chkFlete"><strong>Flete corre por cuenta del cliente</strong></label>
                            </div>
                        </div>
                        <div v-if="editProjectCont" class="d-inline-flex">
                            <div class="form-group mr-2">
                                <label for="dateIn"><strong>Inicio </strong><span style="color:red;" v-show="inicio==''">(*Seleccione)</span></label>
                                <date-picker name="dateIn" id="dateIn" v-model="inicio" :config="optionsDP"></date-picker>
                            </div>
                            <div class="form-group">
                                <label for="dateFn"><strong>Fecha Promesa </strong><span style="color:red;" v-show="fin==''">(*Seleccione)</span></label>
                                <date-picker name="dateFn" id="dateFn" v-model="fin" :config="optionsDP"></date-picker>
                            </div>
                        </div>
                        <div v-if="editProjectCont">
                            <div class="form-group" v-if="!pagado_parcial">
                                <label for=""><i style="color:green;" class="fa fa-money"></i><strong> Valor del proyecto</strong><span style="color:red;" v-show="total==''">(*Ingrese)</span></label>
                                <input type="number" step="any" class="form-control" v-model="total" placeholder="Escriba el valor del proyecto">
                            </div>
                        </div>
                        <div v-if="estado == 'Registrado' && usrol != 4">
                            <button type="button" @click="editarProject()" class="btn btn-primary float-right" v-if="!editProjectCont">
                                <i class="fa fa-pencil"></i>&nbsp;Editar
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Mostrar estatico -->
                <div class="form-group row" v-if="!editProjectCont">
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column">
                            <div>
                                <h4 v-text="title"></h4>
                            </div>
                            <div>
                                <viewer :value="content"/>
                            </div>

                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column">
                            <div><h3>Presupuestos seleccionados</h3></div>
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
                                                <strong>Aún no tienes ventas seleccionadas para este presupuesto...</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- Mostrar para edición -->
                <div class="form-group row" v-else>

                    <div class="col-12 col-md-6 d-flex flex-column">
                        <h3><strong> Proyecto </strong></h3>
                        <div class="form-group">
                            <label for=""><strong> Nombre </strong><span style="color:red;" v-show="title==''">(*Ingrese)</span></label>
                            <input type="text" class="form-control" v-model="title" placeholder="Escriba el nombre del proyecto">
                        </div>
                        <div class="form-group">
                            <label for=""><strong> Descripción </strong></label>
                             <template class="justify-content-center">
                                <editor :options="editorOptions" mode="wysiwyg" v-model="content" Width="100%"/>
                            </template>
                        </div>
                    </div>
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
                    <div class="col-12" v-if="estado == 'Registrado'">
                        <button type="button" @click="refreshProject(id_project)" class="btn btn-secondary float-right ml-2">
                            &nbsp;Cancelar
                        </button>
                        <button type="button" @click="actualizarProject()" class="btn btn-primary float-right">
                            <i class="fa fa-refresh"></i>&nbsp;Actualizar
                        </button>
                    </div>
                </div>
                <!-- Files Upploader -->
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="page-header">
                            <h3 id="timeline">Archivos adjuntos &nbsp;</h3>
                        </div>
                        <div class="divdocs" v-if="docsArray.length">
                            <div v-for="file in docsArray" :key="file.id" class="d-flex justify-content-around">
                                <div>
                                    <template v-if="file.tipo != 'pdf'">
                                        <div class="d-flex justify-content-center">
                                            <div>
                                                <lightbox class="m-0" album="" :src="'projectfiles/'+file.url">
                                                    <figure class="figure">
                                                        <img :src="'projectfiles/'+file.url" width="150" height="100" class="figure-img img-fluid rounded" alt="File CAPTION">
                                                        <figcaption class="figure-caption text-right" v-text="file.url"></figcaption>
                                                    </figure>
                                                </lightbox>&nbsp;
                                            </div>
                                            <div>
                                                <button @click="eliminarFile(file.id,id_project)" class="btn btn-transparent text-danger rounded-circle"><i class="fa fa-times fa-2x"></i></button>
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
                                                <button @click="eliminarFile(file.id,id_project)" class="btn btn-transparent text-danger rounded-circle"><i class="fa fa-times fa-2x"></i></button>
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
                    <div class="col-12 col-md">
                        <div class="row">
                            <div class="col">
                                <label for="exampleFormControlTextarea2"><strong>Observaciones</strong></label>
                            </div>
                            <div class="col-2">
                                <template v-if="obsEditable == 0">
                                    <button type="button" class="btn btn-warning btn-sm float-right" @click="editObservacion()">
                                        <i class="icon-pencil"></i>
                                    </button>
                                </template>
                                <template v-else>
                                    <button type="button" class="btn btn-primary btn-sm float-right" @click="actualizarObservacion(id_project)">
                                        <i class="fa fa-floppy-o"></i>
                                    </button>
                                </template>&nbsp;

                            </div>
                        </div>&nbsp;
                        <template v-if="obsEditable == 0">
                            <textarea class="form-control rounded-0" rows="3" maxlength="256" readonly v-model="observacion"></textarea>
                        </template>
                        <template v-else>
                            <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacion"></textarea>
                        </template>
                    </div>&nbsp;
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label for=""><strong>Lugar de entrega</strong></label>
                            <p v-text="lugar_entrega"></p>
                        </div>
                    </div>&nbsp;
                    <div class="col-12 col-md">
                        <div class="row">
                            <div class="col">
                                <label for="exampleFormControlTextarea2"><strong>Observaciones Internas</strong></label>
                            </div>
                            <div class="col-2">
                                <template v-if="obsprivEditable == 0">
                                    <button type="button" class="btn btn-warning btn-sm float-right" @click="editObservacionPriv()">
                                        <i class="icon-pencil"></i>
                                    </button>
                                </template>
                                <template v-else>
                                    <button type="button" class="btn btn-primary btn-sm float-right" @click="actualizarObservacionPriv(id_project)">
                                        <i class="fa fa-floppy-o"></i>
                                    </button>
                                </template>&nbsp;
                            </div>
                        </div>&nbsp;
                        <template v-if="obsprivEditable == 0">
                            <textarea class="form-control rounded-0" rows="3" maxlength="256" readonly v-model="observacionpriv"></textarea>
                        </template>
                        <template v-else>
                            <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacionpriv"></textarea>
                        </template>
                    </div>&nbsp;
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin ver Project-->
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
     <!--Inicio del modal listar ventas-->
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
                    <!-- Filtros Modal ventas -->
                    <div class="form-group row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <select class="form-control" v-model="criterioPre">
                                    <option value="cliente">Cliente</option>
                                <option value="num_comprobante">No° Comprobante</option>
                                <option value="fecha_hora">Fecha</option>
                                <option value="forma_pago">Forma de pago</option>
                                </select>
                                <input type="text" v-model="buscarPre" @keyup.enter="getPresupuestosCliente(idcliente,1,buscarPre,criterioPre)" class="form-control" placeholder="Texto a buscar">
                                 <button type="submit" @click="getPresupuestosCliente(idcliente,1,buscarPre,criterioPre)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                            <thead>
                                <tr>
                                    <th>Comprobante</th>
                                    <th>Atendió</th>
                                    <th>Cliente</th>
                                    <th>No° Comprobante</th>
                                    <th>Fecha Hora</th>
                                    <th>Total</th>
                                    <th>100% Pagado</th>
                                    <th>P. Especial</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayPresupuestosT.length">
                                <tr v-for="venta in arrayPresupuestosT" :key="venta.id">
                                    <td>
                                        <button type="button" @click="agregarPresupuesto(venta)" class="btn btn-success btn-sm">
                                            <i class="icon-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfVenta(venta.id)">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </button>&nbsp;
                                    </td>
                                    <td v-text="venta.usuario"></td>
                                    <td v-text="venta.nombre"></td>
                                    <td v-text="venta.num_comprobante"></td>
                                    <td>{{ convertDateVenta(venta.fecha_hora) }}</td>
                                    <td v-text="venta.total"></td>
                                    <td v-if="venta.pagado">
                                        <toggle-button :value="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                    </td>
                                    <td v-else>
                                        <toggle-button :value="false" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                    </td>
                                    <td class="text-center">
                                        <span v-if="venta.special" class="badge badge-success">Si</span>
                                        <span v-else class="badge badge-danger">No</span>
                                    </td>
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
                    <!-- Paginacion MODAL -->
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="paginationPre.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaPre(idcliente,paginationPre.current_page - 1,buscarPre,criterioPre)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumberPre" :key="page" :class="[page == isActivedPre ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaPre(idcliente,page,buscarPre,criterioPre)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="paginationPre.current_page < paginationPre.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaPre(idcliente,paginationPre.current_page + 1,buscarPre,criterioPre)">Sig</a>
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

    <!-- Modal crear abono -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal2}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-deposit">
                <div class="modal-body ">
                    <h3 class="mb-3">Adeudo actual: {{ adeudo }}</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <h5 for=""><strong>Forma de pago </strong><span style="color:red;" v-show="forma_pagoab==''">(*Seleccione)</span></h5>
                                <select class="form-control" v-model="forma_pagoab" v-if="otroFormPayab == false">
                                    <option value='' disabled>Seleccione la forma de pago</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Mixto">Mixto</option>
                                </select>
                                <div class="form-check float-left mt-1">
                                    <input class="form-check-input" type="checkbox" id="chkOtherPayab" v-model="otroFormPayab">
                                    <label class="form-check-label p-0 m-0" for="chkOtherPayab"><strong>Otro</strong></label>
                                </div>
                                <input class="form-control rounded-0"  maxlength="35" placeholder="Ingresa la forma de pago" v-model="forma_pagoab" v-if="otroFormPayab == true">
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <h5 for=""><strong> $ Abono: </strong></h5>
                            <input type="number" step="any" min="1" class="form-control" v-model="totalab">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-primary mr-2" @click="saveDeposit(id_project,adeudo,totalab)">Guardar</button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModal2(id_project)">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin Modal crear abono -->

    <!-- Modal Seleccionar tipo de abono -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal3}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-askdep">
                <div class="modal-body ">
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-3">
                            <h3 class="text-center">Selecciona la forma de pago</h3>
                            <div class="justify-content-center d-flex mt-5">
                                <button type="button" class="btn btn-primary mr-2" @click="pagoCredit(id_project,adeudo,idcliente)">Nota de crédito</button>
                                <button type="button" class="btn btn-primary" @click="pagoOtro(id_project,adeudo)">Otros</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 mb-0">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal3(id_project)">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Modal Seleccionar tipo de abono -->

    <!-- Modal crear abono con nota credito -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal4}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content content-creditPay">
                <div class="modal-body">
                    <h3 class="mb-3">Adeudo actual: {{ adeudo }}</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-2">
                            <h4>Notas de crédito : </h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>No° de Nota</th>
                                            <th>Monto</th>
                                            <th>Forma de pago</th>
                                            <th>Fecha</th>
                                            <th>Observaciones</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayCreditos.length">
                                        <tr v-for="credito in arrayCreditos" :key="credito.id">
                                            <td v-if="credito.estado == 'Vigente'">
                                                <button type="button" class="btn btn-success btn-sm" @click="addDetalleCredito(credito)">
                                                    <i class="icon-plus"></i>
                                                </button>&nbsp;
                                            </td>
                                            <td v-text="credito.num_documento"></td>
                                            <td v-text="credito.total"></td>
                                            <td v-text="credito.forma_pago"></td>
                                            <td>{{ convertDateVenta(credito.fecha_hora) }}</td>
                                            <td v-text="credito.observacion"></td>
                                            <td v-text="credito.estado"></td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <strong>Aún no tienes notas de credito con este cliente...</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item" v-if="paginationcred.current_page > 1">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaCre(idcliente,paginationcred.current_page - 1)">Ant</a>
                                    </li>
                                    <li class="page-item" v-for="page in pagesNumberCre" :key="page" :class="[page == isActivedCre ? 'active' : '']">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaCre(idcliente,page)" v-text="page"></a>
                                    </li>
                                    <li class="page-item" v-if="paginationcred.current_page < paginationcred.last_page">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaCre(idcliente,paginationcred.current_page + 1)">Sig</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12">
                            <template v-if="selectedCredits.length"><h5>Notas de seleccionadas: </h5></template>
                            <template v-else><h5 style="color:red;">Selecciona al menos una nota de crédito: </h5></template>
                            <div class="form-inline" v-if="selectedCredits.length">
                                <div v-for="(credit,index) in selectedCredits" :key="credit.id" class="d-flex justify-content-around mr-1">
                                    <table class="table table-bordered table-striped table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Opciones</th>
                                                <th>No° de Nota</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                 <td width="10px">
                                                    <button @click="eliminarDetalleCredito(index)" type="button" class="btn btn-danger btn-sm">
                                                        <i class="icon-close"></i>
                                                    </button>&nbsp;
                                                </td>
                                                <td v-text="credit.num_documento"></td>
                                                <td v-text="credit.total"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="form-group">
                                <h5 for=""><strong>Forma de pago </strong></h5>
                                <select class="form-control" v-model="forma_pagoab">
                                    <option value="Nota de crédito">Nota de crédito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <h5 for=""><strong> $ Abono: </strong></h5>
                            <input type="number" step="any" min="1" class="form-control" disabled :value="TotalAbonoCredito">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-primary mr-2"
                                @click="guardarAbonoCredit(id_project,adeudo,totalab)" v-if="selectedCredits.length"> Guardar
                            </button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModal4(id_project)">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin Modal crear abono con nota credito -->

     <!-- Modal exportar excel -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal5}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-success modal-md " role="document">
            <div class="modal-content content-exportUs">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ tituloModal }}</h5>
                    <button type="button" class="close" @click="cerrarModal5()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body ">
                   <!--  <h4 class="mb-3"> Generar reporte de presupuestos especiales</h4> -->
                    <div class="row d-flex justify-content-center">
                        <div class="input-group input-group-sm col-12 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Usuarios</span>
                            </div>
                            <v-select multiple v-model="selectedUsers" :on-search="selectReceptor" label="nombre" :options="arrayReceptores" placeholder="Buscar usuarios...">
                            </v-select>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Inicio: </strong></label>
                            <input type="date" class="form-control" v-model="fecha1">
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Fin: </strong></label>
                           <input type="date" class="form-control" v-model="fecha2">
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        <div>
                            <button type="button" class="btn btn-primary mr-5" @click="listarExcel(fecha1,fecha2,selectedUsers)">Generar</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal5()">Cerrar</button>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin exportar excel -->
    <!-- Modal exportar excel -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal10}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-success modal-md " role="document">
            <div class="modal-content content-exportUs">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal10()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body ">
                    <h3 class="mb-3">Generar reporte de Abonos</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Inicio: </strong></label>
                            <!-- <date-picker name="date" v-model="fecha1" class="form-control" :config="options"></date-picker> -->
                            <input type="date" class="form-control" v-model="fecha1">
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Fin: </strong></label>
                           <!--  <date-picker name="date" v-model="fecha2" :config="options"></date-picker> -->
                           <input type="date" class="form-control" v-model="fecha2">
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <div>
                            <button type="button" class="btn btn-primary mr-5" @click="listarAbonosExcel(fecha1,fecha2)">Reporte</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal10()">Cerrar</button>
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
import vSelect from 'vue-select';
import VueLightbox from 'vue-lightbox';
import moment from 'moment';
import ToggleButton from 'vue-js-toggle-button';
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import 'tui-editor/dist/tui-editor.css';
import 'tui-editor/dist/tui-editor-contents.css';
import 'codemirror/lib/codemirror.css';
import 'highlight.js/styles/github.css';
import Editor from '@toast-ui/vue-editor/src/Editor.vue';
import { Viewer } from '@toast-ui/vue-editor';
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton);
Vue.use(datePicker);
export default {
    data() {
        return {
            id_project : 0,
            idcliente : 0,
            nombre_cliente : "",
            rfc_cliente: "",
            tipo_cliente : "",
            telefono_cliente : "",
            contacto_cliente : "",
            telcontacto_cliente : "",
            obs_cliente: "",
            cfdi_cliente: "",
            idusuario : 0,
            usuario : '',
            facturado: 0,
            titular:"",
            num_comprobante : '',
            title : '',
            content : '',
            inicio : '',
            fin : '',
            impuesto : 0.16,
            total : 0,
            adeudo : 0,
            forma_pago : 'Efectivo',
            lugar_entrega : '',
            estado : '',
            pagado : 0,
            pagado_parcial : 0,
            entregado : 0,
            entregado_parcial : 0,
            flete : 0,
            instalacion : 0,
            area : 'GDL',
            tipo_facturacion : '',
            observacion : '',
            observacionpriv : '',
            listado : 1,
            arrayCliente : [],
            errorProject: 0,
            errorMostrarMsjProject: [],
            otroLugEntr : false,
            otroFormPay : false,
            usrol : 0,
            optionsDP: {
                format: 'YYYY-MM-DD HH:mm:ss',
                useCurrent: false,
                showClear: true,
                showClose: true,
                daysOfWeekDisabled: [0],
                minDate : moment().startOf('month').format('YYYY-MM-DD hh:mm'),
                maxDate: moment().add(60, 'days'),
                locale : moment.locale('es'),
            },
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
                ]
            },
            modal : 0,
            modal2 : 0,
            modal3: 0,
            modal4: 0,
            modal5: 0,
            modal10:0,
            tituloModal: "",
            arrayPresupuestosT : [],
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            arrayProject : [],
            criterio : 'num_comprobante',
            buscar : '',
            estadoProj : 'Registradas',
            entregaProj : '',
            paginationPre : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            criterioPre : 'num_comprobante',
            buscarPre : '',
            detallePresupuestos : [],
            btnEntrega : false,
            btnEntregaParcial : false,
            btnPagado : false,
            btnPagadoParcial : false,
            obsEditable : 0,
            obsprivEditable : 0,
            arrayDepositos : [],
            editProjectCont : 0,
            arrayFiles : [],
            docsArray : [],
            forma_pagoab : "",
            otroFormPayab : false,
            totalab : 0,
            arrayCreditos : [],
            paginationcred : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            selectedCredits : [],
            fecha1 : "",
            fecha2 : "",
            arrayReceptores : [],
            selectedUsers : []
        };
    },
    components: {
        vSelect,
        'editor': Editor,
        'viewer': Viewer
    },
    computed:{
        getFechaCode : function(){
            let me = this;
            let date = "";
            moment.locale('es');
            date = moment().format('YYMMDD');
            me.CodeDate = moment().format('YYMMDD');
            return date;
        },
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
        isActivedPre: function(){
            return this.paginationPre.current_page;
        },
        pagesNumberPre: function() {
            if(!this.paginationPre.to) {
                return [];
            }

            var from = this.paginationPre.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.paginationPre.last_page){
                to = this.paginationPre.last_page;
            }

            var pagesArray = [];
            while(from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        isActivedCre: function(){
            return this.paginationcred.current_page;
        },
        pagesNumberCre: function() {
            if(!this.paginationcred.to) {
                return [];
            }

            var from = this.paginationcred.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.paginationcred.last_page){
                to = this.paginationcred.last_page;
            }

            var pagesArray = [];
            while(from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        calcularAbonos : function(){
            let me=this;
            let resultado = 0;
            for(var i=0;i<me.arrayDepositos.length;i++){
                resultado += parseFloat(me.arrayDepositos[i].total);
            }
            return resultado;
        },
        TotalAbonoCredito : function(){
            let me=this;
            let resultado = 0;
            for(var i=0;i<me.selectedCredits.length;i++){
                resultado += parseFloat(me.selectedCredits[i].total);
            }
            me.totalab = resultado;
            return resultado;
        }
    },
    methods: {
        listarProject(page,buscar,criterio,estadoProj,entregaProj){
            let me=this;
            var url= '/project?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='+ estadoProj + '&estadoEntrega=' + entregaProj;
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
        cambiarPagina(page,buscar,criterio,estadoVenta,entregaProj){
            let me = this;
            me.pagination.current_page = page;
            me.listarProject(page,buscar,criterio,estadoVenta,entregaProj);
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
        selectCliente(search,loading){
            let me=this;
            loading(true)
            var url= '/cliente/selectCliente?filtro='+search;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                q: search
                me.arrayCliente=respuesta.clientes;
                loading(false)
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        getDatosCliente(val1){
            let me = this;
            me.loading = true;
            me.idcliente = val1.id;
            me.nombre_cliente = val1.nombre;
            me.rfc_cliente = val1.rfc;
            me.tipo_cliente = val1.tipo;
            me.telefono_cliente = val1.telefono;
            me.contacto_cliente = val1.company;
            me.telcontacto_cliente = val1.tel_company;
            me.obs_cliente = val1.observacion;
            me.cfdi_cliente =  val1.cfdi;

        },
        ocultarDetalle(){
            this.listado = 1;
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
            this.num_comprobanteocultarDetalle = 0;
            this.arrayPresupuestosT = [];
            this.detallePresupuestos = [];
            this.listarProject(this.pagination.current_page,this.buscar,this.criterio,this.estadoProj,this.entregaProj);

        },
        seleccionarPresupuestos(id){
            this.modal = 1;
            this.tituloModal = `Seleccionar presupuestos para el proyecto ${ this.title }`;
            this.getPresupuestosCliente(id,this.paginationPre.current_page,this.buscarPre,this.criterioPre);
        },
        cerrarModal(){
            this.modal = 0,
            this.criterioPre = 'num_comprobante';
            this.buscarPre = '';
            this.arrayPresupuestosT = [];
            this.paginationPre = {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            };

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
        convertDateVenta(date){
            let me=this;
            var datec = moment(date).format('MMM DD YYYY HH:mm:ss');
            return datec;
        },
        cambiarPaginaPre(idcliente,page,buscar,criterio){
            let me = this;
            //Actualiza la página actual
            me.paginationPre.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.getPresupuestosCliente(idcliente,page,buscar,criterio);
        },
        agregarPresupuesto(data = []){
            let me=this;
            if(me.encuentra(data['id'])){
                Swal.fire({
                    type: 'error',
                    title: 'Lo siento...',
                    text: 'Este Presupuesto ya esta en el listado!!',
                })
            }
            else{
                me.detallePresupuestos.push({
                    idventa         : data['id'],
                    usuario         : data['usuario'],
                    cliente         : data['nombre'],
                    num_comprobante : data['num_comprobante'],
                    fecha_hora      : data['fecha_hora'],
                    total           : data['total'],
                    pagado          : data['pagado']
                });
                Swal.fire({
                    type: 'success',
                    title: 'Añadido...'
                })
            }
        },
        encuentra(id){
            var sw=0;
            for(var i=0;i<this.detallePresupuestos.length;i++){
                if(this.detallePresupuestos[i].idventa==id){
                    sw=true;
                }
            }
            return sw;
        },
        pdf(id){
            window.open('/project/pdf/'+id);
        },
        pdfVenta(id){
            window.open('/venta/pdf/'+id);
        },
        eliminarDetallePresupuestos(index){
            let me = this;
            me.detallePresupuestos.splice(index,1);
        },
        registrarPresupuestoEspecial(){
            if (this.validarPresupuesto()) {
                return;
            }
            let me = this;
            var flet = 0;
            var insta = 0;


            var numcomp = "PS-".concat(me.CodeDate,"-",me.num_comprobante);

            var ArrPresupuestos = [];
            for(let i = 0; i < this.detallePresupuestos.length; i++){
                ArrPresupuestos.push(this.detallePresupuestos[i]['idventa']);
            }
            console.log(ArrPresupuestos);

            if(this.flete == true){
                flet = 1;
            }

            if(this.instalacion == true){
                insta = 1;
            }

            axios.post('/project/registrar',{
                'idcliente': this.idcliente,
                'tipo_comprobante': 'PresupuestoEspecial',
                'num_comprobante' : numcomp,
                'title' : this.title,
                'content' : this.content,
                'inicio' : this.inicio,
                'fin' : this.fin,
                'impuesto' : this.impuesto,
                'total' : this.total,
                'forma_pago' : this.forma_pago,
                'lugar_entrega' : this.lugar_entrega,
                'flet' : flet,
                'insta' : insta,
                'area' : this.area,
                'tipo_facturacion' : this.tipo_facturacion,
                'observacion' : this.observacion,
                'observacionpriv' : this.observacionpriv,
                'presupuestos': ArrPresupuestos

            }).then(function(response) {
                Swal.fire({
                    type: 'success',
                    title: 'Añadido...',
                    text : 'Presupuesto especial registrado con éxito'
                });
                me.ocultarDetalle();
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        validarPresupuesto() {
            let me = this;
            me.errorProject = 0;
            me.errorMostrarMsjProject = [];

            if (me.idcliente==0) me.errorMostrarMsjProject.push("Seleccione un cliente");
            if (me.title==0) me.errorMostrarMsjProject.push("Ingrese el título del presupuesto.");
            if (!me.num_comprobante) me.errorMostrarMsjProject.push("Ingrese el numero de comprobante");
            if (!me.impuesto) me.errorMostrarMsjProject.push("Ingrese el impuesto");
            /* if (me.arrayDetalle.length<=0) me.errorMostrarMsjProject.push("Introdusca articulos para la venta"); */
            if (me.errorMostrarMsjProject.length) me.errorProject = 1;

            return me.errorProject;
        },
        verProject(data = []){
            //console.log(data);
            this.id_project = data['id'];
            this.tipo_comprobante = data['tipo_comprobante'];
            this.num_comprobante = data['num_comprobante'];
            this.title = data['title'];
            this.content = data['content'];
            this.inicio = data['inicio'];
            this.fin =  data['fin'];
            this.impuesto = data['impuesto'];
            this.total = data['total'];
            this.adeudo = data['adeudo'];
            this.forma_pago = data['forma_pago'];
            this.lugar_entrega = data['lugar_entrega'];
            this.estado = data['estado'];
            this.pagado = data['pagado'];
            this.pagado_parcial = data['pagado_parcial'];
            this.entregado = data['entregado'];
            this.entregado_parcial = data['entregado_parcial'];
            this.flete = data['flete'];
            this.instalacion = data['instalacion'];
            this.area = data['area'];
            this.tipo_facturacion = data['tipo_facturacion'];
            this.observacion = data['observacion'];
            this.observacionpriv = data['observacionpriv'];
            this.usuario = data['usuario'];
            this.idcliente =  data['idcliente'];
            this.cliente = data['cliente'];
            this.tipo_cliente = data['tipo'];
            this.telefono_cliente = data['telefono'];
            this.rfc_cliente = data['rfc'];
            this.cfdi_cliente = data['cfdi'];
            this.contacto_cliente = data['company'];
            this.telcontacto_cliente = data['tel_company'];
            this.titular = data['titular'];
            this.getVentasProject(data['id']);
            this.listado = 2;
            this.editProjectCont = 0;
            this.getDocs(data['id']);


            if(this.entregado == 1){
                this.btnEntrega = true;
                this.entregado_parcial = 0;
            }

            if(this.entregado_parcial == 1){
                this.btnEntregaParcial = true;
                this.entregado = 0;

            }

            if(this.pagado ==1){
                this.btnPagado = true;
                this.btnPagadoParcial = false;
            }

            if(this.pagado_parcial == 1){
                this.btnPagadoParcial = true;
                this.getDeposits(this.id_project);
            }else{
                this.btnPagadoParcial = false;
            }
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
        editObservacion(){
            let me = this;
            me.obsEditable = 1;
        },
        editObservacionPriv(){
            let me = this;
            me.obsprivEditable = 1;
        },
        actualizarObservacion(id){
            let me = this;
            axios.post('/project/actualizarObservacion',{
                'id': id,
                'observacion' : this.observacion
            }).then(function (response) {
                me.obsEditable = 0;
                me.refreshProject(id);
            }).catch(function (error) {
                console.log(error);
            });
        },
        actualizarObservacionPriv(id){
            let me = this;
            axios.post('/project/actualizarObservacionPriv',{
                'id': id,
                'observacionpriv' : this.observacionpriv
            }).then(function (response) {
                me.obsprivEditable = 0;
                me.refreshProject(id);
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoPagado(id){
          let me = this;
            if(me.btnPagado == true){
                me.pagado = 1;
                me.btnPagadoParcial = false;
            }else{
                me.pagado = 0;
                me.btnEntrega = false;
                //me.cambiarEstadoEntrega(id);
            }
            axios.post('/project/cambiarPagado',{
                'id': id,
                'pagado' : this.pagado
            }).then(function (response) {
                if(me.pagado == 1){
                    swal.fire(
                    'Completado!',
                    'El presupuesto ha sido marcado como pagado con éxito.',
                    'success');
                }else{
                    swal.fire(
                    'Atención!',
                    'El presupuesto se registro como pendiente de pago.',
                    'warning');
                }
                me.refreshProject(id);
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoEntrega(id){
            let me = this;
            if(me.btnEntrega == true){
                me.entregado = 1;
            }else{
                me.entregado = 0;
            }
            axios.post('/project/cambiarEntrega',{
                'id': id,
                'entregado' : this.entregado
            }).then(function (response) {
                if(me.entregado == 1){
                    swal.fire(
                    'Completado!',
                    'El presupuesto ha sido registrado con entregado al 100%.',
                    'success');
                }else{
                    swal.fire(
                    'Atención!',
                    'El presupuesto ha sido registrado como no entregado.',
                    'warning');
                }
                me.refreshProject(id);
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoEntregaParcial(id){
            let me = this;
            if(me.btnEntregaParcial == true){
                me.entregado_parcial = 1;
            }else{
                me.entregado_parcial = 0;
            }
            axios.post('/project/cambiarEntregaParcial',{
                'id': id,
                'entregado_parcial' : this.entregado_parcial
            }).then(function (response) {
                if(me.entregado_parcial == 1){
                    swal.fire(
                    'Completado!',
                    'El presupuesto ha sido registrado con entrega parcial.',
                    'success');
                }else{
                    swal.fire(
                    'Atención!',
                    'El presupuesto ha sido registrado como no entregado.',
                    'warning');
                }
                me.refreshProject(id);
            }).catch(function (error) {
                console.log(error);
            });
        },
        abrirModal10(){
            this.modal10 = 1;
            this.tituloModal = "Generar Reporte de Abonos";
        },
        cerrarModal10(){
            this.modal10 = 0;
            this.tituloModal = "";
            this.fecha1 = "";
            this.fecha2 = "";
        },
        listarAbonosExcel(inicio, fin){
              window.open('/project/AbonoProjectExportExcel?inicio=' + inicio + '&fin=' + fin);
        },
        desactivarProject(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de anular este presupuesto?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/project/desactivar',{
                        'id': id
                    }).then(function (response) {
                        swal.fire(
                        'Completado!',
                        'El presupuesto ha sido anulado con éxito.',
                        'success');
                        me.estadoProj = 'Anuladas';
                        me.listarProject(me.pagination.current_page,me.buscar,me.criterio,'Anuladas','');
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        getDeposits(id){
            let me = this;
            var url= '/project/getDeposits?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayDepositos = respuesta.abonos;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        deleteDeposit(id,idproject,total){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de eliminar este abono?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/project/eliminarDeposit',{
                        'id': id,
                        'idproject' : idproject,
                        'total' : total
                    }).then(function (response) {
                        me.refreshProject(idproject);
                        swal.fire(
                        'Eliminado!',
                        'El abono ha sido eliminado con éxito.',
                        'success');
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                    this.refreshProject(idproject);
                }
            })
        },
        cerrarModal2(id){
            this.modal2 = 0;
            this.forma_pagoab = '';
            this.refreshProject(id);
            this.otroFormPayab =  false;
        },
        saveDeposit(id,adeudo,total){
            let abono = parseFloat(total);
            if(this.forma_pagoab == ''){
                swal.fire(
                'Atención!',
                'Ingrese la forma de pago.',
                'error');
            }else{
                if(abono > adeudo){
                    swal.fire(
                    'Error!',
                    'El abono no puede ser mayor que el adeudo.',
                    'error');
                    this.totalab = 0;
                }else{
                    let me = this;
                    axios.post('/project/crearDeposit',{
                        'id' : id,
                        'total' : abono,
                        'forma_pago' : this.forma_pagoab
                    }).then(function(response) {
                        me.modal2 = 0;
                        me.forma_pagoab = '';
                        me.otroFormPayab =  false;
                        me.refreshProject(id);
                        swal.fire(
                        'Completado!',
                        'El abono ha sido registrado con éxito.',
                        'success');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
                }

            }
        },
        refreshProject(id){
            let me = this;
            var url= '/project/refreshProject?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                var FreshProj = respuesta.project;
                me.verProject(FreshProj);
                me.usrol = respuesta.userrol;
                me.getDeposits(me.id_project);
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        editarProject(){
            this.editProjectCont = 1;
        },
        actualizarProject(){

            if (this.validarPresupuesto()) {
                return;
            }
            let me = this;
            var flet = 0;
            var insta = 0;


            var numcomp = "PS-".concat(me.CodeDate,"-",me.num_comprobante);

            var ArrPresupuestos = [];
            for(let i = 0; i < this.detallePresupuestos.length; i++){
                ArrPresupuestos.push(this.detallePresupuestos[i]['idventa']);
            }

            var ArrDeposits = [];
            for(let i = 0; i < this.arrayDepositos.length; i++){
                ArrDeposits.push(this.arrayDepositos[i]['idventa']);
            }
            /* console.log(ArrPresupuestos); */

            if(this.flete == true){
                flet = 1;
            }

            if(this.instalacion == true){
                insta = 1;
            }

            axios.put('/project/actualizar',{
                'id' : this.id_project,
                'idcliente': this.idcliente,
                'tipo_comprobante': 'PresupuestoEspecial',
                'num_comprobante' : this.num_comprobante,
                'title' : this.title,
                'content' : this.content,
                'titular' : this.titular,
                'inicio' : this.inicio,
                'fin' : this.fin,
                'impuesto' : this.impuesto,
                'total' : this.total,
                'adeudo' : this.adeudo,
                'forma_pago' : this.forma_pago,
                'lugar_entrega' : this.lugar_entrega,
                'flet' : flet,
                'insta' : insta,
                'area' : this.area,
                'tipo_facturacion' : this.tipo_facturacion,
                'observacion' : this.observacion,
                'observacionpriv' : this.observacionpriv,
                'depositos' : ArrDeposits,
                'presupuestos': ArrPresupuestos

            }).then(function(response) {
                Swal.fire({
                    type: 'success',
                    title: 'Añadido...',
                    text : 'Presupuesto especial actualizado con éxito'
                });
                me.refreshProject(me.id_project);
                me.getDeposits(me.id_project);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        eliminarFile(id,idproject){
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
                    axios.put('/project/eliminarDoc', {
                        'id' : id
                    }).then(function(response) {
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El documento ha sido eliminada con éxito.",
                            "success"
                        );
                        me.getDocs(idproject);
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        downloadDoc(file){
            window.open('projectfiles/'+file);
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
            var project = this.id_project;
            axios.put('/project/filesupplo',{
                'id' : this.id_project,
                'filesdata': this.arrayFiles
            }).then(function(response) {
                swal.fire(
                'Completado!',
                'Los archivos fueron guardados con éxito.',
                'success');
                me.arrayFiles = [];
                me.getDocs(project);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        getDocs(idproject){
            let me = this;
            var url= '/project/getDocs?id=' + idproject;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.docsArray = respuesta.documentos;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoPagadoParcial(id,adeudo){
            this.modal3 = 1;
            this.tituloModal = 'Crear Abono';
            this.forma_pagoab = '';
            this.totalab = 0;
        },
        pagoOtro(id,adeudo){
            this.modal3 = 0;
            this.modal2 = 1;
            this.tituloModal = 'Crear Abono';
            this.forma_pagoab = '';
            this.totalab = 0;
        },
        pagoCredit(id,adeudo,idcliente){
            this.modal3 = 0;
            this.modal4 = 1;
            this.tituloModal = 'Crear Abono';
            this.forma_pagoab = 'Nota de crédito';
            this.totalab = 0;
            this.getCredits(idcliente,1);
        },
        getCredits(id,page){
            let me = this;
            var url= '/cliente/getCreditsPay?id=' + id + '&page=' + page;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayCreditos = respuesta.creditos.data;
                me.paginationcred = respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPaginaCre(id,page){
            let me = this;
            me.pagination.current_page = page;
            me.getCredits(id,page);
        },
        cerrarModal3(){
            this.modal3 = 0;
            if(this.pago_parcial == 0)
                this.btnPagadoParcial =  false;
            else
                this.btnPagadoParcial = true;

        },
        addDetalleCredito(data =[]){
            let me=this;
            if(me.encuentraCredit(data['id'])){
                Swal.fire({
                    type: 'error',
                    title: 'Lo siento...',
                    text: 'Esta Nota de crédito ya esta seleccionada!!',
                })
            }
            else{
                me.selectedCredits.push({
                    idcredit         : data['id'],
                    num_documento    : data['num_documento'],
                    total            : data['total'],
                });
            }
        },
        encuentraCredit(id){
            var sw=0;
            for(var i=0;i<this.selectedCredits.length;i++){
                if(this.selectedCredits[i].idcredit==id){
                    sw=true;
                }
            }
            return sw;
        },
        eliminarDetalleCredito(index){
            let me = this;
            me.selectedCredits.splice(index,1);
        },
        guardarAbonoCredit(id,adeudo,total){
            let abono = parseFloat(total);
            if(abono > adeudo){
                swal.fire(
                'Error!',
                'El abono no puede ser mayor que el adeudo.',
                'error');
                this.totalab = 0;
            }else{
                let me = this;

                var ArrCredits = [];
                for(let i = 0; i < this.selectedCredits.length; i++){
                    ArrCredits.push(this.selectedCredits[i]['idcredit']);
                }

                axios.post('/project/crearDepositCredit',{
                    'id'         : id,
                    'total'      : abono,
                    'forma_pago' : this.forma_pagoab,
                    'creditos'   : ArrCredits
                }).then(function(response) {
                    me.cerrarModal4();
                    me.refreshProject(id);
                    swal.fire(
                    'Completado!',
                    'El abono ha sido registrado con éxito.',
                    'success');
                })
                .catch(function(error) {
                    console.log(error);
                });
            }
        },
        cerrarModal4(id){
            this.modal4 = 0;
            this.forma_pagoab = '';
            this.refreshProject(id);
            this.otroFormPayab =  false;
            this.arrayCreditos = [];
            this.selectedCredits = [];
            this.totalab = 0;
        },
        abrirModal5(){
            this.modal5 = 1;
            this.tituloModal = "Generar Reporte de proyectos especiales";
        },
        cerrarModal5(){
            this.modal5 = 0;
            this.tituloModal = "";
            this.fecha1 = "";
            this.fecha2 = "";
            this.arrayReceptores = [];
            this.selectedUsers = [];
        },
        selectReceptor(){
            let me=this;
            var url= '/user/selectUsuario';

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayReceptores = respuesta.usuarios;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        listarExcel(inicio, fin,selectedUsers){
            if(!selectedUsers.length){
                swal.fire(
                'Atencion!',
                `Seleccione los usuarios para el reporte`,
                'error');
            }else{
                var ArrUsuarios = [];
                for(let i = 0; i < selectedUsers.length; i++){
                    ArrUsuarios.push(selectedUsers[i]['id']);
                }
                window.open('/project/ExportExcel?inicio=' + inicio + '&fin=' + fin + '&usuarios=' + ArrUsuarios);
            }
        },
    },
    mounted() {
        this.listarProject(1,this.buscar,this.criterio,this.estadoProj,this.entregaProj);
    }
};
</script>

<style>
@media only screen and (max-width: 440px) {}


/* Extra small devices (phones, 600px and down) */

@media only screen and (min-width: 440px) {
    .modal-lg {
        max-width: 95% !important;
    }
}


/* Extra small devices (phones, 600px and down) */

@media only screen and (max-width: 576px) {
    .modal-lg {
        max-width: 95% !important;
    }
}


/* Medium devices (landscape tablets, 768px and up) */

@media only screen and (min-width: 768px) {
    .modal-lg {
        max-width: 90% !important;
    }
}


/* Large devices (laptops/desktops, 992px and up) */

@media only screen and (min-width: 992px) {
    .modal-lg {
        max-width: 90% !important;
    }
}


/* Extra large devices (large laptops and desktops, 1200px and up) */

@media only screen and (min-width: 1200px) {
    .modal-lg {
        max-width: 80% !important;
    }
}

</style>
