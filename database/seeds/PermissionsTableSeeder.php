<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Area Comercial
         */
        Permission::create([
            'name'          =>  'Mostrar Área Comercial (menu)',
            'slug'          =>  'acomercial.menu',
            'description'   =>  'Muesta la sección de Área comercial en el menu del sistema.',
            'section' => 'menu'
        ]);
        // Cotizaciones
        Permission::create([
            'name'          =>  'Ver Cotizaciones',
            'slug'          =>  'cotizaciones.show',
            'description'   =>  'Lista y Muestra cotizaciones del sistema.',
            'section' => 'acomercial',
            'section_2' => 'cotizaciones'
        ]);
        Permission::create([
            'name'          =>  'Crear Cotizaciones',
            'slug'          =>  'cotizaciones.create',
            'description'   =>  'Crea Cotizaciones en el sistema.',
            'section' => 'acomercial',
            'section_2' => 'cotizaciones'
        ]);
        Permission::create([
            'name'          =>  'Editar Cotizaciones',
            'slug'          =>  'cotizaciones.edit',
            'description'   =>  'Edita Cotizaciones del sistema.',
            'section' => 'acomercial',
            'section_2' => 'cotizaciones'
        ]);
        Permission::create([
            'name'          =>  'Eliminar Cotizaciones',
            'slug'          =>  'cotizaciones.delete',
            'description'   =>  'Elimina Cotizaciones del sistema.',
            'section' => 'acomercial',
            'section_2' => 'cotizaciones'
        ]);
        // Permission::create([
        //     'name'          =>  'Detalle de Cotizaciones (PDF)',
        //     'slug'          =>  'cotizaciones.pdf',
        //     'description'   =>  'Muestra el detalle en formato PDF de cotiizaciones del sistema.',
        //     'section' => 'acomercial',
        //     'section_2' => 'cotizaciones'
        // ]);
        Permission::create([
            'name'          =>  'Convertir a Comprobante',
            'slug'          =>  'cotizaciones.convert',
            'description'   =>  'Convierte una cotización en comprobante.',
            'section' => 'acomercial',
            'section_2' => 'cotizaciones'
        ]);
        Permission::create([
            'name'          =>  'Enviar a cliente',
            'slug'          =>  'cotizaciones.send',
            'description'   =>  'Envia una cotización a un Cliente.',
            'section' => 'acomercial',
            'section_2' => 'cotizaciones'
        ]);
        //Ventas
        Permission::create([
            'name'          =>  'Ver Ventas',
            'slug'          =>  'ventas.show',
            'description'   =>  'Lista y Muestra las Ventas del Sistema.',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
        Permission::create([
            'name'          =>  'Crea Boleta de venta',
            'slug'          =>  'ventas.boletacreate',
            'description'   =>  'Crea boletas de ventas en el sistema.',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
        Permission::create([
            'name'          =>  'Crea Factura de venta',
            'slug'          =>  'ventas.facturacreate',
            'description'   =>  'Crea facturas de ventas en el sistema.',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
        Permission::create([
            'name'          =>  'Generar Anulación o Comunicación de Baja',
            'slug'          =>  'ventas.lows',
            'description'   =>  'Genera Anulaciones o Comunicaciones de Baja',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
        Permission::create([
            'name'          =>  'Generar Notas de Crédito',
            'slug'          =>  'ventas.creditnote',
            'description'   =>  'Genera Notas de Crédito a un comprobante',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
        Permission::create([
            'name'          =>  'Generar Notas de Débito',
            'slug'          =>  'ventas.debitnote',
            'description'   =>  'Genera Notas de Débito a un comprobante',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
        Permission::create([
            'name'          =>  'Cambiar Forma de Pago',
            'slug'          =>  'ventas.cambiarformapago',
            'description'   =>  'Cambia la forma de pago un comprobante',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
        Permission::create([
            'name'          =>  'Aplicar Bonificaciones',
            'slug'          =>  'ventas.bonificaciones',
            'description'   =>  'Permite aplicar bonificaciones en una venta',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
        //Clientes
        Permission::create([
            'name'          =>  'Ver Clientes',
            'slug'          =>  'clientes.show',
            'description'   =>  'Lista y muestra los clientes del sistema.',
            'section' => 'acomercial',
            'section_2' => 'clientes'
        ]);
        Permission::create([
            'name'          =>  'Crear Clientes',
            'slug'          =>  'clientes.create',
            'description'   =>  'Crea clientes del sistema.',
            'section' => 'acomercial',
            'section_2' => 'clientes'
        ]);
        Permission::create([
            'name'          =>  'Importa Clientes',
            'slug'          =>  'clientes.import',
            'description'   =>  'Importa clientes al sistema.',
            'section' => 'acomercial',
            'section_2' => 'clientes'
        ]);
        Permission::create([
            'name'          =>  'Exporta Clientes',
            'slug'          =>  'clientes.export',
            'description'   =>  'Exporta clientes del sistema.',
            'section' => 'acomercial',
            'section_2' => 'clientes'
        ]);
        Permission::create([
            'name'          =>  'Editar Clientes',
            'slug'          =>  'clientes.edit',
            'description'   =>  'Edita clientes del sistema.',
            'section' => 'acomercial',
            'section_2' => 'clientes'
        ]);
        Permission::create([
            'name'          =>  'Eliminar Clientes',
            'slug'          =>  'clientes.delete',
            'description'   =>  'Elimina clientes del sistema.',
            'section' => 'acomercial',
            'section_2' => 'clientes'
        ]);
        Permission::create([
            'name'          =>  'Ver Reporte de Ventas',
            'slug'          =>  'reportesventas.show',
            'description'   =>  'Muestra y consulta los reportes de ventas',
            'section' => 'acomercial',
            'section_2' => 'reportes'
        ]);
        Permission::create([
            'name'          =>  'Ver Reporte Diario',
            'slug'          =>  'reportediario.show',
            'description'   =>  'Muestra y consultar reporte diario',
            'section' => 'acomercial',
            'section_2' => 'reportes'
        ]);
        Permission::create([
            'name'          =>  'Ver Reporte de Ingresos',
            'slug'          =>  'reporteingresos.show',
            'description'   =>  'Muestra y consultar reporte de ingresos',
            'section' => 'acomercial',
            'section_2' => 'reportes'
        ]);
        /**
         * Area Logistiica
         */
        Permission::create([
            'name'          =>  'Mostrar Área Logística (menu)',
            'slug'          =>  'alogistica.menu',
            'description'   =>  'Muesta la sección de Área Logística en el menu del sistema.',
            'section' => 'menu'
        ]);
        //Proveedores
        Permission::create([
            'name'          =>  'Mostrar Proveedores (Menu)',
            'slug'          =>  'proveedores.submenu',
            'description'   =>  'Muestra la seccion de proveedores en el menu del sistema.',
            'section' => 'alogistica',
            'section_2' => 'proveedores'
        ]);
        Permission::create([
            'name'          =>  'Ver Proveedores',
            'slug'          =>  'proveedores.show',
            'description'   =>  'Lista y muestra los proveedores en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'proveedores'
        ]);
        Permission::create([
            'name'          =>  'Crear Proveedores',
            'slug'          =>  'proveedores.create',
            'description'   =>  'Crea proveedores en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'proveedores'
        ]);
        Permission::create([
            'name'          =>  'Importar Proveedores',
            'slug'          =>  'proveedores.importar',
            'description'   =>  'Importa proveedores al sistema.',
            'section' => 'alogistica',
            'section_2' => 'proveedores'
        ]);
        Permission::create([
            'name'          =>  'Exportar Proveedores',
            'slug'          =>  'proveedores.export',
            'description'   =>  'Exporta proveedores del sistema.',
            'section' => 'alogistica',
            'section_2' => 'proveedores'
        ]);
        Permission::create([
            'name'          =>  'Editar Proveedor',
            'slug'          =>  'proveedores.edit',
            'description'   =>  'Edita proveedores del sistema.',
            'section' => 'alogistica',
            'section_2' => 'proveedores'
        ]);
        Permission::create([
            'name'          =>  'Eliminar Proveedor',
            'slug'          =>  'proveedores.delete',
            'description'   =>  'Elimina proveedores del sistema.',
            'section' => 'alogistica',
            'section_2' => 'proveedores'
        ]);
        Permission::create([
            'name'          =>  'Ver Propuestas',
            'slug'          =>  'propuestas.show',
            'description'   =>  'Lista y muestra las propuestas de proveedores en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'proveedores'
        ]);
        //Compras
        Permission::create([
            'name'          =>  'Ver Compras',
            'slug'          =>  'compras.show',
            'description'   =>  'Lista y navega las compras en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'compras'
        ]);
        Permission::create([
            'name'          =>  'Generar Compras Nuevas',
            'slug'          =>  'compras.new',
            'description'   =>  'Genera compras nuevas en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'compras'
        ]);
        Permission::create([
            'name'          =>  'Registrar Compra Fisica',
            'slug'          =>  'compras.fisica',
            'description'   =>  'Registra compras fisicas en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'compras'
        ]);
        Permission::create([
            'name'          =>  'Registrar Compra Electronica',
            'slug'          =>  'compras.electronica',
            'description'   =>  'Registra compras electronicas en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'compras'
        ]);
        Permission::create([
            'name'          =>  'Exportar Compras',
            'slug'          =>  'compras.export',
            'description'   =>  'Exporta todas las compras del sistema.',
            'section' => 'alogistica',
            'section_2' => 'compras'
        ]);
        // Ordenes de compra
        Permission::create([
            'name'          =>  'Ver Ordenes de Compra',
            'slug'          =>  'ocompra.show',
            'description'   =>  'Lista y navega las ordenes de compra del sistema.',
            'section' => 'alogistica',
            'section_2' => 'ordencompras'
        ]);
        Permission::create([
            'name'          =>  'Enviar Orden de Compra',
            'slug'          =>  'ocompra.send',
            'description'   =>  'Envia ordenes de compra a un proveedor.',
            'section' => 'alogistica',
            'section_2' => 'ordencompras'
        ]);
        Permission::create([
            'name'          =>  'Editar Orden de Compra',
            'slug'          =>  'ocompra.edit',
            'description'   =>  'Edita una orden de compra del sistema.',
            'section' => 'alogistica',
            'section_2' => 'ordencompras'
        ]);
        Permission::create([
            'name'          =>  'Borrar Orden de Compra',
            'slug'          =>  'ocompra.delete',
            'description'   =>  'Borra una orden de compra del sistema.',
            'section' => 'alogistica',
            'section_2' => 'ordencompras'
        ]);
        Permission::create([
            'name'          =>  'Exportar Orden de Compra',
            'slug'          =>  'ocompra.export',
            'description'   =>  'Exportar ordenes de compra del sistema.',
            'section' => 'alogistica',
            'section_2' => 'ordencompras'
        ]);
        //Productos y Servicios
        Permission::create([
            'name'          =>  'Mostrar Productos y Servicios (menu)',
            'slug'          =>  'pservicios.submenu',
            'description'   =>  'Muestra la sección de productos y servicios en el menu del sistema.',
            'section' => 'alogistica',
            'section_2' => 'pservicios'
        ]);
        Permission::create([
            'name'          =>  'Ver Productos y Servicios',
            'slug'          =>  'pservicios.show',
            'description'   =>  'Lista y muestra los productos y servicios del sistema.',
            'section' => 'alogistica',
            'section_2' => 'pservicios'
        ]);
        Permission::create([
            'name'          =>  'Crear Producto/Servicio',
            'slug'          =>  'pservicios.create',
            'description'   =>  'Crea un producto/servicio en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'pservicios'
        ]);
        Permission::create([
            'name'          =>  'Edita Producto/Servicio',
            'slug'          =>  'pservicios.edit',
            'description'   =>  'Edita un producto/servicio del sistema.',
            'section' => 'alogistica',
            'section_2' => 'pservicios'
        ]);
        Permission::create([
            'name'          =>  'Elimina Producto/Servicio',
            'slug'          =>  'pservicios.delete',
            'description'   =>  'Elimina un producto/servicio del sistema.',
            'section' => 'alogistica',
            'section_2' => 'pservicios'
        ]);
        Permission::create([
            'name'          =>  'Deshabilita Producto/Servicio',
            'slug'          =>  'pservicios.disable',
            'description'   =>  'Deshabilita un producto/servicio del sistema.',
            'section' => 'alogistica',
            'section_2' => 'pservicios'
        ]);
        Permission::create([
            'name'          =>  'Importar Producto/Servicio',
            'slug'          =>  'pservicios.import',
            'description'   =>  'Importa productos/servicios al sistema.',
            'section' => 'alogistica',
            'section_2' => 'pservicios'
        ]);
        Permission::create([
            'name'          =>  'Exporta Producto/Servicio',
            'slug'          =>  'pservicios.export',
            'description'   =>  'Export productos/servicios del sistema.',
            'section' => 'alogistica',
            'section_2' => 'pservicios'
        ]);
        // Categorias
        Permission::create([
            'name'          =>  'Ver categorías',
            'slug'          =>  'categorias.show',
            'description'   =>  'Lista y muestra las categorías del sistema.',
            'section' => 'alogistica',
            'section_2' => 'categorias'
        ]);
        Permission::create([
            'name'          =>  'Crear categoría',
            'slug'          =>  'categorias.create',
            'description'   =>  'Crea una categoría en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'categorias'
        ]);
        Permission::create([
            'name'          =>  'Edita categoría',
            'slug'          =>  'categorias.edit',
            'description'   =>  'Edita una categoría del sistema.',
            'section' => 'alogistica',
            'section_2' => 'categorias'
        ]);
        Permission::create([
            'name'          =>  'Elimina categoría',
            'slug'          =>  'categorias.delete',
            'description'   =>  'Elimina una categoría del sistema.',
            'section' => 'alogistica',
            'section_2' => 'categorias'
        ]);
        Permission::create([
            'name'          =>  'Importar categorías',
            'slug'          =>  'categorias.import',
            'description'   =>  'Importa categorías al sistema.',
            'section' => 'alogistica',
            'section_2' => 'categorias'
        ]);
        Permission::create([
            'name'          =>  'Exporta categorías',
            'slug'          =>  'categorias.export',
            'description'   =>  'Export categorías del sistema.',
            'section' => 'alogistica',
            'section_2' => 'categorias'
        ]);

        // Requerimientos
        Permission::create([
            'name'          =>  'Ver Requerimientos',
            'slug'          =>  'requirement.show',
            'description'   =>  'Lista y navega los requerimientos del sistema.',
            'section' => 'alogistica',
            'section_2' => 'requerimientos'
        ]);
        Permission::create([
            'name'          =>  'Crear Requerimientos',
            'slug'          =>  'requirement.create',
            'description'   =>  'Permite crear un nuevo requerimiento en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'requerimientos'
        ]);
        Permission::create([
            'name'          =>  'Editar Requerimiento',
            'slug'          =>  'requirement.edit',
            'description'   =>  'Permite al jefe editar y tomar acciones en un determinado requerimiento en el sistema.',
            'section' => 'alogistica',
            'section_2' => 'requerimientos'
        ]);
        Permission::create([
            'name'          =>  'Exporta Requerimientos',
            'slug'          =>  'requirement.export',
            'description'   =>  'Export los requerimientos del sistema.',
            'section' => 'alogistica',
            'section_2' => 'requerimientos'
        ]);

        /**
         * Alamacenes
         */
        Permission::create([
            'name'          =>  'Mostrar Almacén (menu)',
            'slug'          =>  'almacen.menu',
            'description'   =>  'Muesta la sección de Almacén en el menu del sistema.',
            'section' => 'menu'
        ]);
        // Almacenes
        Permission::create([
            'name'          =>  'Ver Almacenes',
            'slug'          =>  'almacenes.show',
            'description'   =>  'Lista y muestra almacenes en el sistema.',
            'section' => 'almacen',
            'section_2' => 'almacenes'
        ]);
        Permission::create([
            'name'          =>  'Crear Almacenes',
            'slug'          =>  'almacenes.create',
            'description'   =>  'Crea almacenes en el sistema.',
            'section' => 'almacen',
            'section_2' => 'almacenes'
        ]);
        Permission::create([
            'name'          =>  'Edita Almacenes',
            'slug'          =>  'almacenes.edit',
            'description'   =>  'Edita un almacen en el sistema.',
            'section' => 'almacen',
            'section_2' => 'almacenes'
        ]);
        Permission::create([
            'name'          =>  'Ver Transferencias',
            'slug'          =>  'transfers.show',
            'description'   =>  'Lista y muestra transferencias.',
            'section' => 'almacen',
            'section_2' => 'almacenes'
        ]);
        Permission::create([
            'name'          =>  'Crear Transferencias',
            'slug'          =>  'transfers.create',
            'description'   =>  'Permite crear transferencias.',
            'section' => 'almacen',
            'section_2' => 'almacenes'
        ]);
        Permission::create([
            'name'          =>  'Anular Transferencias',
            'slug'          =>  'transfers.delete',
            'description'   =>  'Permite anular transferencias.',
            'section' => 'almacen',
            'section_2' => 'almacenes'
        ]);
        Permission::create([
            'name'          =>  'Reporte Stock Almacén',
            'slug'          =>  'report.stockwarehouse',
            'description'   =>  'Muestra reporte Stock Almacén.',
            'section' => 'almacen',
            'section_2' => 'almacenes'
        ]);
        Permission::create([
            'name'          =>  'Duplicar Transferencias',
            'slug'          =>  'transfers.duplicate',
            'description'   =>  'Permite duplicar transferencias.',
            'section' => 'almacen',
            'section_2' => 'almacenes'
        ]);
        Permission::create([
            'name'          =>  'Agregar Movimiento en Kardex',
            'slug'          =>  'kardex.addmovement',
            'description'   =>  'Agrega movimientos al Kardex',
            'section' => 'almacen',
            'section_2' => 'inventarios'
        ]);
        Permission::create([
            'name'          =>  'Ver Inventarios',
            'slug'          =>  'inventario.show',
            'description'   =>  'Muestra y lista los inventarios',
            'section' => 'almacen',
            'section_2' => 'inventarios'
        ]);
        Permission::create([
            'name'          =>  'Ingresos',
            'slug'          =>  'ingresos',
            'description'   =>  'Agrega un nuevo ingreso a inventarios',
            'section' => 'almacen',
            'section_2' => 'inventarios'
        ]);
        Permission::create([
            'name'          =>  'Transferencias',
            'slug'          =>  'transferencias',
            'description'   =>  'Permite realizar una nueva transferencia entre almacenes',
            'section' => 'almacen',
            'section_2' => 'inventarios'
        ]);
        Permission::create([
            'name'          =>  'Salidas',
            'slug'          =>  'salidas',
            'description'   =>  'Configura y registra salidas de almacen',
            'section' => 'almacen',
            'section_2' => 'inventarios'
        ]);
        Permission::create([
            'name'          =>  'Kardex',
            'slug'          =>  'kardex',
            'description'   =>  'Muestra los movimientos de almacenes',
            'section' => 'almacen',
            'section_2' => 'inventarios'
        ]);

        /**
         * Contabilidad
         */
        Permission::create([
            'name'          =>  'Mostrar Contabilidad (menu)',
            'slug'          =>  'cantabilidad.show',
            'description'   =>  'Muestra la sección de Contabilidad en el menu del sistema',
            'section' => 'menu',
            'section_2' => ''
        ]);
        Permission::create([
            'name'          =>  'Ver Comprobantes',
            'slug'          =>  'comprobantes.show',
            'description'   =>  'Muestra la sección de Comprobantes en el menu del sistema',
            'section' => 'acontabilidad',
            'section_2' => 'comprobantes'
        ]);
        Permission::create([
            'name'          =>  'Ver Resumen Diario',
            'slug'          =>  'comprobantes.resumen',
            'description'   =>  'Muestra y lista el resumen diario',
            'section' => 'acontabilidad',
            'section_2' => 'comprobantes'
        ]);
        Permission::create([
            'name'          =>  'Ver contigencias',
            'slug'          =>  'comprobantes.contigencias',
            'description'   =>  'Muestra y lista contigencias',
            'section' => 'acontabilidad',
            'section_2' => 'comprobantes'
        ]);
        Permission::create([
            'name'          =>  'Ver Retenciones',
            'slug'          =>  'comprobantes.retenciones',
            'description'   =>  'Muestra y lista retenciones',
            'section' => 'acontabilidad',
            'section_2' => 'comprobantes'
        ]);
        Permission::create([
            'name'          =>  'Ver Percepción',
            'slug'          =>  'comprobantes.percepcion',
            'description'   =>  'Muestra y lista percepciones',
            'section' => 'acontabilidad',
            'section_2' => 'comprobantes'
        ]);
        Permission::create([
            'name'          =>  'Ver Guías de Remisión',
            'slug'          =>  'comprobantes.guiasremsion',
            'description'   =>  'Muestra y lista guías de remisión',
            'section' => 'acontabilidad',
            'section_2' => 'comprobantes'
        ]);
        Permission::create([
            'name'          =>  'Crear Guías de Remisión',
            'slug'          =>  'guiaremision.create',
            'description'   =>  'Crea guías de remisión',
            'section' => 'acontabilidad',
            'section_2' => 'comprobantes'
        ]);
        Permission::create([
            'name'          =>  'Mostrar Anulaciones',
            'slug'          =>  'anulaciones.show',
            'description'   =>  'Muestra Anulaciones en el menu del sistema',
            'section' => 'acontabilidad',
            'section_2' => 'anulaciones'
        ]);
        Permission::create([
            'name'          =>  'Ver Comunicaciones de Baja',
            'slug'          =>  'low.show',
            'description'   =>  'Lista y Navega las comunicaciones de baja',
            'section' => 'acontabilidad',
            'section_2' => 'anulaciones'
        ]);
        Permission::create([
            'name'          =>  'Ver Notas de crédito',
            'slug'          =>  'creditnote.show',
            'description'   =>  'Lista y Navega las notas de créditos',
            'section' => 'acontabilidad',
            'section_2' => 'anulaciones'
        ]);
        Permission::create([
            'name'          =>  'Ver Notas de débito',
            'slug'          =>  'debittnote.show',
            'description'   =>  'Lista y Navega las notas de débito',
            'section' => 'acontabilidad',
            'section_2' => 'anulaciones'
        ]);
        Permission::create([
            'name'          =>  'Ver Percepciones',
            'slug'          =>  'perception.show',
            'description'   =>  'Lista y Navega percepciones',
            'section' => 'acontabilidad',
            'section_2' => 'anulaciones'
        ]);
        Permission::create([
            'name'          =>  'Ver Retenciones',
            'slug'          =>  'retention.show',
            'description'   =>  'Lista y Navega retenciones',
            'section' => 'acontabilidad',
            'section_2' => 'anulaciones'
        ]);

        /**
         * Configuraciones
         */
        Permission::create([
            'name'          =>  'Mostrar Configuraciones (menu)',
            'slug'          =>  'configuraciones.menu',
            'description'   =>  'Muesta la sección de Configuraciones en el menu del sistema.',
            'section' => 'menu'
        ]);
        // Configuracion de correlativos
        Permission::create([
            'name'          =>  'Ver Correlativos',
            'slug'          =>  'correlativos.show',
            'description'   =>  'Lista y muestra los correlativos del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'correlativos'
        ]);
        Permission::create([
            'name'          =>  'Crear Correlativos',
            'slug'          =>  'correlativos.create',
            'description'   =>  'Crea correlativos del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'correlativos'
        ]);
        Permission::create([
            'name'          =>  'Editar Correlativos',
            'slug'          =>  'correlativos.edit',
            'description'   =>  'Edita correlativos del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'correlativos'
        ]);
        // Empresa
        Permission::create([
            'name'          =>  'Ver Configuración de Empresa',
            'slug'          =>  'empresa.show',
            'description'   =>  'Muestra la configuración de la empresa.',
            'section' => 'configuraciones',
            'section_2' => 'empresa'
        ]);
        Permission::create([
            'name'          =>  'Agrega Datos del Emisor',
            'slug'          =>  'empresa.emisor',
            'description'   =>  'Agrega datos de emisor.',
            'section' => 'configuraciones',
            'section_2' => 'empresa'
        ]);
        Permission::create([
            'name'          =>  'Personalizar representación impresa',
            'slug'          =>  'empresa.impresa',
            'description'   =>  'Personalizar representación impresa.',
            'section' => 'configuraciones',
            'section_2' => 'empresa'
        ]);
        Permission::create([
            'name'          =>  'Configuración adicional',
            'slug'          =>  'empresa.adicional',
            'description'   =>  'Configuración adicional.',
            'section' => 'configuraciones',
            'section_2' => 'empresa'
        ]);
        // Locales y Series
        Permission::create([
            'name'          =>  'Ver Locales y Series',
            'slug'          =>  'localserie.show',
            'description'   =>  'Lista y muestra los locales y series del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'localserie'
        ]);
        Permission::create([
            'name'          =>  'Crear Local',
            'slug'          =>  'localserie.create',
            'description'   =>  'Crea un nuevo local en el sistema.',
            'section' => 'configuraciones',
            'section_2' => 'localserie'
        ]);
        Permission::create([
            'name'          =>  'Editar Local',
            'slug'          =>  'localserie.edit',
            'description'   =>  'Edita un nuevo local en el sistema.',
            'section' => 'configuraciones',
            'section_2' => 'localserie'
        ]);
        // Apariencia
        Permission::create([
            'name'          =>  'Ver Apariencia',
            'slug'          =>  'apariencia.show',
            'description'   =>  'Muesta la configuración de apariencia del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'apariencia'
        ]);
        // Usuarios
        Permission::create([
            'name'          =>  'Ver usuarios',
            'slug'          =>  'usuarios.show',
            'description'   =>  'Lista y muestra los usuarios del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'usuarios'
        ]);
        Permission::create([
            'name'          =>  'Crear usuarios',
            'slug'          =>  'usuarios.create',
            'description'   =>  'Crear usuario nuevo en el sistema.',
            'section' => 'configuraciones',
            'section_2' => 'usuarios'
        ]);
        // Roles
        Permission::create([
            'name'          =>  'Ver Roles',
            'slug'          =>  'roles.show',
            'description'   =>  'Lista y muestra los roles del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'roles'
        ]);
        Permission::create([
            'name'          =>  'Crear Role',
            'slug'          =>  'roles.create',
            'description'   =>  'Crea roles del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'roles'
        ]);
        Permission::create([
            'name'          =>  'Editar Role',
            'slug'          =>  'roles.edit',
            'description'   =>  'Edita un role del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'roles'
        ]);
        Permission::create([
            'name'          =>  'Elimina Role',
            'slug'          =>  'roles.delete',
            'description'   =>  'Elimina un role del sistema.',
            'section' => 'configuraciones',
            'section_2' => 'roles'
        ]);
    }
}
