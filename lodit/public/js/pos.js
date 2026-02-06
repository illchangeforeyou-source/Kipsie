// ==================== STATE ==================== 
let products = [];
let cart = [];
let allCategories = [];
let selectedCategoryId = null;
let currentProduct = null;
let currentAgeVerificationProduct = null;
let selectedDeliveryOption = null;
let currentOrderId = null;

// ==================== INITIALIZATION ==================== 
document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
    fetchCategories();
    setupEventListeners();
});

function setupEventListeners() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('category-filter');
    
    if (searchInput) {
        searchInput.addEventListener('input', () => renderProducts());
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', (e) => {
            selectedCategoryId = e.target.value ? parseInt(e.target.value) : null;
            renderProducts();
        });
    }
}

// ==================== FETCH & RENDER ==================== 
async function fetchProducts() {
    try {
        const response = await fetch('/medicines', { credentials: "same-origin" });
        if (!response.ok) throw new Error("Failed to fetch medicines");
        const data = await response.json();
        products = data.data || [];
        renderProducts();
    } catch (error) {
        console.error(error);
        alert("Error loading medicines: " + error.message);
    }
}

async function fetchCategories() {
    try {
        const response = await fetch('/categories', { credentials: "same-origin" });
        if (!response.ok) throw new Error("Failed to fetch categories");
        const data = await response.json();
        allCategories = data.data || [];
        renderCategoryFilters();
    } catch (error) {
        console.error(error);
        alert("Error loading categories");
    }
}

function renderCategoryFilters() {
    const filterSelect = document.getElementById("category-filter");
    if (!filterSelect) return;
    
    filterSelect.innerHTML = '<option value="">All Medicine</option>';
    const parentCategories = allCategories.filter(cat => !cat.parent_id);
    
    parentCategories.forEach(category => {
        const optgroup = document.createElement('optgroup');
        optgroup.label = category.name;
        
        const parentOption = document.createElement('option');
        parentOption.value = category.id;
        parentOption.textContent = category.name;
        optgroup.appendChild(parentOption);
        
        if (category.subcategories && category.subcategories.length > 0) {
            category.subcategories.forEach(sub => {
                const subOption = document.createElement('option');
                subOption.value = sub.id;
                subOption.textContent = `→ ${sub.name}`;
                optgroup.appendChild(subOption);
            });
        }
        
        filterSelect.appendChild(optgroup);
    });
}

function renderProducts() {
    const list = document.getElementById("product-list");
    if (!list) return;
    list.innerHTML = "";

    const searchTerm = document.getElementById("searchInput")?.value.toLowerCase() || "";
    
    let filtered = products.filter(p => 
        p.name.toLowerCase().includes(searchTerm)
    );
    
    if (selectedCategoryId) {
        filtered = filtered.filter(p => p.category_id === selectedCategoryId);
    }

    if (filtered.length === 0) {
        list.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #9ca3af;"><i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>No medicines found</div>';
        return;
    }

    filtered.forEach(product => {
        const card = createProductCard(product);
        list.appendChild(card);
    });
}

function createProductCard(product) {
    const col = document.createElement("div");
    col.className = "product-card";
    
    let imageUrl = '/foto/goodb.jpg';
    if (product.image && product.image.trim() !== '') {
        imageUrl = product.image.startsWith('http') || product.image.startsWith('/')
            ? product.image
            : '/storage/' + product.image;
    }
    
    const ageHTML = product.age_restriction 
        ? `<div class="age-badge">${product.age_restriction} Only</div>` 
        : '';
    
    col.innerHTML = `
        <img src="${imageUrl}" alt="${product.name}" onerror="this.src='/foto/goodb.jpg';">
        <div class="product-info">
            <div class="product-name">${product.name}</div>
            <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
            <div class="product-stock">Stock: ${product.stock}</div>
            ${ageHTML}
            <button class="add-to-cart-btn" onclick="showAddToCartOptions(${product.id})">
                <i class="bi bi-cart-plus"></i> Add to Cart
            </button>
        </div>
    `;
    
    return col;
}

// ==================== CART MANAGEMENT ==================== 
function showAddToCartOptions(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    // Show view modal first if age restricted
    if (product.age_restriction) {
        currentAgeVerificationProduct = product;
        showViewModal(productId);
    } else {
        addToCart(productId, 1);
    }
}

function addToCart(productId, quantity = 1) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    // Check stock
    if (product.stock < quantity) {
        alert(`Not enough stock! Only ${product.stock} available.`);
        return;
    }

    // Check if already in cart
    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        if (existingItem.quantity + quantity > product.stock) {
            return alert(`Cannot add. Only ${product.stock - existingItem.quantity} remaining.`);
        }
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: product.name,
            price: product.price,
            quantity: quantity,
            image: product.image
        });
    }

    updateCartUI();
    showNotification(`✅ Added ${quantity} x ${product.name}`);
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartUI();
}

function updateQuantity(productId, change) {
    const item = cart.find(i => i.id === productId);
    if (!item) return;
    
    const product = products.find(p => p.id === productId);
    const newQty = item.quantity + change;
    
    if (newQty < 1) {
        removeFromCart(productId);
    } else if (newQty > product.stock) {
        alert(`Only ${product.stock} available`);
    } else {
        item.quantity = newQty;
        updateCartUI();
    }
}

function updateCartUI() {
    const cartItems = document.getElementById("cart-items");
    const cartCount = document.getElementById("cart-count");
    const cartTotal = document.getElementById("cart-total");
    const checkoutBtn = document.getElementById("checkout-btn");
    
    if (!cartItems || !cartCount || !cartTotal) return;

    // Clear cart display
    cartItems.innerHTML = "";
    let total = 0;
    let itemCount = 0;

    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="cart-empty">
                <i class="bi bi-cart" style="font-size: 48px; color: #d1d5db; display: block; margin-bottom: 12px;"></i>
                <p>Your cart is empty</p>
                <p style="font-size: 12px; color: #d1d5db;">Add items to get started</p>
            </div>
        `;
        checkoutBtn.disabled = true;
    } else {
        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            itemCount += item.quantity;

            const cartItem = document.createElement("div");
            cartItem.className = "cart-item";
            cartItem.innerHTML = `
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">$${item.price.toFixed(2)} x ${item.quantity} = $${subtotal.toFixed(2)}</div>
                </div>
                <div class="cart-item-controls">
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">−</button>
                    <span class="qty-display">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                    <button class="remove-btn" onclick="removeFromCart(${item.id})" title="Remove">×</button>
                </div>
            `;
            cartItems.appendChild(cartItem);
        });

        checkoutBtn.disabled = false;
    }

    cartCount.textContent = itemCount;
    cartTotal.textContent = total.toFixed(2);
}

// ==================== AGE VERIFICATION ==================== 
function showAgeModal(product) {
    const modal = document.getElementById('ageModal');
    const subtitle = document.getElementById('ageModalSubtitle');
    subtitle.textContent = `This medicine (${product.name}) requires age verification`;
    
    document.getElementById('birthDate').value = '';
    document.getElementById('ageError').style.display = 'none';
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeAgeModal() {
    const modal = document.getElementById('ageModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    currentAgeVerificationProduct = null;
}

async function submitAgeVerification(event) {
    event.preventDefault();
    
    if (!currentAgeVerificationProduct) {
        closeAgeModal();
        return;
    }

    const birthDate = document.getElementById('birthDate').value;
    const errorMsg = document.getElementById('ageError');

    try {
        const response = await fetch('/verify-age', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                birth_date: birthDate,
                medicine_id: currentAgeVerificationProduct.id
            })
        });

        const data = await response.json();

        if (data.success && data.verified) {
            closeAgeModal();
            const quantity = parseInt(document.getElementById('view-quantity').value) || 1;
            addToCart(currentAgeVerificationProduct.id, quantity);
            bootstrap.Modal.getInstance(document.getElementById('viewModal')).hide();
        } else {
            errorMsg.style.display = 'block';
            errorMsg.textContent = `❌ You must be ${data.required_age || 'old'} years old to purchase this medicine`;
        }
    } catch (error) {
        console.error(error);
        errorMsg.style.display = 'block';
        errorMsg.textContent = 'Error verifying age';
    }
}

// ==================== CHECKOUT & PAYMENT ==================== 
async function proceedToCheckout() {
    if (!cart.length) {
        alert('Cart is empty!');
        return;
    }

    const customerName = window.catalogUsername || "Guest";
    const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch('/save-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                customer_name: customerName,
                items: cart,
                total: total
            }),
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            currentOrderId = data.order_id;
            showReceipt(data.order_id);
            cart = [];
            updateCartUI();
        } else {
            alert('Error placing order: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Error: ' + error.message);
    }
}

// ==================== RECEIPT ==================== 
async function showReceipt(orderId) {
    const modal = document.getElementById('receiptModal');
    const orderIdSpan = document.getElementById('receipt-order-id');
    
    orderIdSpan.textContent = orderId;
    resetDeliveryOptions();

    try {
        const response = await fetch(`/receipt/generate/${orderId}`, {
            credentials: 'same-origin'
        });

        if (response.ok) {
            const data = await response.json();
            document.getElementById('receiptContent').innerHTML = data.html || '<p>Receipt loading...</p>';
        }
    } catch (error) {
        console.error(error);
    }

    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeReceipt() {
    const modal = document.getElementById('receiptModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    currentOrderId = null;
}

function selectDeliveryOption(option) {
    selectedDeliveryOption = option;
    
    // Update UI
    document.querySelectorAll('.delivery-option').forEach(el => {
        el.classList.remove('active');
    });
    event.target.closest('.delivery-option').classList.add('active');

    const inputContainer = document.getElementById('deliveryInputContainer');
    const sendBtn = document.getElementById('sendReceiptBtn');
    const downloadBtn = document.getElementById('downloadReceiptBtn');
    const printBtn = document.getElementById('printReceiptBtn');
    const input = document.getElementById('deliveryInput');

    // Reset buttons
    sendBtn.style.display = 'none';
    downloadBtn.style.display = 'none';
    printBtn.style.display = 'none';
    inputContainer.style.display = 'none';

    if (option === 'email' || option === 'sms') {
        inputContainer.style.display = 'block';
        sendBtn.style.display = 'block';
        input.placeholder = option === 'email' ? 'Enter email address' : 'Enter phone number';
    } else if (option === 'download') {
        downloadBtn.style.display = 'block';
    } else if (option === 'print') {
        printBtn.style.display = 'block';
    }
}

function resetDeliveryOptions() {
    selectedDeliveryOption = null;
    document.querySelectorAll('.delivery-option').forEach(el => {
        el.classList.remove('active');
    });
    document.getElementById('deliveryInputContainer').style.display = 'none';
    document.getElementById('sendReceiptBtn').style.display = 'none';
    document.getElementById('downloadReceiptBtn').style.display = 'none';
    document.getElementById('printReceiptBtn').style.display = 'none';
}

async function sendReceipt() {
    const input = document.getElementById('deliveryInput').value;
    if (!input) {
        alert('Please enter ' + (selectedDeliveryOption === 'email' ? 'email' : 'phone'));
        return;
    }

    const endpoint = selectedDeliveryOption === 'email' ? '/receipt/send-email' : '/receipt/send-sms';
    const field = selectedDeliveryOption === 'email' ? 'email' : 'phone';

    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                order_id: currentOrderId,
                [field]: input
            }),
            credentials: 'same-origin'
        });

        const data = await response.json();
        if (data.success) {
            showNotification(data.message);
            setTimeout(() => closeReceipt(), 1500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Error sending receipt');
    }
}

function downloadReceipt() {
    window.location.href = `/receipt/download/${currentOrderId}`;
    setTimeout(() => closeReceipt(), 1000);
}

function printReceipt() {
    const receiptContent = document.getElementById('receiptContent');
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Receipt</title>');
    printWindow.document.write('<link rel="stylesheet" href="' + document.querySelector('link[rel="stylesheet"]').href + '">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(receiptContent.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
    setTimeout(() => closeReceipt(), 500);
}

// ==================== PRODUCT VIEW MODAL ==================== 
let currentViewProductImages = [];
let currentViewImageIndex = 0;

function showViewModal(productId) {
    currentProduct = products.find(p => p.id === productId);
    if (!currentProduct) return;

    const modal = document.getElementById('viewModal');
    document.getElementById('view-name').textContent = currentProduct.name;
    document.getElementById('view-description').textContent = currentProduct.description || 'No description';
    document.getElementById('view-price').textContent = parseFloat(currentProduct.price).toFixed(2);
    document.getElementById('view-stock').textContent = currentProduct.stock;

    // Age restriction
    const ageContainer = document.getElementById('view-age-restriction-container');
    if (currentProduct.age_restriction) {
        ageContainer.style.display = 'block';
        document.getElementById('view-age-restriction').textContent = currentProduct.age_restriction;
    } else {
        ageContainer.style.display = 'none';
    }

    // Expiry date
    const expiryContainer = document.getElementById('view-expiry-date-container');
    if (currentProduct.expiry_date) {
        expiryContainer.style.display = 'block';
        document.getElementById('view-expiry-date').textContent = currentProduct.expiry_date;
    } else {
        expiryContainer.style.display = 'none';
    }

    // Image
    let imageUrl = '/foto/goodb.jpg';
    if (currentProduct.image && currentProduct.image.trim() !== '') {
        imageUrl = currentProduct.image.startsWith('http') || currentProduct.image.startsWith('/')
            ? currentProduct.image
            : '/storage/' + currentProduct.image;
    }
    document.getElementById('view-image').src = imageUrl;
    document.getElementById('view-quantity').value = 1;

    const modal_obj = new bootstrap.Modal(modal);
    modal_obj.show();
}

function increaseQuantity() {
    const input = document.getElementById('view-quantity');
    const max = currentProduct.stock;
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('view-quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function addToCartFromView() {
    const quantity = parseInt(document.getElementById('view-quantity').value) || 1;
    const product = currentProduct;

    if (!product.age_restriction) {
        addToCart(product.id, quantity);
        bootstrap.Modal.getInstance(document.getElementById('viewModal')).hide();
    } else {
        // Show age verification
        showAgeModal(product);
    }
}

// ==================== UTILITIES ==================== 
function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #16a34a;
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);
