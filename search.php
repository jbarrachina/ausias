<html>
    <head>
        <meta charset="UTF-8">
        <title>Bootstrap 101 Template</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link data-require="bootstrap-css@3.1.1" data-semver="3.1.1" rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
        <script data-require="bootstrap@3.1.1" data-semver="3.1.1" src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <script src="http://code.jquery.com/jquery.js"></script>
    </head>

    <body>
        <div class="container">
            <?php
            /*             * *********************************************************
             * script search.php
             * necesita un parámetro code
             * utiliza la base de datos glpi
             * ********************************************************* */

// incluir la conexión a la base de datos
            include 'conexion.php';
            $codigo = isset($_GET['code']) ? $_GET['code'] : "657880";//un parámetro

// Elegir los datos que deseamos recuperar de la tabla
            $query = <<<SQL
       SELECT com.name, serial, otherserial, glpi_plugin_fusioninventory_inventorycomputercomputers.last_fusioninventory_update, glpi_locations.completename 
           FROM glpi_computers com 
           LEFT JOIN glpi_plugin_fusioninventory_inventorycomputercomputers ON glpi_plugin_fusioninventory_inventorycomputercomputers.computers_id = com.id 
           LEFT JOIN glpi_locations ON com.locations_id = glpi_locations.id 
           WHERE otherserial = ? AND is_template = 0 AND is_deleted = 0
SQL;
//echo $query;

            if ($stmt = $conexion->prepare($query)) {
                $stmt->bind_param('s', $codigo);
                if (!$stmt->execute()) {
                    die('Error de ejecución de la consulta. ' . $conexion->error);
                }
// recoger los datos
                $stmt->bind_result($name, $serial, $otherserial, $fusion_update, $location);


//recorrido por el resultado de la consulta
                while ($stmt->fetch()) {
                    echo "<div class='row'>";
                    echo "<div class=\"col-md-2\"><h2 class=\"bg-primary\">$name</h2></div>";
                    echo "<div class=\"col-md-2\"><h4>Nº Serie: $serial</h4></div>";
                    echo "<div class='col-md-2'><h5>Nº Inventario: $otherserial</h5></div>";
                    echo "<div class='col-md-2'>Última actualización: $fusion_update</div>";
                    echo "<div class='col-md-2'>Ubicación: $location</div>\n";
                    echo "</div>";
                }
                // end table


                $stmt->close();
            } else {
                die('Imposible preparar la consulta. ' . $conexion->error);
            }
            ?>
        </div>
    </body>
</html>