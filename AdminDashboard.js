// AdminDashboard.js

document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    loadProducts();
    loadAreas();
    loadOrders();
    loadInquiries();

    document.getElementById('product-form').addEventListener('submit', createProduct);
    document.getElementById('area-form').addEventListener('submit', createArea);
    document.getElementById('admin-form').addEventListener('submit', createAdmin);
    document.getElementById('category-form').addEventListener('submit', createCategory);
    document.getElementById('edit-product-form').addEventListener('submit', editProduct);
});

function showSection(sectionId) {
    document.querySelectorAll('.section').forEach(section => {
        section.style.display = 'none';
    });
    document.getElementById(sectionId).style.display = 'block';
}


function loadCategories() {
    fetch('/GforGas Website/api/categories.php')
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('product-category');
            const editCategorySelect = document.getElementById('edit-product-category');
            categorySelect.innerHTML = '';
            editCategorySelect.innerHTML = '';
            data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.category_id;
                option.textContent = category.name;
                categorySelect.appendChild(option);

                const editOption = option.cloneNode(true);
                editCategorySelect.appendChild(editOption);
            });
        })
        .catch(error => console.error('Error loading categories:', error));
}


function loadProducts() {
    fetch('/GforGas Website/api/products.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Fetched products:', data); // Debugging output
            if (!Array.isArray(data)) {
                throw new Error('Expected an array but got: ' + JSON.stringify(data));
            }
            
            const productsList = document.getElementById('products-list');
            productsList.innerHTML = '';
            data.forEach(product => {
                const productDiv = document.createElement('div');
                productDiv.textContent = `${product.name} - ${product.price}`;
                
                const editButton = document.createElement('button');
                editButton.textContent = 'Edit';
                editButton.onclick = () => loadProductForEdit(product);
                productDiv.appendChild(editButton);

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.onclick = () => deleteProduct(product.product_id);
                productDiv.appendChild(deleteButton);

                productsList.appendChild(productDiv);
            });
        })
        .catch(error => console.error('Error loading products:', error));
}

function loadProductForEdit(product) {
    showSection('edit-product');
    document.getElementById('edit-product-id').value = product.product_id;
    document.getElementById('edit-product-name').value = product.name;
    document.getElementById('edit-product-description').value = product.description;
    document.getElementById('edit-product-price').value = product.price;
    document.getElementById('edit-product-stock').value = product.stock_quantity;
    document.getElementById('edit-product-category').value = product.category_id;
    document.getElementById('edit-product-image').value = product.image_url;
    document.getElementById('edit-product-featured').checked = product.is_featured;
}

function editProduct(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const productData = {
        product_id: formData.get('product-id'),
        name: formData.get('product-name'),
        description: formData.get('product-description'),
        price: formData.get('product-price'),
        stock_quantity: formData.get('product-stock'),
        category_id: formData.get('product-category'),
        image_url: formData.get('product-image'),
        is_featured: formData.get('product-featured') ? 1 : 0,
    };

    fetch('/GforGas Website/api/products.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(productData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadProducts();
            alert('Product updated successfully');
            showSection('view-products');
        } else {
            alert('Error updating product: ' + data.message);
        }
    })
    .catch(error => console.error('Error updating product:', error));
}
function loadAreas() {
    fetch('/GforGas Website/api/areas.php')
        .then(response => response.json())
        .then(data => {
            const areasList = document.getElementById('areas-list');
            areasList.innerHTML = '';
            data.forEach(area => {
                const areaDiv = document.createElement('div');
                areaDiv.textContent = area.area_name;
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.onclick = () => deleteArea(area.area_id);
                areaDiv.appendChild(deleteButton);
                areasList.appendChild(areaDiv);
            });
        })
        .catch(error => console.error('Error loading areas:', error));
}

function loadOrders() {
    fetch('/GforGas%20Website/api/orders.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Fetched orders:', data); // Debugging output

            const pendingOrders = document.getElementById('pending-orders');
            const completedOrders = document.getElementById('completed-orders');
            const cancelledOrders = document.getElementById('cancelled-orders');
            pendingOrders.innerHTML = '';
            completedOrders.innerHTML = '';
            cancelledOrders.innerHTML = '';

            data.forEach(order => {
                const orderDiv = document.createElement('div');
                orderDiv.textContent = `Order #${order.order_id} - ${order.status}`;
                const updateStatusButton = document.createElement('button');
                updateStatusButton.textContent = 'Update Status';
                updateStatusButton.onclick = () => updateOrderStatus(order.order_id);
                orderDiv.appendChild(updateStatusButton);

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.onclick = () => deleteOrder(order.order_id);
                orderDiv.appendChild(deleteButton);

                switch(order.status.toLowerCase()) {
                    case 'pending':
                        pendingOrders.appendChild(orderDiv);
                        break;
                    case 'completed':
                        completedOrders.appendChild(orderDiv);
                        break;
                    case 'cancelled':
                        cancelledOrders.appendChild(orderDiv);
                        break;
                    default:
                        console.warn(`Unknown status: ${order.status}`);
                }
            });
        })
        .catch(error => console.error('Error loading orders:', error));
}

function loadInquiries() {
    fetch('/GforGas Website/api/inquiries.php')
        .then(response => response.json())
        .then(data => {
            const inquiriesList = document.getElementById('inquiries-list');
            inquiriesList.innerHTML = '';
            data.forEach(inquiry => {
                const inquiryDiv = document.createElement('div');
                inquiryDiv.textContent = `${inquiry.name} - ${inquiry.subject}`;
                inquiriesList.appendChild(inquiryDiv);
            });
        })
        .catch(error => console.error('Error loading inquiries:', error));
}

function createProduct(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const productData = {
        name: formData.get('product-name'),
        description: formData.get('product-description'),
        price: formData.get('product-price'),
        stock_quantity: formData.get('product-stock'),
        category_id: formData.get('product-category'),
        image_url: formData.get('product-image'),
        is_featured: formData.get('product-featured') ? 1 : 0,
    };

    fetch('/GforGas Website/api/products.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(productData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadProducts();
            alert('Product created successfully');
            event.target.reset();
        } else {
            alert('Error creating product: ' + data.message);
        }
    })
    .catch(error => console.error('Error creating product:', error));
}


function createArea(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const areaData = {
        area_name: formData.get('area-name'),
    };

    fetch('/GforGas Website/api/areas.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(areaData),
    })
    .then(response => response.json())
    .then(() => {
        loadAreas();
        alert('Area created successfully');
        event.target.reset();
    })
    .catch(error => console.error('Error creating area:', error));
}

function createAdmin(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const adminData = {
        email: formData.get('admin-email'),
        password: formData.get('admin-password'),
        full_name: formData.get('admin-fullname'),
    };

    console.log('Sending data:', JSON.stringify(adminData)); // Debugging output

    fetch('/GforGas%20Website/api/admins.php', { // Ensure this path is correct
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(adminData),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log('Received response:', data); // Debugging output
        alert('Admin created successfully');
        event.target.reset();
    })
    .catch(error => {
        console.error('Error creating admin:', error);
    });
}


function createCategory(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const categoryData = {
        name: formData.get('category-name'),
    };

    fetch('/GforGas Website/api/categories.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(categoryData),
    })
    .then(response => response.json())
    .then(() => {
        loadCategories();
        alert('Category created successfully');
        event.target.reset();
    })
    .catch(error => console.error('Error creating category:', error));
}

function deleteProduct(productId) {
    fetch(`/GforGas Website/api/products.php?product_id=${productId}`, {
        method: 'DELETE',
    })
    .then(() => {
        loadProducts();
        alert('Product deleted successfully');
    })
    .catch(error => console.error('Error deleting product:', error));
}

function deleteArea(areaId) {
    fetch(`/GforGas Website/api/areas.php?area_id=${areaId}`, {
        method: 'DELETE',
    })
    .then(() => {
        loadAreas();
        alert('Area deleted successfully');
    })
    .catch(error => console.error('Error deleting area:', error));
}

function deleteOrder(orderId) {
    fetch(`/GforGas%20Website/api/orders.php?order_id=${orderId}`, {
        method: 'DELETE',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(() => {
        loadOrders();
        alert('Order deleted successfully');
    })
    .catch(error => console.error('Error deleting order:', error));
}


function updateOrderStatus(orderId) {
    const newStatus = prompt('Enter new status (Pending, Completed, Cancelled):');
    if (newStatus) {
        fetch(`/GforGas%20Website/api/orders.php?order_id=${orderId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: newStatus }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(() => {
            loadOrders();
            alert('Order status updated successfully');
        })
        .catch(error => console.error('Error updating order status:', error));
    }
}