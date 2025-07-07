<?php
// session_start();
// if (!isset($_SESSION['user'])) {
//     header('Location: login.php');
//     exit;
// }
$products = json_decode(file_get_contents('products.json'), true) ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Portal</a>
        <div class="d-flex">
            <a href="/grupo5/admin" class="btn btn-outline-primary btn-sm me-3">Productos</a>
            <a href="/grupo5" class="btn btn-outline-primary btn-sm me-3">Usuarios</a>
            <a href="/grupo5" class="btn btn-outline-primary btn-sm me-3">Volver a la Tienda</a>
            <span class="me-3">Usuario: <?php //echo $_SESSION['user']; ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h4>Registrar Producto</h4>
            <form method="post" action="register_product.php">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Precio</label>
                    <input type="number" step="0.01" class="form-control" name="price" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select class="form-select" name="category" required>
                        <option value="electronics">Electrónicos</option>
                        <option value="clothing">Ropa</option>
                        <option value="home">Hogar</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen (URL o ruta)</label>
                    <input type="text" class="form-control" name="image" required>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Producto</button>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Productos Registrados</h4>
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo ucfirst($product['category']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning me-1" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['category']; ?>', '<?php echo addslashes($product['image']); ?>')">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para editar producto -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="post" action="edit_product.php">
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="editPrice" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" id="editCategory" name="category" required>
                            <option value="electronics">Electrónicos</option>
                            <option value="clothing">Ropa</option>
                            <option value="home">Hogar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen (URL o ruta)</label>
                        <input type="text" class="form-control" id="editImage" name="image" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editProduct(id, name, price, category, image) {
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editPrice').value = price;
    document.getElementById('editCategory').value = category;
    document.getElementById('editImage').value = image;
    
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

function deleteProduct(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        // Crear un formulario oculto para enviar la solicitud de eliminación
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete_product.php';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</body>
</html>
