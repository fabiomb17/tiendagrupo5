<?php
// session_start();
$error = '';
// // Configuración de la base de datos
// $host = 'localhost';
// $db = 'grupo5'; // Cambia por el nombre de tu base de datos
// $user = 'root'; // Cambia por tu usuario de MariaDB
// $pass = '';
// $mysqli = new mysqli($host, $user, $pass, $db);
// if ($mysqli->connect_errno) {
//     die('Error de conexión a la base de datos: ' . $mysqli->connect_error);
// }
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $username = $_POST['username'] ?? '';
//     $password = $_POST['password'] ?? '';
//     $stmt = $mysqli->prepare('SELECT * FROM usuarios WHERE username = ? AND password = ?');
//     $stmt->bind_param('ss', $username, $password);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if ($result->num_rows === 1) {
//         $_SESSION['user'] = $username;
//         header('Location: index.php');
//         exit;
//     } else {
//         $error = 'Usuario o contraseña incorrectos';
//     }
//     $stmt->close();
// }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">Iniciar Sesión</div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
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
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="register_user.php">Registrar usuario</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
