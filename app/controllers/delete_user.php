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

// Capturar el ID enviado por método POST desde JavaScript
$id = $_POST['id'] ?? null;

if ($id) {
    // Consulta SQL para eliminar el registro
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario eliminado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al intentar eliminar el usuario."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "ID no proporcionado."]);
}

$conn->close();
?>