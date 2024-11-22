@extends('layouts.azia')

@section('css')
<style>
/* Estilos para el encabezado */
header {
    background-color: #3498db;
    border-radius: 3px;
    padding: 19px;
    text-align: center;
}

h1 {
    margin: 0;
    font-size: 32px;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

/* Estilos para el contenedor principal */
.container {
    margin: 20px auto;
    padding: 20px;
    background-color: #f2f2f2;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 1500px !important;
    display: flex;
    justify-content: center; /* Centramos el contenido horizontalmente */
}

/* Estilos para las secciones */
.section {
    background-color: #FFFFFF;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    max-width: 1500px !;
}

.section-heading {
    color: #3498db;
    font-size: 28px;
    margin-bottom: 20px;
    margin-top: 25px;
}

/* Estilos para los elementos */
.item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    margin: 20px;
    border-bottom: 1px solid #E0E0E0;
}

.item:last-child {
    border-bottom: none;
}

.item-label {
    color: #333;
    font-weight: bold;
    font-size: 18px;
}

.item-value {
    color: #555;
    font-size: 20px; /* Increased font size for prices */
    margin-left: 200px; /* Increased spacing between labels and prices */
}

/* Estilos para la sección de búsqueda */
.container-option {
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}

form {
    margin-bottom: 20px;
}

.input-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.input-container {
    flex: 1;
    margin-right: 10px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="date"],
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

button.filter-button,
button.detail-button {
    background-color: #3498db;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

button.filter-button:hover,
button.detail-button:hover {
    background-color: #2980b9;
}

.detail-button {
    margin-top: 10px;
    width: 100%;
}

/* Seccion de Graphics */
.container-charts {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-top: 30px;
}

.graphics {
    max-width: 520px;
    margin-bottom: 30px;
}

.bar {
    width: 48%;
    margin-bottom: 30px;
}

.explanation {
    width: 48%;
    padding: 20px;
    background-color: #f0f0f0;
    border-radius: 8px;
}

/* Add media queries for responsiveness */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
    }

    .right-section {
        margin-left: 0;
    }

    .section {
        width: 100%;
    }

    .graphics {
        margin-bottom: 20px;
    }

    .bar {
        width: 100%;
        margin-bottom: 20px;
    }
}

@media (max-width: 600px) {
    .item-label {
        font-size: 16px;
    }

    .item-value {
        font-size: 16px;
    }

    .date-input {
        flex-direction: column;
    }

    .date-input-box {
        width: 100%;
    }

    .search-label,
    .search-select {
        font-size: 14px;
    }

    .search-button,
    .detail-button {
        font-size: 14px;
    }
}

/* Additional styles for the header and container */
header {
    margin-bottom: 30px;
}

.container {
    padding: 30px;
}

/* Styling the buttons */
.filter-button {
    background-color: #3498db;
}

.filter-button:hover {
    background-color: #2980b9;
}

.detail-button {
    background-color: #2ecc71;
}

.detail-button:hover {
    background-color: #27ae60;
}


</style>
@endsection

@section('content')
<header>
    <h1>Finanzas</h1>
</header>

<div class="container-option">
    <form method="post" action="{{ route('filtro') }}">
        @csrf
        <div class="input-row">
            <div class="input-container">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" name="fecha_inicio" required>
            </div>
            <div class="input-container">
                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" name="fecha_fin" required>
            </div>
            <div class="input-container">
                <label for="locales">Seleccionar local:</label>
                <select name="locales" id="locales">
                    <option value="-1">Todos los locales</option>
                    @foreach($locales as $id => $descripcion)
                        <option value="{{ $id }}">{{ $descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="filter-button">Filtrar</button>
        </div>
        {{-- <button type="button" class="detail-button">Ver detalladamente</button> --}}
    </form>
</div>

 

<div class="container"> 

    <div class="vertical-sections">
        <div class="section">
            <h2 class="section-heading">Estado de resultados</h2>
            <div class="item">
                <span class="item-label">Ingresos Brutos</span>
                <span class="item-value">S/. {{ number_format($data['ingresoB'], 2) }}</span>
            </div>
            <div class="item">
                <span class="item-label">Descuentos y devoluciones</span>
                <span class="item-value">{{ ($data['descDevo'] < 0) ? '(S/. '.number_format(abs($data['descDevo']), 2).')' : 'S/. '.number_format($data['descDevo'], 2) }}</span>
            </div>
            <div class="item">
                <span class="item-label">Ingresos Netos</span>
                <span class="item-value">S/. {{ number_format($data['ingresoN'], 2) }}</span>
            </div>
            
            <div class="item">
                <span class="item-label">Costo de Ventas</span>
                <span class="item-value">(S/. {{ number_format($data['costSale'], 2) }})</span>
            </div>
            <div class="item">
                <span class="item-label">Utilidad Bruta</span>
                <span class="item-value">{{ ($data['utilidad'] < 0) ? '(S/. '.number_format(abs($data['utilidad']), 2).')' : 'S/. '.number_format($data['utilidad'], 2) }}</span>
            </div>
            <div class="item">
                <span class="item-label">Gastos</span>
                <span class="item-value">(S/. {{ number_format($data['gastos'], 2) }})</span>
            </div>
            <div class="item">
                <span class="item-label">Gasto RH</span>
                <span class="item-value" id="utilidad-operativa-valor" data-valor-original="{{ $data['gastosRH'] }}">{{ ($data['gastosRH'] < 0) ? '(S/. '.number_format(abs($data['gastosRH']), 2).')' : 'S/. '.number_format($data['gastosRH'], 2) }}</span>
            </div>
            <div class="item">
                <span class="item-label">Gasto de Compras</span>
                <span class="item-value" id="utilidad-operativa-valor" data-valor-original="{{ $data['gastosCO'] }}">{{ ($data['gastosCO'] < 0) ? '(S/. '.number_format(abs($data['gastosCO']), 2).')' : 'S/. '.number_format($data['gastosCO'], 2) }}</span>
            </div>
            <div class="item">
                <span class="item-label">Gasto de personal</span>
                <span class="item-value" id="gasto-personal-valor">(S/. )</span>
            </div>            
            <div class="item">
                <span class="item-label">Utilidad operativa</span>
                <span class="item-value" id="utilidad-operativa-valor" data-valor-original="{{ $data['utilidadO'] }}">{{ ($data['utilidadO'] < 0) ? '(S/. '.number_format(abs($data['utilidadO']), 2).')' : 'S/. '.number_format($data['utilidadO'], 2) }}</span>
            </div>
            
            
            
        </div>
    
        {{-- <div class="section">  
            <div class="item">
                <span class="item-label">Impuesto a la Renta</span>
                <span class="item-value">S/. 0</span>
            </div>
            <div class="item">
                <span class="item-label">Utilidad libre de impuestos</span>
                <span class="item-value">S/. 0</span>
            </div>
        </div> --}}
    </div>
    <div class="right-section">
        <div class="section">
            <h2 class="section-heading">Ingresa los gastos de personal</h2>
            <div class="item">
                <input type="text" id="gastos-personal">
                <button type="button" class="search-button" onclick="agregarGastoPersonal()">Agregar</button>
            </div>
            <div id="error-message" style="color: red;"></div>
        </div>
    </div>
    
    
    
    
</div>


<div class="container-charts">
    @if($chart)
        <div class="bar">
            <canvas id="myChart1"></canvas>
        </div>
        <div class="explanation">
            <h2>Gráfico de Barras</h2>
            <p>
                El gráfico de barras que ves muestra una comparación entre los 'Ingresos Brutos', 'Descuentos y Devoluciones' e 'Ingresos Netos' de nuestra empresa. Este tipo de gráfico es excelente para visualizar y analizar datos cuantitativos de diferentes categorías.
                <br><br>
                <strong>'Ingresos Brutos':</strong> Representa el total de ingresos generados por nuestra empresa antes de aplicar cualquier descuento o devolución. Es una métrica importante que nos da una visión general del volumen de ventas sin tener en cuenta factores adicionales.
                <br><br>
                <strong>'Descuentos y Devoluciones':</strong> Muestra la cantidad de ingresos perdidos debido a descuentos otorgados a los clientes y productos devueltos. Es esencial comprender esta cifra, ya que afecta directamente a nuestros ingresos netos.
                <br><br>
                <strong>'Ingresos Netos':</strong> Indica los ingresos finales después de restar los descuentos y devoluciones de los ingresos brutos. Esta cifra refleja la cantidad real de ingresos que hemos obtenido después de tener en cuenta los ajustes necesarios.
                <br><br>
                El gráfico utiliza diferentes colores para representar cada categoría: azul para 'Ingresos Brutos', rojo para 'Descuentos y Devoluciones', y verde para 'Ingresos Netos'
                cada barra en el gráfico representa una categoría específica, y la altura de cada barra corresponde al valor numérico de la categoría que representa. Además, las barras se han ajustado para facilitar la comparación visual entre las categorías.
                <br><br>
                Este gráfico nos ayuda a evaluar rápidamente la relación entre los ingresos brutos generados, los descuentos aplicados y las ganancias netas resultantes. Si las barras rojas son significativamente altas en comparación con las demás, puede ser útil investigar las razones detrás de los descuentos y devoluciones para mejorar nuestra rentabilidad.
            </p>
        </div>

        <!-- Add other charts and their explanations here -->
        <div class="bar">
            <canvas id="myChart2"></canvas>
        </div>
        <div class="explanation">
            <h2>Gráfico de Dona</h2>
            <p>
                El gráfico de dona que estás viendo muestra la distribución de la Utilidad Bruta y los Gastos de nuestra empresa. Este tipo de gráfico es ideal para visualizar la proporción entre dos categorías diferentes.
            </p>
            <p>
                La <strong>'Utilidad Bruta'</strong> representa los ingresos generados por nuestras operaciones comerciales antes de descontar los costos de producción. Es un indicador clave que refleja la rentabilidad de nuestras actividades principales.
            </p>
            <p>
                Los <strong>'Gastos'</strong> comprenden todos los costos y gastos asociados con nuestras operaciones, incluyendo salarios, impuestos, suministros y otros gastos operativos.
            </p>
            <p>
                Al observar el gráfico, puedes notar que la parte azul representa la Utilidad Bruta, mientras que la parte roja representa los Gastos. Si la Utilidad Bruta es mayor que los Gastos, tendremos una ganancia neta positiva, lo cual es una señal saludable para el negocio. Por otro lado, si los Gastos superan la Utilidad Bruta, es importante que analicemos nuestras operaciones para optimizar la eficiencia y mejorar nuestra rentabilidad.
            </p>
        </div>

        <div class="bar">
            <canvas id="myChart"></canvas>
        </div>
        <div class="explanation">
            <h2>Gráfico de Pastel</h2>
            <p>
                El gráfico de tipo pie que estás viendo representa la distribución de los ingresos y costos de nuestra empresa, así como la Utilidad Bruta resultante. Este tipo de gráfico es ideal para visualizar la proporción de diferentes componentes en un conjunto de datos.
            </p>
            <ul>
                <li>
                    <strong>Ingresos Netos:</strong> Esta porción del gráfico (representada en azul) muestra la cantidad total de ingresos generados por nuestra empresa después de descontar los costos de ventas y otros gastos operativos. Los ingresos netos son un indicador clave de la rentabilidad general de nuestro negocio.
                </li>
                <li>
                    <strong>Costo de Ventas:</strong> Esta parte del gráfico (representada en naranja) muestra el costo total de los bienes y servicios vendidos por nuestra empresa. Es importante mantener este costo bajo para maximizar nuestra Utilidad Bruta.
                </li>
                <li>
                    <strong>Utilidad Bruta:</strong> Esta sección del gráfico (representada en rojo) muestra el resultado de restar el Costo de Ventas de los Ingresos Netos. La Utilidad Bruta refleja la eficiencia de nuestras operaciones comerciales antes de considerar otros gastos generales.
                </li>
            </ul>
            <p>
                Observar este gráfico nos permite comprender rápidamente la composición de nuestras ganancias y gastos principales. Un porcentaje mayor de Ingresos Netos y Utilidad Bruta en comparación con el Costo de Ventas es un signo positivo de una gestión financiera sólida.
            </p>
        </div>
        <div class="bar">
            <canvas id="myChart4"></canvas>
        </div>

    @else
        <p>No hay datos para generar gráficos.</p>
    @endif
</div>





<script>
    document.addEventListener("DOMContentLoaded", function() {
            // Datos para los gráficos
            const graficoData = <?php echo json_encode($data); ?>;


            new Chart("myChart4", {
                type: 'bar',
                data: {
                labels: ['Gasto 1', 'Gasto 2', 'Gasto 3', 'Gasto 4', 'Gasto 5'],
                datasets: [{
                    label: 'Gastos',
                    data: [graficoData.type_purchase, graficoData.total_gasto],
                    backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    ],
                    borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1,
                }],
                },
                options: {
                scales: {
                    y: {
                    beginAtZero: true
                    }
                }
                }
            });

            // Configuración del gráfico de pastel
            const data = {
                labels: ['Ingresos Netos', 'Costo de Ventas', 'Gastos'],
                datasets: [{
                    label: 'Ventas Semanales',
                    data: [graficoData.ingresoN, -graficoData.costSale, -graficoData.gastos],
                    backgroundColor: ["#3498db", "#FFA500", "#e74c3c"],
                }]
            };

            const config = {
                type: 'pie',
                data,
                options: {
                    plugins: {
                        tooltip: {
                            enabled: true
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                const datapoints = context.dataset.data;
                                function totalSum(total, datapoint) {
                                    return total + datapoint;
                                }
                                const totalValue = datapoints.reduce(totalSum, 0);
                                const percentageValue = ((value / totalValue) * 100).toFixed(1);
                                return percentageValue + '%';
                            }
                        }
                    }
                }
            };

            // Inicializa y renderiza el gráfico de pastel en el lienzo con el ID "myChart"
            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
            
            // Gráfico de barras
            new Chart("myChart1", {
                type: "bar",
                data: {
                    labels: ["Ingresos Brutos", "Descuentos y devoluciones", "Ingresos Netos"],
                    datasets: [
                        {
                            label: "Ingresos Brutos",
                            data: [graficoData.ingresoB, 0, 0],
                            backgroundColor: "#3498db",
                        },
                        {
                            label: "Descuentos y devoluciones",
                            data: [0, graficoData.descDevo, 0],
                            backgroundColor: "#e74c3c",
                        },
                        {
                            label: "Ingresos Netos",
                            data: [0, 0, graficoData.ingresoN],
                            backgroundColor: "#2ecc71",
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                    barPercentage: 0.9, // Ajusta el ancho de las barras
                    categoryPercentage: 0.9, // Ajusta el espaciado entre las categorías
                },
            });

            // Gráfico de pastel
            new Chart("myChart2", {
                type: "doughnut",
                data: {
                    labels: ["Costo", "Gastos"],
                    datasets: [
                        {
                            label: "Utilidad",
                            data: [graficoData.costSale, -graficoData.gastos],
                            backgroundColor: ["#3498db", "#e74c3c"],
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });



        });
        

</script>


<script>
    // Función para actualizar la utilidad operativa
    function actualizarUtilidadOperativa(nuevoValor) {
        const utilidadOperativaSpan = document.getElementById('utilidad-operativa-valor');
        const esNegativo = nuevoValor < 0;
        const valorFormateado = esNegativo ? '(S/. ' + Math.abs(nuevoValor).toFixed(2) + ')' : 'S/. ' + nuevoValor.toFixed(2);
        utilidadOperativaSpan.textContent = valorFormateado;
    }

    // Resto del script...
</script>

<script>
    // Función para actualizar el valor del gasto de personal
    function actualizarGastoPersonal(nuevoValor) {
        const gastoPersonalSpan = document.getElementById('gasto-personal-valor');
        gastoPersonalSpan.textContent = '(S/. ' + nuevoValor.toFixed(2) + ')';
    }

    // Función para agregar el gasto de personal y actualizar la utilidad operativa
    function agregarGastoPersonal() {
        const gastoPersonalInput = document.getElementById('gastos-personal');
        const gastoPersonalValor = parseFloat(gastoPersonalInput.value);

        // Check if the entered value is a valid number
        if (isNaN(gastoPersonalValor)) {
            document.getElementById('error-message').textContent = 'Por favor, ingresa un valor numérico válido.';
            return;
        }

        // Clear any previous error messages
        document.getElementById('error-message').textContent = '';

        // Actualizar el valor del gasto de personal
        actualizarGastoPersonal(gastoPersonalValor);

        // Obtener el valor original de la utilidad operativa
        const utilidadOperativaOriginal = parseFloat(document.querySelector('.item-value[data-valor-original]').getAttribute('data-valor-original'));

        // Restar el gasto de personal a la utilidad operativa original
        const nuevaUtilidadOperativa = utilidadOperativaOriginal - gastoPersonalValor;

        // Mostrar la nueva utilidad operativa con el formato adecuado
        actualizarUtilidadOperativa(nuevaUtilidadOperativa);

        // Clear the input field
        gastoPersonalInput.value = '';
    }

    // Función para formatear la utilidad operativa al cargar la página
    document.addEventListener("DOMContentLoaded", function() {
        const utilidadOperativaOriginal = parseFloat(document.querySelector('.item-value[data-valor-original]').getAttribute('data-valor-original'));
        actualizarUtilidadOperativa(utilidadOperativaOriginal);
    });






















    

    
    // function agregarGastos() {
    //     const gastosPersonalInput = document.getElementById('gastos-personal');
    //     const gastoPersonalValue = parseFloat(gastosPersonalInput.value);
        
    //     if (isNaN(gastoPersonalValue)) {
    //         document.getElementById('error-message').textContent = 'Ingrese un valor numérico válido.';
    //         return;
    //     }

    //     // Determinar si la utilidad operativa es negativa
    //     const esNegativo = nuevaUtilidadOperativa < 0;
    
    //     const gastoPersonalSpan = document.getElementById('gasto-personal-value');
    
    //     // Agregar el gasto de personal al DOM
    //     gastoPersonalSpan.textContent = `(S/. ${gastoPersonalValue.toFixed(2)})`;
    
    //     // Calcular y restar el gasto de personal de la utilidad operativa (usar el valor inicial)
    //     const nuevaUtilidadOperativa = utilidadOperativaInicial - gastoPersonalValue;



    //     // Mostrar la utilidad operativa con el formato adecuado
    //     const utilidadOperativaMostrar = esNegativo ? `(S/. ${Math.abs(nuevaUtilidadOperativa).toFixed(2)})` : `S/. ${nuevaUtilidadOperativa.toFixed(2)}`;

    //     document.getElementById('utilidad-operativa-value').textContent = utilidadOperativaMostrar;


        
    //     // Limpiar el campo de texto y el mensaje de error
    //     gastosPersonalInput.value = '';
    //     document.getElementById('error-message').textContent = '';
    // }
    
    
</script>






@endsection