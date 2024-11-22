<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class MyChartName extends Chart
{
    public $chartData;

    public function __construct($chartData)
    {
        parent::__construct();

        $this->chartData = $chartData;
    }

    public function handler()
    {
        $ingresoB = $this->chartData['ingresoB'];
        $descDevo = $this->chartData['descDevo'];
        $ingresoN = $this->chartData['ingresoN'];
        $costSale = $this->chartData['costSale'];

        // Código para el gráfico de barras (Ingresos Brutos y Descuentos y Devoluciones)
        $this->type('bar');

        $this->labels(['Ingresos Brutos', 'Descuentos y Devoluciones']);

        $this->dataset('Ingresos Brutos', [$ingresoB])->backgroundColor('blue');
        $this->dataset('Descuentos y Devoluciones', [$descDevo])->backgroundColor('red');

        // Código para el gráfico de pastel (Ingresos Netos y Costo de Ventas)
        $this->type('pie');

        $this->labels(['Ingresos Netos', 'Costo de Ventas']);

        $this->dataset('Ingresos Netos', [$ingresoN])->backgroundColor('green');
        $this->dataset('Costo de Ventas', [$costSale])->backgroundColor('orange');

        // Otras opciones y configuraciones del gráfico que puedas necesitar
        // ...

        return $this;
    }
}
