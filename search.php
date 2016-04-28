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
            /*             * ********************************************************
             * script search.php 
             * necesita un parámetro code
             * utiliza la base de datos glpi
             * ********************************************************* */

            function getStatus($status) {
                switch ($status) {
                    case 1: return "Sin Asignar";
                    case 2: return "Asignada";
                    case 3: return "Planificada";
                    case 4: return "En Espera";
                    case 5: return "Resuelta";
                    case 6: return "Cerrada";
                    default: return "Status Incorrecto";
                }
            }

// incluir la conexión a la base de datos
            include 'conexion.php';
            $codigo = isset($_GET['code']) ? $_GET['code'] : "368939"; //un parámetro
// Elegir los datos que deseamos recuperar de la tabla
            $query = <<<SQL
       SELECT com.id, com.name, com.SERIAL, otherserial, glpi_plugin_fusioninventory_inventorycomputercomputers.last_fusioninventory_update, glpi_locations.completename, glpi_computermodels.name, glpi_states.completename, glpi_items_devicememories.size
            FROM glpi_computers com
            LEFT JOIN glpi_plugin_fusioninventory_inventorycomputercomputers ON glpi_plugin_fusioninventory_inventorycomputercomputers.computers_id = com.id
            LEFT JOIN glpi_locations ON com.locations_id = glpi_locations.id
            LEFT JOIN glpi_computermodels ON com.computermodels_id = glpi_computermodels.id
            LEFT JOIN glpi_states ON com.states_id = glpi_states.id
	    LEFT JOIN glpi_items_devicememories ON glpi_items_devicememories.items_id = com.id
            WHERE otherserial = ? AND is_template =0 AND com.is_deleted = 0
SQL;
//echo $query;

            if ($stmt = $conexion->prepare($query)) {
                $stmt->bind_param('s', $codigo);
                if (!$stmt->execute()) {
                    die('Error de ejecución de la consulta. ' . $conexion->error);
                }
// recoger los datos
                $stmt->bind_result($pcid, $name, $serial, $otherserial, $fusion_update, $location, $model, $state, $ram);


//recorrido por el resultado de la consulta
                while ($stmt->fetch()) {
                    echo "<div class='row'>";
                    echo "<div class=\"col-md-3\">";
                    echo "<h2 class=\"bg-primary\">$name</h2></div>";
                    echo "<div class=\"col-md-4\">";
                    echo "<h4>Modelo: $model</h4>";
                    echo "<h4>Nº Serie: $serial</h4>";
                    echo "<h5>Nº Inventario: $otherserial</h5></div>";
                    echo "<div class='col-md-4'>Última actualización: $fusion_update";
                    echo "<p>Ubicación: $location</p>";
                    echo "<p>Estado: $state</p>";
                    echo "<p>RAM: $ram";
                    while ($stmt->fetch())
                        echo " + " . $ram;
                    echo "</p></div>";
                    echo "</div>";
                }
                // end table

                $stmt->close();
            } else {
                die('Imposible preparar la consulta. ' . $conexion->error);
            }

            $incidencia = false;

            $query = <<<SQL
                SELECT status, date, glpi_tickets.name, closedate, content, solution, concat(firstname, ' ', realname)
                FROM glpi_items_tickets
                LEFT JOIN glpi_tickets ON glpi_items_tickets.tickets_id = glpi_tickets.id
                LEFT JOIN glpi_tickets_users ON glpi_items_tickets.tickets_id = glpi_tickets_users.tickets_id
                LEFT JOIN glpi_users ON glpi_users.id = glpi_tickets_users.users_id    
                WHERE glpi_items_tickets.items_id = ? AND glpi_tickets.is_deleted = 0 AND glpi_tickets_users.type = 2
SQL;

            if ($stmt = $conexion->prepare($query)) {
                $stmt->bind_param('s', $pcid);
                if (!$stmt->execute()) {
                    die('Error de ejecución de la consulta. ' . $conexion->error);
                }
// recoger los datos
                $stmt->store_result();
                $stmt->bind_result($status, $date, $name, $closedate, $content, $solution, $tecnician);


//recorrido por el resultado de la consulta
                while ($stmt->fetch()) {
                    echo "<div class='row'>";
                    echo "<div class=\"col-md-2\">";
                    echo "<h4 class=\"bg-primary\">" . getStatus($status) . "</h4>";
                    echo "<h4 class=\"bg-info\">" . $tecnician . "</h4>";
                    echo "</div>";
                    echo "<div class=\"col-md-8\">";
                    echo "<h5>Abierta: $date - Cerrada: $closedate </h5>";
                    echo "<h4>$name</h4>";
                    echo "<div>{$content}</div>";
                    echo "<div class=\"bg-success\">".htmlspecialchars_decode($solution)."</div>";
                    echo "</div>";
                    echo "</div>";
                    if ($status < 6) { //si hay incidencia sin cerrar
                        $incidencia = true;
                    }
                }
                // end table

                $stmt->close();
            } else {
                die('Imposible preparar la consulta. ' . $conexion->error);
            }

            if (!$incidencia) {
                ?>
                <p>
                    incidencias a:
                    <a href="mailto:moodle@ausiasmarch.net?Subject=Incidencia: <?= $name . ": " . $serial ?>" target="_top">Enviar ... </a>
                </p>
                <?php
            }
            ?>
        </div>
    </body>
</html>