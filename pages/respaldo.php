<?php 

date_default_timezone_set('America/Caracas');

// Configuración de la base de datos
$db_server = "localhost"; 
$db_name = "basecelulares"; 
$db_username = "root"; 
$db_password = "joel21"; // Cambia la contraseña aquí

// Nombre del archivo de respaldo
$filename = "basecelulares_" . date("d-m-Y_H-i-s") . ".sql";

// Conexión a la base de datos
$dbconnection = new mysqli($db_server, $db_username, $db_password, $db_name);

// Verificar conexión
if ($dbconnection->connect_error) {
    die("Conexión fallida: " . $dbconnection->connect_error);
}

// Función para ejecutar consultas y manejar errores
function query($query_string) {
    global $dbconnection;
    $result = $dbconnection->query($query_string);
    if (!$result) {
        die("Error en la consulta: " . $dbconnection->error);
    }
    return $result;
}

// Función para obtener el volcado de una tabla
function fetch_table_dump_sql($table, $fp) {
    global $dbconnection;

    // Estructura de la tabla
    $createTableQuery = query("SHOW CREATE TABLE `$table`");
    $createTableRow = $createTableQuery->fetch_assoc();
    
    fwrite($fp, "-- Estructura para la tabla `$table`\n");
    fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");
    fwrite($fp, $createTableRow['Create Table'] . ";\n\n");

    // Datos de la tabla
    fwrite($fp, "-- Datos para la tabla `$table`\n");
    fwrite($fp, "LOCK TABLES `$table` WRITE;\n");

    $dataQuery = query("SELECT * FROM `$table`");
    
    while ($row = $dataQuery->fetch_assoc()) {
        $values = array_map(function($value) use ($dbconnection) {
            return is_null($value) ? 'NULL' : "'" . $dbconnection->real_escape_string($value) . "'";
        }, array_values($row));
        
        fwrite($fp, "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n");
    }

    fwrite($fp, "UNLOCK TABLES;\n\n");
}

// Inicio del respaldo
echo "<center><h1>Respaldo de la base de Datos</h1></center><br><strong>";
echo "- Base de Datos: '$db_name' en '$db_server'.<br>";

$fileHandle = fopen($filename, 'w');
if (!$fileHandle) {
    die("- No se pudo crear el archivo '$filename'. Asegúrate de tener permisos de escritura.<br>");
}

$resultTables = query("SHOW TABLES");
if ($resultTables->num_rows === 0) {
    echo "- No hay tablas en la base de datos para respaldar.<br>";
} else {
    while ($row = $resultTables->fetch_array()) {
        fetch_table_dump_sql($row[0], $fileHandle);
    }
}

fclose($fileHandle);
echo "- Respaldo completado exitosamente. Archivo guardado como: <strong><a href='$filename'>$filename</a></strong><br>";
?>