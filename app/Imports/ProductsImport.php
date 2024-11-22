<?php
namespace App\Imports;
use Auth;
use App\Taxe;
use App\Brand;
use App\Store;
use App\Kardex;
use App\Measure;
use App\Product;
use App\Category;
use App\Inventory;
use App\PriceList;
use App\Warehouse;
use App\Correlative;
use App\HeadQuarter;
use App\OperationType;
use App\Classification;
use App\ProductPriceLog;
use App\ProductPriceList;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public $headquarter;
    public function __construct()
    {
        $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        if($row['descripcion'] == null) {
            return;
        }
        $code = trim($row['codigo_de_barra']);
        $internalcode = trim($row['codigo_interno']);
        $description = trim($row['descripcion']);
        $igv = $row['tipo_de_igv1gravado9inafecto8exonerado'];
        $tipoProducto = $row['tipo_de_producto_0activo_fijo_1mercaderia_2gasto'];
        $tipoOperacion = $row['tipo_de_opeacion_1productos2servicios22bolsa_de_plastico'];
        $operation_type = $row['codigo_unidad_de_medida'];
        $coin_id = $row['moneda_1soles_2dolares_3euros'];
        $cost = $row['costo'];
        $utilidad = $row['utilidad'];
        $category_id = $row['descripcion_de_categoria'];
        $brand_id = $row['marca'];
        $sunat_code = $row['codigo_producto_sunat'];
        $stock_new = $row['stock_actual_disponible'];
        $clasificacion = $row['clasificacion'];
        $codigo_equivalencia = $row['codigo_de_equivalencia'];
        $detalle = $row['detalle'];
        $imagen = $row['imagen'];
        $stockInicial = $row['stock_inicial'];
        $stock_minimo = $row['stock_minimo'];
        $stock_maximo = $row['stock_maximo'];
        $precio_lista_normal = 0;
        $impuesto = $row['impuesto'];
        $categoryDescription = trim($row['descripcion_de_categoria']);
        $brandDescription = trim($row['marca']);

        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;
    
        $productExist = Product::where('internalcode',$internalcode)->where('client_id', Auth::user()->headquarter->client_id)->first();
        $clientId = auth()->user()->client_id;

        $brand = Brand::where('description', 'LIKE', $brandDescription)
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->first();
        $category = Category::where('description', 'LIKE', $categoryDescription)
                            ->where('client_id', auth()->user()->headquarter->client_id)
                            ->first();

        if ($brandDescription == null) {
            $brand = Brand::where('description', 'SIN MARCA')->where('client_id', Auth::user()->headquarter->client_id)->first();

            if ($brand == null) {
                $brand = new Brand;
                $brand->description = 'SIN MARCA';
                $brand->client_id = auth()->user()->headquarter->client_id;
                $brand->save();
            }
        }

        if ($categoryDescription == null) {
            $category = Category::where('description', 'SIN CATEGORIA')->where('client_id', Auth::user()->headquarter->client_id)->first();

            if ($category == null) {
                $category = new Category;
                $category->description = 'SIN CATEGORIA';
                $category->status = 1;
                $category->client_id = auth()->user()->headquarter->client_id;
                $category->save();
            }
        }

        if ($category == null) {
            $category = new Category;
            $category->description = $categoryDescription;
            $category->status = 1;
            $category->client_id = auth()->user()->headquarter->client_id;
            $category->save();
        }

        if ($brand == null) {
            $brand = new Brand;
            $brand->description = $brandDescription;
            $brand->client_id = auth()->user()->headquarter->client_id;
            $brand->save();
        }

        if ($productExist == null) {
            $category = Category::where('description', 'LIKE', '%' . $category_id . '%')->where('client_id',  Auth::user()->headquarter->client_id)->first();
            $brand = Brand::where('description', 'LIKE', '%' . $brand_id . '%')->where('client_id',  Auth::user()->headquarter->client_id)->first();
            $classification = Classification::where('description', 'LIKE', '%' . $clasificacion . '%')->first();
            $measure = Measure::where('code', $operation_type)->first();
            if ($impuesto != null) {
                $tax = Taxe::where('description',  'LIKE', '%' . $impuesto . '%')->where('client_id',  Auth::user()->headquarter->client_id)->first();
            }

            if($classification == null) {
                $classification = Classification::where('description', 'SIN CLASIFICACIÓN')->first();
            }

            $product = new Product;
            $product->code =  $code;
            $product->internalcode =  $internalcode;
            $product->description =  $description;
            $product->operation_type =  $tipoOperacion;
            $product->measure_id = $measure->id;
            $product->coin_id =  $coin_id;
            $product->status = 1;
            $product->category_id =  $category->id;
            $product->brand_id =  $brand->id;
            $product->sunat_code =  $sunat_code;
            $product->cost = $cost;
            $product->utility = $utilidad;
            $product->client_id =  Auth::user()->headquarter->client_id;
            $product->classification_id =  $classification->id;
            $product->equivalence_code  =  $codigo_equivalencia;
            $product->external_code     =  '';
            $product->detail            =  $detalle;
            $product->image             =  $imagen ? $imagen : 'default.jpg';
            $product->initial_stock = $stockInicial;
            $product->initial_date = date('Y-m-d');
            $product->type_product = $tipoProducto;
            $product->type_igv_id = $igv;
            $product->tax_id = $impuesto != null ? $tax->id : null;
            $product->save();

            $isOt = 0;
            if ($tipoOperacion != 2) {
                $isOt = 1;
            }
            
            $productId= $product->id;
            
            $store = new Store;
            $store->stock = $stock_new;
            $store->price = $precio_lista_normal;
            $store->warehouse_id = $mainWarehouseId;
            $store->product_id = $product->id;
            $store->higher_price    =   0;
            $store->maximum_stock   =   $stock_maximo;
            $store->minimum_stock   =   $stock_minimo;
            $store->save();
            $nuevoStock = $stock_new;

            $existInventory = Inventory::where('product_id', $product->id)
                            ->where('warehouse_id', $mainWarehouseId)
                            ->where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->first();
        } else {
            $stock = Store::where('product_id',$productExist->id)->where('warehouse_id', $mainWarehouseId)->first();
            $productId = $productExist->id;
            $isOt = 0;
            if ($productExist->operation_type != 2) {
                $isOt = 1;
            }
            
            if ($stock == null) {
                $stock = new Store;
                $stock->warehouse_id = $mainWarehouseId;
                $stock->product_id = $productExist->id;
                $stock->stock = $stock_new;
                $stock->price = $precio_lista_normal;
                $nuevoStock = $stock_new;
            } else {
                $newStock = 0;
                $oldStock = $stock->stock;
                $newStock = (int) $oldStock + (int) $stock_new;
                $stock->stock = $newStock;
                $nuevoStock = $newStock;
            }
            
            $stock->save();

            $existInventory = Inventory::where('product_id', $productExist->id)
                            ->where('warehouse_id', $mainWarehouseId)
                            ->where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->first();
        }
        /**
         * AGREGA A INVENTARIO
         */
        if ($isOt == 1) {
            $correlatives = Correlative::where([
                ['client_id',  auth()->user()->headquarter->client_id],
                ['typevoucher_id', 20]
            ])->first();

            if ($correlatives == null) {
                return response()->JSON(-9);
            }

            $correlatives = Correlative::where([
                ['client_id', auth()->user()->headquarter->client_id],
                ['typevoucher_id', 20]
            ])->first();

            $setCorrelative = (int) $correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

            $correlative = Correlative::find($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $existInventory = Inventory::where('product_id', $productId)->where('client_id', auth()->user()->headquarter->client_id)->first();

            if($existInventory == null) {
                $admission = new Inventory;
                $admission->warehouse_id = $mainWarehouseId;
                $admission->client_id = auth()->user()->headquarter->client_id;
                $admission->headquarter_id = $this->headquarter;
                $admission->admission = date("Y-m-d");
                $admission->serie = $correlative->serialnumber;
                $admission->correlative = $final;
                $admission->place = null;
                $admission->responsable = auth()->user()->name;
                $admission->serial = null;
                $admission->lot = null;
                $admission->expiration = null;
                $admission->warranty = null;
                $admission->amount_entered = $nuevoStock;
                $admission->observation = 'Ingresado desde Sección Productos y Servicios';
                $admission->product_id = $productId;
                $admission->guide = 'INGRESO EXCEL';
                $admission->save();
            } else {
                $existInventory->admission = date("Y-m-d");
                $existInventory->amount_entered = $nuevoStock;
                $existInventory->save();
            }
            $product = Product::find($productId);

            $kardex = new Kardex;
            $kardex->type_transaction = 'INGRESO EXCEL';
            $kardex->cost = $product->cost;
            $kardex->entry = $nuevoStock;
            $kardex->balance = $nuevoStock;
            $kardex->warehouse_id = $mainWarehouseId;
            $kardex->client_id = auth()->user()->headquarter->client_id;
            $kardex->product_id = $productId;
            $kardex->coin_id = 1;
            $kardex->exchange_rate = auth()->user()->headquarter->client->exchange_rate_sale;
            $kardex->save();
        }

        ProductPriceList::where('product_id', $productId)->get()->each(function($item, $key) {
            $item->delete();
        });

        if ($row['precio_mayorista'] != null) {
            $priceList = new ProductPriceList();
            $priceList->price_list_id       =   2;
            $priceList->price               =   $row['precio_mayorista'];
            if ($cost < 1) {
                $priceList->utility_percentage  =   0;
            } else {
                $priceList->utility_percentage  =   number_format(((float) $row['precio_mayorista'] - (float) $cost) / (float) $cost, 2);
            }
            $priceList->product_id          =   $productId;
            $priceList->save();
        }
        if ($row['precio_por_caja'] != null) {
            $priceList = new ProductPriceList();
            $priceList->price_list_id       =   3;
            $priceList->price               =   $row['precio_por_caja'];
            if ($cost < 1) {
                $priceList->utility_percentage = 0;
            } else {
                $priceList->utility_percentage = number_format(((float)$row['precio_por_caja'] - (float)$cost) / (float)$cost, 2);
            }
            $priceList->product_id          =   $productId;
            $priceList->save();
        }
        if ($row['precio_por_unidad'] != null) {
            $priceList = new ProductPriceList();
            $priceList->price_list_id       =   4;
            $priceList->price               =   $row['precio_por_unidad'];
            if ($cost < 1) {
                $priceList->utility_percentage = 0;
            } else {
                $priceList->utility_percentage = number_format(((float)$row['precio_por_unidad'] - (float)$cost) / (float)$cost, 2);
            }
            $priceList->product_id          =   $productId;
            $priceList->save();
        }
    }
}