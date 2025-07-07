 // Datos de ejemplo para los productos
        const productsData = [
            {
                id: 1,
                name: "Smartphone X",
                price: 55.02,
                category: "electronics",
                image: "img/smartphone.png"
            },
            {
                id: 2,
                name: "Laptop Pro",
                price: 1200.99,
                category: "electronics",
                image: "img/laptop.png"
            },
            {
                id: 3,
                name: "Camisa Casual",
                price: 25.50,
                category: "clothing",
                image: "img/camisa.png"
            },
            {
                id: 4,
                name: "Sofá Moderno",
                price: 599.99,
                category: "home",
                image: "img/sofa.png"
            },
            {
                id: 5,
                name: "Auriculares Inalámbricos",
                price: 89.99,
                category: "electronics",
                image: "img/auriculares.png"
            },
            {
                id: 6,
                name: "Mesa de Centro",
                price: 150.00,
                category: "home",
                image: "img/mesa.png"
            }
        ];
        
        // Carrito de compras
        let cart = [];
        
        // DOM Elements
        const productsGrid = document.getElementById('productsGrid');
        const categoriesList = document.getElementById('categoriesList');
        const cartButton = document.getElementById('cartButton');
        const cartSidebar = document.getElementById('cartSidebar');
        const cartOverlay = document.getElementById('cartOverlay');
        const closeCart = document.getElementById('closeCart');
        const cartItems = document.getElementById('cartItems');
        const cartTotalItems = document.getElementById('cartTotalItems');
        const cartTotalAmount = document.getElementById('cartTotalAmount');
        const checkoutButton = document.getElementById('checkoutButton');
        const cartBadge = document.getElementById('cartBadge');
        
        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            renderProductsGrid();
            setupEventListeners();
        });
        
        // Configurar event listeners
        function setupEventListeners() {
            // Filtrado por categoría
            categoriesList.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const category = this.dataset.category;
                    
                    // Actualizar active class
                    categoriesList.querySelectorAll('.nav-link').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // Filtrar productos
                    filterProducts(category);
                });
            });
            
            // Carrito
            cartButton.addEventListener('click', toggleCart);
            closeCart.addEventListener('click', toggleCart);
            cartOverlay.addEventListener('click', toggleCart);
            
            // Completar compra
            checkoutButton.addEventListener('click', completePurchase);
        }
        
        // Renderizar grid de productos en el área principal
        function renderProductsGrid(filteredProducts = productsData) {
            productsGrid.innerHTML = '';
            
            if (filteredProducts.length === 0) {
                productsGrid.innerHTML = '<div class="col-12 text-center py-5"><h5>No hay productos en esta categoría</h5></div>';
                return;
            }
            
            filteredProducts.forEach(product => {
                const col = document.createElement('div');
                col.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
                
                col.innerHTML = `
                    <div class="card product-card h-100">
                        <img src="${product.image}" class="card-img-top product-img p-3" alt="${product.name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="card-text text-muted">ID: ${product.id}</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">DOP ${product.price.toFixed(2)}</span>
                                <button class="btn btn-primary add-to-cart" data-id="${product.id}">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                productsGrid.appendChild(col);
            });
            
            // Agregar event listeners a los botones de añadir al carrito
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.dataset.id);
                    addToCart(productId);
                });
            });
        }
        
        // Filtrar productos por categoría
        function filterProducts(category) {
            if (category === 'all') {
                renderProductsGrid(productsData);
                return;
            }
            
            const filteredProducts = productsData.filter(product => product.category === category);
            renderProductsGrid(filteredProducts);
        }
        
        // Añadir producto al carrito
        function addToCart(productId) {
            const product = productsData.find(p => p.id === productId);
            
            if (!product) return;
            
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    image: product.image
                });
            }
            
            updateCart();
            showCartNotification();
        }
        
        // Actualizar el carrito y la UI
        function updateCart() {
            // Actualizar badge
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            cartBadge.textContent = totalItems;
            
            // Actualizar sidebar del carrito
            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p>Tu carrito está vacío</p>
                    </div>
                `;
                checkoutButton.disabled = true;
            } else {
                cartItems.innerHTML = '';
                
                cart.forEach(item => {
                    const cartItem = document.createElement('div');
                    cartItem.className = 'cart-item mb-3 border-bottom pb-3';
                    cartItem.innerHTML = `
                        <div class="d-flex">
                            <img src="${item.image}" class="rounded me-3" width="60" height="60" style="object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${item.name}</h6>
                                <small class="text-muted">DOP ${item.price.toFixed(2)} c/u</small>
                            </div>
                            <div class="text-end">
                                <div class="d-flex align-items-center mb-1">
                                    <button class="btn btn-sm btn-outline-secondary decrease-quantity" data-id="${item.id}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="mx-2">${item.quantity}</span>
                                    <button class="btn btn-sm btn-outline-secondary increase-quantity" data-id="${item.id}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <span class="fw-bold">DOP ${(item.price * item.quantity).toFixed(2)}</span>
                            </div>
                        </div>
                    `;
                    
                    cartItems.appendChild(cartItem);
                });
                
                // Habilitar botón de compra
                checkoutButton.disabled = false;
            }
            
            // Actualizar resumen
            const totalAmount = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            cartTotalItems.textContent = cart.reduce((total, item) => total + item.quantity, 0);
            cartTotalAmount.textContent = `DOP ${totalAmount.toFixed(2)}`;
            
            // Agregar event listeners a los botones de cantidad
            document.querySelectorAll('.increase-quantity').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.dataset.id);
                    updateCartItemQuantity(productId, 1);
                });
            });
            
            document.querySelectorAll('.decrease-quantity').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.dataset.id);
                    updateCartItemQuantity(productId, -1);
                });
            });
        }
        
        // Actualizar cantidad de un item en el carrito
        function updateCartItemQuantity(productId, change) {
            const itemIndex = cart.findIndex(item => item.id === productId);
            
            if (itemIndex === -1) return;
            
            cart[itemIndex].quantity += change;
            
            // Eliminar item si la cantidad llega a 0
            if (cart[itemIndex].quantity <= 0) {
                cart.splice(itemIndex, 1);
            }
            
            updateCart();
        }
        
        // Mostrar/ocultar carrito
        function toggleCart() {
            cartSidebar.classList.toggle('show');
            cartOverlay.classList.toggle('show');
            
            // Bloquear scroll del body cuando el carrito está abierto
            if (cartSidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
        
        // Mostrar notificación al añadir al carrito
        function showCartNotification() {
            const notification = document.createElement('div');
            notification.className = 'position-fixed bottom-0 end-0 p-3';
            notification.style.zIndex = '1100';
            
            notification.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <strong class="me-auto">Producto añadido</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        El producto se ha añadido al carrito.
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Eliminar la notificación después de 3 segundos
            setTimeout(() => {
                notification.querySelector('.toast').classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
            
            // Permitir cerrar la notificación manualmente
            notification.querySelector('.btn-close').addEventListener('click', function() {
                notification.querySelector('.toast').classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            });
        }
        
        // Completar compra
        function completePurchase() {
            if (cart.length === 0) return;
            
            // Aquí iría la lógica para procesar la compra
            alert(`Compra completada! Total: ${cartTotalAmount.textContent}`);
            
            // Vaciar carrito
            cart = [];
            updateCart();
            toggleCart();
        }