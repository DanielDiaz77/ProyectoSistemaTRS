<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li @click="menu=0" class="nav-item">
                <a class="nav-link active" href="#"><i class="icon-speedometer"></i> Escritorio</a>
            </li>
            <li class="nav-title">
                Mantenimiento
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-bag"></i> Almacén</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=1" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i> Materiales</a>
                    </li>
                    <li @click="menu=2" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i> Artículos</a>
                    </li>
                    <li @click="menu=34" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i>Javas</a>
                    </li>
                    <li @click="menu=37" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i>Abrasivos</a>
                    </li>
                    <li @click="menu=48" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i>Bloques</a>
                    </li>
                    <li @click="menu=23" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-picture-o" aria-hidden="true"></i> Galería</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-bag"></i>Traslados</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=17" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-map-marker"></i> Traslados</a>
                    </li>
                    <li @click="menu=51" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-map-marker"></i> Traslado Javas</a>
                    </li>
                    <li @click="menu=52" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-map-marker"></i> Traslado Abrasivos</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-wallet"></i> Ingresos</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=3" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-wallet"></i> Ingresos</a>
                    </li>
                    <li @click="menu=4" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-notebook"></i> Proveedores</a>
                    </li>
                    <li @click="menu=35" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-wallet"></i>Ingreso Java</a>
                    </li>
                    <li @click="menu=39" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-wallet"></i>Ingreso Abrasivo</a>
                    </li>
                    <li @click="menu=55" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-wallet"></i>Ingreso Bloques</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-basket"></i> Ventas</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=22" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-basket-loaded"></i> Presupuesto Especial</a>
                    </li>
                    <li @click="menu=5" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-basket-loaded"></i> Presupuesto</a>
                    </li>
                    <li @click="menu=36" class="nav-item">
                        <a class="nav-link"  href="#"><i class="icon-basket-loaded"></i>Presupuesto Javas</a>
                    </li>
                    <li @click="menu=42" class="nav-item">
                        <a class="nav-link"  href="#"><i class="icon-basket-loaded"></i>Presupuesto Abrasivos</a>
                    </li>
                    <li @click="menu=13" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-list"></i> Cotizacion</a>
                    </li>
                    <li @click="menu=49" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-list"></i> Cotizacion Javas</a>
                    </li>
                    <li @click="menu=50" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-list"></i> Cotizacion Abrasivos</a>
                    </li>
                    <li @click="menu=40" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-list"></i> Cotizacion Especiales</a>
                    </li>
                    <li @click="menu=14" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-truck"></i> Entrega</a>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-pie-chart"></i> Consultas</a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=10" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-basket-loaded"></i> Pagos & Entregas</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-address-book"></i> Clientes</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=6" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-notebook"></i> Clientes</a>
                    </li>
                    <li @click="menu=15" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-tasks"></i> CRM</a>
                    </li>
                    <li @click="menu=21" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-sticky-note"></i> Recadero</a>
                    </li>
                    <li @click="menu=24" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-money" aria-hidden="true"></i> Notas de crédito</a>
                    </li>
                </ul>
            </li>
            <li @click="menu=56" class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-file-text-o"></i> Plantillas</a>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-desktop" aria-hidden="true"></i>Panel de Control</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=57" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i>Seccion de Reclamos</a>
                    </li>
                    <li @click="menu=43" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i>Articulos Editados</a>
                    </li>
                    <li @click="menu=44" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i>Articulos Con Detalle</a>
                    </li>
                    <li @click="menu=7" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user"></i> Usuarios</a>
                    </li>
                    <li @click="menu=8" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user-following"></i> Roles</a>
                    </li>
                </ul>
            </li>
            <li @click="menu=16" class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-calendar"></i> Actividades</a>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-file-text-o"></i>Facturas</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=18" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-file-text-o"></i>Facturación</a>
                    </li>
                    <li @click="menu=38" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-file-text-o"></i>Facturación Presupuestos E</a>
                    </li>
                    <li @click="menu=53" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-file-text-o"></i>Facturación Javas</a>
                    </li>
                    <li @click="menu=54" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-file-text-o"></i>Facturación Abrasivos</a>
                    </li>
                </ul>
            </li>
            <li @click="menu=20" class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-tasks"></i> Tareas
                    <template v-if="numActivities != 0">
                        <span class="badge badge-danger">@{{ numActivities }}</span>
                    </template>
                    <template v-else>
                        <span class="badge badge-success">0</span>
                    </template>
                </a>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-pie-chart"></i> Consultas</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=19" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-calendar"></i>Actividades</a>
                    </li>
                    <li @click="menu=9" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-chart"></i>Ingresos</a>
                    </li>
                    {{-- <li @click="menu=10" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-chart"></i>Presupuestos</a>
                    </li> --}}
                </ul>
            </li>
            {{-- <li @click="menu=11" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-book-open"></i> Ayuda <span class="badge badge-danger">PDF</span></a>
            </li>
            <li @click="menu=12" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-info"></i> Acerca de...<span class="badge badge-info">IT</span></a>
            </li> --}}

        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
