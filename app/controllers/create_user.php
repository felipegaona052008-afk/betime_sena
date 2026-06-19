<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "empresa";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Conexión fallida"]));
}
$conn->set_charset("utf8");

// Capturar los datos enviados por POST
$nombre = $_POST['nombre'] ?? null;
$email = $_POST['email'] ?? null;
$direccion = $_POST['direccion'] ?? null;
$compania = $_POST['compania'] ?? null;
$estado = $_POST['estado'] ?? null;

if ($nombre && $email) {
    // Sentencia SQL para insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (nombre, email, direccion, compania, estado) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $email, $direccion, $compania, $estado);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario registrado con éxito."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar el usuario en la base de datos (Posible email duplicado)."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Datos obligatorios incompletos."]);
}

$conn->close();
?>