<?php
// Configuración de la conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "empresa";

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Validar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Configurar codificación de caracteres a UTF-8 para evitar problemas con acentos o eñes
$conn->set_charset("utf8");

// Consultar los usuarios
$sql = "SELECT id, nombre, email, direccion, compania, estado FROM usuarios";
$result = $conn->query($sql);

$usuarios = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

// Indicar al navegador que la respuesta es un JSON
header('Content-Type: application/json');

// Imprimir los datos en formato JSON
echo json_encode($usuarios);

// Cerrar conexión
$conn->close();
?>