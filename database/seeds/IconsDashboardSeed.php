<?php

use App\IconDashboard;
use Illuminate\Database\Seeder;

class IconsDashboardSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IconDashboard::create([
            'description'   =>  'Clientes',
            'url'           =>  '/commercial.customers',
            'permission'    =>  'clientes.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Nueva Cotización',
            'url'           =>  '/commercial.quotations.create',
            'permission'    =>  'cotizaciones.create'
        ]);
        IconDashboard::create([
            'description'   =>  'Nueva Factura',
            'url'           =>  '/commercial.sales.create/1',
            'permission'    =>  'ventas.facturacreate'
        ]);
        IconDashboard::create([
            'description'   =>  'Nueva Boleta',
            'url'           =>  '/commercial.sales.create/2',
            'permission'    =>  'ventas.boletacreate'
        ]);
        IconDashboard::create([
            'description'   =>  'Reporte de Ventas',
            'url'           =>  '/commercial.reports',
            'permission'    =>  'reportesventas.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Proveedores',
            'url'           =>  '/logistic.providers',
            'permission'    =>  'proveedores.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Ordenes de Compra',
            'url'           =>  '/logistic.order.purchase',
            'permission'    =>  'ocompra.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Compras',
            'url'           =>  '/logistic.purchases',
            'permission'    =>  'compras.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Productos y Servicios',
            'url'           =>  '/warehouse.products',
            'permission'    =>  'pservicios.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Requerimientos',
            'url'           =>  '/requirements',
            'permission'    =>  'requirement.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Almacenes',
            'url'           =>  '/warehouse.list',
            'permission'    =>  'almacenes.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Inventarios',
            'url'           =>  '/inventory',
            'permission'    =>  'inventario.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Transferencias',
            'url'           =>  '/transfer',
            'permission'    =>  'transferencias'
        ]);
        IconDashboard::create([
            'description'   =>  'Kardex',
            'url'           =>  '/kardex',
            'permission'    =>  'kardex'
        ]);
        IconDashboard::create([
            'description'   =>  'Guías de Remisión',
            'url'           =>  '/reference-guide',
            'permission'    =>  'comprobantes.guiasremsion'
        ]);
        IconDashboard::create([
            'description'   =>  'Notas de Crédito',
            'url'           =>  '/account/notes/credit',
            'permission'    =>  'creditnote.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Notas de Débito',
            'url'           =>  '/account/notes/debit',
            'permission'    =>  'debittnote.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Comunicaciones',
            'url'           =>  '/commercial/lows',
            'permission'    =>  'low.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Locales y Series',
            'url'           =>  '/configuration.headquarters',
            'permission'    =>  'localserie.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Empresa',
            'url'           =>  '/configuration.company',
            'permission'    =>  'empresa.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Comunicaciones',
            'url'           =>  '/commercial/lows',
            'permission'    =>  'low.show'
        ]);
        IconDashboard::create([
            'description'   =>  'Usuarios',
            'url'           =>  '/configuration.users',
            'permission'    =>  'usuarios.show'
        ]);
    }
}
