<?php 

if ($menu == 1) {
    $botonProductoActivo = 'btn btn-outline-primary';
    $botonUsuariosActivo = 'btn btn-primary';
    $botondashboardActivo = 'btn btn-outline-primary';
}elseif ($menu == 2) {
    $botonProductoActivo = 'btn btn-outline-primary';
    $botonUsuariosActivo = 'btn btn-outline-primary';
    $botondashboardActivo = 'btn btn-primary';
}else{
    $botonProductoActivo = 'btn btn-primary';
    $botonUsuariosActivo = 'btn btn-outline-primary';
    $botondashboardActivo = 'btn btn-outline-primary';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/tiendagrupo5/admin"><i class="fas fa-tools me-2"></i>Admin Portal</a>
        <div class="d-flex">
            <a href="/tiendagrupo5/admin" class="btn btn-sm me-3 <?php echo $botondashboardActivo; ?>">
                <i class="fas fa-chart-bar me-1"></i>Dashboard
            </a>
            <a href="/tiendagrupo5/admin/products.php" class="btn btn-sm me-3 <?php echo $botonProductoActivo; ?>">
                <i class="fas fa-box me-1"></i>Productos
            </a>
            <a href="/tiendagrupo5/admin/users.php" class="btn btn-sm me-3 <?php echo $botonUsuariosActivo; ?>">
                <i class="fas fa-users me-1"></i>Usuarios
            </a>
            <a href="/tiendagrupo5" class="btn btn-outline-secondary btn-sm me-3">
                <i class="fas fa-store me-1"></i>Volver a la Tienda
            </a>
            <span class="me-3">Usuario: <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>Cerrar sesión
            </a>
        </div>
    </div>
</nav>