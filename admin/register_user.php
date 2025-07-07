<?php
$error = '';
$success = '';
// Configuración de la base de datos
$host = 'localhost';
$db = 'grupo5'; // Cambia por el nombre de tu base de datos
$user = 'root'; // Cambia por tu usuario de MariaDB
$pass = '';
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    die('Error de conexión a la base de datos: ' . $mysqli->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // Verificar si el usuario ya existe
    $stmt = $mysqli->prepare('SELECT id FROM usuarios WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $error = 'El usuario ya existe.';
    } else {
        $stmt->close();
        $stmt = $mysqli->prepare('INSERT INTO usuarios (username, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $username, $password);
        if ($stmt->execute()) {
            $success = 'Usuario registrado correctamente.';
        } else {
            $error = 'Error al registrar usuario.';
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">Registrar Usuario</div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Registrar</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="login.php">Volver al login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
