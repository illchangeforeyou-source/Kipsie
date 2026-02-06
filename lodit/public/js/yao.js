let products = [];
let cart = [];
let allCategories = [];
let selectedCategoryId = null;
let currentPage = 1;
const pageSize = 20; // products per page
let imgObserver = null;

// Simple debounce to reduce frequent re-renders (search input)
function debounce(fn, wait) {
    let t;
    return function(...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), wait);
    };
}

// ================== FETCH & RENDER MEDICINES ==================
async function fetchProducts() {
    try {
        const response = await fetch('/medicines', { credentials: "same-origin" });
        if (!response.ok) throw new Error("Failed to fetch medicines");
        const data = await response.json();
        products = data.data || [];
        renderProducts();
    } catch (error) {
        console.error(error);
        alert("Error loading medicines.");
    }
}

async function fetchCategories() {
    try {
        const response = await fetch('/categories', { credentials: "same-origin" });
        if (!response.ok) throw new Error("Failed to fetch categories");
        const data = await response.json();
        allCategories = data.data || [];
        console.log("Categories loaded:", allCategories);
        renderCategoryFilters();
        populateCategorySelects();
    } catch (error) {
        console.error("Error fetching categories:", error);
        alert("Error loading categories.");
    }
}

function populateCategorySelects() {
    const addSelect = document.getElementById("add-category");
    const editSelect = document.getElementById("edit-category");
    
    console.log("Populating selects - addSelect:", !!addSelect, "editSelect:", !!editSelect, "categories:", allCategories.length);
    
    if (!addSelect && !editSelect) {
        console.warn("Category select elements not found");
        return;
    }
    
    // Helper to build options
    const buildOptions = () => {
        let html = '<option value="">Select a category...</option>';
        
        if (!allCategories || allCategories.length === 0) {
            console.warn("No categories available");
            return html;
        }
        
        allCategories.forEach(category => {
            // Add parent category
            if (!category.parent_id) {
                html += `<option value="${category.id}">${category.name}</option>`;
                
                // Add subcategories indented
                if (category.subcategories && category.subcategories.length > 0) {
                    category.subcategories.forEach(sub => {
                        html += `<option value="${sub.id}">  → ${sub.name}</option>`;
                    });
                }
            }
        });
        return html;
    };
    
    const optionsHtml = buildOptions();
    console.log("Built options HTML:", optionsHtml);
    
    if (addSelect) addSelect.innerHTML = optionsHtml;
    if (editSelect) editSelect.innerHTML = optionsHtml;
}

function renderCategoryFilters() {
    const filterSelect = document.getElementById("category-filter");
    if (!filterSelect) return;
    
    // Clear and rebuild options
    filterSelect.innerHTML = '<option value="">All Medicine</option>';
    
    // Add parent categories and their subcategories
    const parentCategories = allCategories.filter(cat => !cat.parent_id);
    parentCategories.forEach(category => {
        const optgroup = document.createElement('optgroup');
        optgroup.label = category.name;
        
        // Add parent as first option in group
        const parentOption = document.createElement('option');
        parentOption.value = category.id;
        parentOption.textContent = category.name;
        optgroup.appendChild(parentOption);
        
        // Add subcategories
        if (category.subcategories && category.subcategories.length > 0) {
            category.subcategories.forEach(sub => {
                const subOption = document.createElement('option');
                subOption.value = sub.id;
                subOption.textContent = `  → ${sub.name}`;
                optgroup.appendChild(subOption);
            });
        }
        
        filterSelect.appendChild(optgroup);
    });
    
    // Add change event listener
    filterSelect.addEventListener('change', (e) => {
        filterByCategory(e.target.value ? parseInt(e.target.value) : null);
    });
}

function renderProducts() {
    const list = document.getElementById("product-list");
    if (!list) return;
    list.innerHTML = "";

    const searchTerm = document.getElementById("searchInput")?.value.toLowerCase() || "";
    const sortValue = document.getElementById("sortSelect")?.value || "";

    let filtered = products.filter(p => p.name.toLowerCase().includes(searchTerm));
    
    // Filter by category if selected
    if (selectedCategoryId) {
        filtered = filtered.filter(p => p.category_id === selectedCategoryId);
    }

    if (sortValue) {
        const [key, order] = sortValue.split("-");
        filtered.sort((a, b) => (a[key] < b[key] ? (order === "asc" ? -1 : 1) : a[key] > b[key] ? (order === "asc" ? 1 : -1) : 0));
    }

    const total = filtered.length;
    const start = (currentPage - 1) * pageSize;
    const end = start + pageSize;
    const pageItems = filtered.slice(start, end);

    // render page items only
    pageItems.forEach(product => {
        const col = document.createElement("div");
        col.className = "col-md-4 mb-3";
        
        // Determine image to display - check if it starts with http or /, otherwise prepend /storage/
        let imageUrl = '/foto/goodb.jpg'; // default fallback
        if (product.image && product.image.trim() !== '') {
            if (product.image.startsWith('http') || product.image.startsWith('/')) {
                imageUrl = product.image;
            } else {
                // Relative path from storage, add /storage prefix
                imageUrl = '/storage/' + product.image;
            }
        }
        
        // use data-src for IntersectionObserver lazy load
        col.innerHTML = `
    <div class="card p-3 h-100" style="display: flex; flex-direction: column;">
        <img data-src="${imageUrl}" src="/foto/goodb.jpg" alt="${product.name}" class="card-img-top mb-2 lazy-product-image" loading="lazy" decoding="async" style="height: 200px; object-fit: cover; border-radius: 5px;" onerror="this.src='/foto/goodb.jpg';">
        <h5 class="card-title" style="min-height: 50px;">${product.name}</h5>
        <p class="card-text" style="flex-grow: 1; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
            ${product.description || 'No description'}<br/>
            ${product.age_restriction ? `<small><strong>Age:</strong> ${product.age_restriction}</small><br/>` : ''}
            ${product.expiry_date ? `<small><strong>Expires:</strong> ${product.expiry_date}</small>` : ''}
        </p>
        <p class="card-text"><strong>Price:</strong> $${parseFloat(product.price).toFixed(2)}</p>
        <p class="card-text"><strong>Stock:</strong> ${product.stock}</p>
        <div class="mt-auto">
            <button class="btn btn-info w-100" onclick="showViewModal(${product.id})">View</button>
            ${(window.userLevel == 4 || window.userLevel == 3) ? `<button class="btn btn-warning w-100 mt-2" onclick="showEditModal(${product.id})">Edit</button>` : ''}
        </div>
    </div>
`;

        list.appendChild(col);
        // observe image for lazy load
        try {
            const img = col.querySelector('img.lazy-product-image');
            if (img && imgObserver) imgObserver.observe(img);
        } catch(e){}
    });

    renderPagination(total);
}

function renderPagination(totalItems) {
    const pagination = document.getElementById('product-pagination');
    if (!pagination) return;
    pagination.innerHTML = '';
    const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));

    const makePageItem = (p, label = null, active = false) => {
        const li = document.createElement('li');
        li.className = 'page-item' + (active ? ' active' : '');
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = label || p;
        a.addEventListener('click', (e) => { e.preventDefault(); setPage(p); });
        li.appendChild(a);
        return li;
    };

    // prev
    pagination.appendChild(makePageItem(Math.max(1, currentPage - 1), '«'));
    // pages (limit displayed)
    const maxDisplay = 5;
    let start = Math.max(1, currentPage - Math.floor(maxDisplay/2));
    let end = Math.min(totalPages, start + maxDisplay - 1);
    if (end - start < maxDisplay - 1) start = Math.max(1, end - maxDisplay + 1);
    for (let p = start; p <= end; p++) {
        pagination.appendChild(makePageItem(p, null, p === currentPage));
    }
    // next
    pagination.appendChild(makePageItem(Math.min(totalPages, currentPage + 1), '»'));
}

function setPage(p) {
    currentPage = p;
    renderProducts();
}

function filterByCategory(categoryId) {
    selectedCategoryId = categoryId;
    renderProducts();
}

// ================== CART FUNCTIONS ==================
function addToCart(id) {
    const product = products.find(p => p.id === id);
    if (!product || product.stock <= 0) return alert("Out of stock!");
    // Prevent extremely large carts — helps memory-constrained devices
    if (cart.reduce((s, i) => s + i.quantity, 0) >= 200) return alert('Cart limit reached. Please checkout first.');

    const cartItem = cart.find(item => item.id === id);
    if (cartItem) {
        if (cartItem.quantity < product.stock) cartItem.quantity++;
        else return alert("No more stock available");
    } else {
        cart.push({ id: product.id, name: product.name, price: product.price, quantity: 1 });
    }

    updateCart();
}

function addToCartFromView() {
    if (currentViewingProductId) {
        const quantityInput = document.getElementById("view-quantity");
        const quantity = parseInt(quantityInput.value) || 1;
        const product = products.find(p => p.id === currentViewingProductId);
        
        if (!product || product.stock <= 0) {
            return alert("Out of stock!");
        }
        
        if (quantity > product.stock) {
            return alert(`Only ${product.stock} items available in stock!`);
        }

        // Add to cart multiple times based on quantity
        const cartItem = cart.find(item => item.id === currentViewingProductId);
        if (cartItem) {
            if (cartItem.quantity + quantity <= product.stock) {
                cartItem.quantity += quantity;
            } else {
                return alert(`Cannot add ${quantity} items. Only ${product.stock - cartItem.quantity} remaining.`);
            }
        } else {
            cart.push({ 
                id: product.id, 
                name: product.name, 
                price: product.price, 
                quantity: quantity 
            });
        }

        updateCart();
        alert(`Added ${quantity} item(s) to cart!`);
    }
}

function increaseQuantity() {
    const quantityInput = document.getElementById("view-quantity");
    const maxStock = parseInt(quantityInput.max);
    const currentQty = parseInt(quantityInput.value);
    if (currentQty < maxStock) {
        quantityInput.value = currentQty + 1;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById("view-quantity");
    const currentQty = parseInt(quantityInput.value);
    if (currentQty > 1) {
        quantityInput.value = currentQty - 1;
    }
}

function updateCart() {
    const cartItems = document.getElementById("cart-items");
    const cartCount = document.getElementById("cart-count");
    const cartTotal = document.getElementById("cart-total");
    if (!cartItems || !cartCount || !cartTotal) return;

    cartItems.innerHTML = "";
    let total = 0;
    cart.forEach(item => {
        total += item.price * item.quantity;
        cartItems.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center">${item.name} x ${item.quantity} - $${(item.price * item.quantity).toFixed(2)}</li>`;
    });

    cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartTotal.textContent = total.toFixed(2);
}

// ================== SAVE ORDER ==================
async function saveOrder() {
    if (!cart.length) return alert("Cart is empty!");
    const customerName = window.catalogUsername || "Guest";
    const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    
    // Store order data globally for receipt modal
    window.currentOrder = {
        customer_name: customerName,
        items: [...cart],
        total: total,
        date: new Date().toLocaleString()
    };
    
    // Show payment modal
    showPaymentModal();
}

// ================== PAYMENT MODAL ==================
function showPaymentModal() {
    // Close cart modal if open to avoid heavy stacked modals
    try {
        const cartEl = document.getElementById('cartModal');
        const cartInstance = bootstrap.Modal.getInstance(cartEl);
        if (cartInstance) cartInstance.hide();
    } catch (e) {}

    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    paymentModal.show();
}

async function processPayment() {
    const paymentMethod = document.querySelector('input[name="payment-method"]:checked')?.value;
    if (!paymentMethod) return alert('Please select a payment method');
    
    const btn = document.getElementById('process-payment-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    // Simulate payment processing
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Hide payment modal
    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
    
    // Show receipt modal
    showReceiptModal();
    
    btn.disabled = false;
    btn.innerHTML = 'Complete Payment';
}

function showReceiptModal() {
    if (!window.currentOrder) return alert('Order data missing');

    const order = window.currentOrder;
    const tbody = document.getElementById('receipt-items-table');
    if (!tbody) return;

    // Use DocumentFragment to avoid large HTML string allocations
    tbody.innerHTML = '';
    const frag = document.createDocumentFragment();
    for (const item of order.items) {
        const tr = document.createElement('tr');
        const tdName = document.createElement('td'); tdName.textContent = item.name; tr.appendChild(tdName);
        const tdQty = document.createElement('td'); tdQty.textContent = item.quantity; tr.appendChild(tdQty);
        const tdPrice = document.createElement('td'); tdPrice.textContent = '$' + parseFloat(item.price).toFixed(2); tr.appendChild(tdPrice);
        const tdTotal = document.createElement('td'); tdTotal.textContent = '$' + (item.price * item.quantity).toFixed(2); tr.appendChild(tdTotal);
        frag.appendChild(tr);
    }
    tbody.appendChild(frag);

    document.getElementById('receipt-customer-name').textContent = order.customer_name;
    document.getElementById('receipt-date').textContent = order.date;
    document.getElementById('receipt-total').textContent = order.total.toFixed(2);

    const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
    receiptModal.show();
}

async function saveReceiptAndFinalize() {
    if (!window.currentOrder) return alert('Order data missing');
    
    const order = window.currentOrder;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        const response = await fetch('/save-order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ customer_name: order.customer_name, items: order.items, total: order.total }),
            credentials: "same-origin"
        });

        if (!response.ok) throw new Error("Failed to save order");
        const data = await response.json();

        if (data.success) {
            alert("Order placed successfully! Order ID: " + data.order_id);
            cart = [];
            updateCart();
            bootstrap.Modal.getInstance(document.getElementById('receiptModal')).hide();
            // Clear large objects to free memory
            window.currentOrder = null;
            try { document.getElementById('receipt-items-table').innerHTML = ''; } catch(e){}
            // hint to GC by dropping references
            setTimeout(() => { order.items = null; }, 0);
        } else alert("Failed to place order: " + (data.message || "Unknown error"));
    } catch (error) {
        console.error(error);
        alert("Error saving order: " + error.message);
    }
}

async function sendReceiptViaEmail(btn) {
    if (!window.currentOrder) return alert('Order data missing');
    
    const email = prompt('Enter customer email address:');
    if (!email) return;
    
    if (!email.includes('@')) return alert('Please enter a valid email address');
    
    const button = btn || document.activeElement || {};
    try { if (button.disabled !== undefined) { button.disabled = true; button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...'; } } catch(e){}
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        const response = await fetch('/send-receipt-email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ email, order: window.currentOrder }),
            credentials: "same-origin"
        });

        const data = await response.json();
        if (data.success) {
            alert('Receipt sent to ' + email + '!');
        } else {
            alert('Failed to send email: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Error sending email: ' + error.message);
    } finally {
        try { if (button.disabled !== undefined) { button.disabled = false; button.innerHTML = '📧 Send via Email'; } } catch(e){}
    }
}

async function sendReceiptViaWhatsApp(btn) {
    if (!window.currentOrder) return alert('Order data missing');
    
    const phone = prompt('Enter customer WhatsApp number (with country code, e.g., +1234567890):');
    if (!phone) return;
    
    const button = btn || document.activeElement || {};
    try { if (button.disabled !== undefined) { button.disabled = true; button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...'; } } catch(e){}
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        const response = await fetch('/send-receipt-whatsapp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ phone, order: window.currentOrder }),
            credentials: "same-origin"
        });

        const data = await response.json();
        if (data.success) {
            alert('Receipt sent to ' + phone + ' via WhatsApp!');
        } else {
            alert('Failed to send WhatsApp: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Error sending WhatsApp: ' + error.message);
    } finally {
        try { if (button.disabled !== undefined) { button.disabled = false; button.innerHTML = '💬 Send via WhatsApp'; } } catch(e){}
    }
}

// ================== ADD MEDICINE ==================
function showAddModal() {
    ["add-name","add-description","add-price","add-stock","add-image","add-age-restriction","add-expiry-date"].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            if (el.type === 'file') {
                el.value = '';
            } else {
                el.value = '';
            }
        }
    });
    const preview = document.getElementById("add-image-preview");
    if (preview) preview.innerHTML = '';
    new bootstrap.Modal(document.getElementById('addModal')).show();
}

function setupImagePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;

    input.addEventListener('change', e => {
        preview.innerHTML = ''; // Clear previous previews
        const files = e.target.files;
        
        if (files && files.length > 0) {
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const imgContainer = document.createElement('div');
                    imgContainer.style.position = 'relative';
                    imgContainer.style.display = 'inline-block';
                    
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.maxHeight = '100px';
                    img.style.maxWidth = '100px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '5px';
                    
                    imgContainer.appendChild(img);
                    preview.appendChild(imgContainer);
                };
                reader.readAsDataURL(file);
            });
        }
    });
}

// ================== ADD MEDICINE ==================
async function saveAdd() {
    const name = document.getElementById("add-name").value.trim();
    const description = document.getElementById("add-description").value.trim();
    const price = parseFloat(document.getElementById("add-price").value);
    const stock = parseInt(document.getElementById("add-stock").value);
    const category_id = document.getElementById("add-category").value || null;
    const age_restriction = document.getElementById("add-age-restriction")?.value.trim() || null;
    const expiry_date = document.getElementById("add-expiry-date")?.value || null;
    const imageInput = document.getElementById("add-image");
    const images = imageInput && imageInput.files ? Array.from(imageInput.files) : [];

    // Validate inputs
    if (!name || isNaN(price) || isNaN(stock)) {
        return alert("Please fill all required fields correctly.");
    }

    const formData = new FormData();
    formData.append("name", name);
    formData.append("description", description);
    formData.append("price", price);
    formData.append("stock", stock);
    if (category_id) formData.append("category_id", category_id);
    if (age_restriction) formData.append("age_restriction", age_restriction);
    if (expiry_date) formData.append("expiry_date", expiry_date);
    
    // Append multiple images
    images.forEach((image, index) => {
        formData.append(`images[]`, image);
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch("/medicines/add", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken
            },
            body: formData,
            credentials: "same-origin"
        });

        if (!response.ok) throw new Error("Failed to add medicine");

        const data = await response.json();

        if (data.success) {
            alert("Medicine added successfully!");
            fetchProducts(); // refresh the list
            // hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById("addModal"));
            if (modal) modal.hide();
        } else {
            alert("Failed to add medicine: " + (data.message || "Unknown error"));
        }
    } catch (error) {
        console.error(error);
        alert("Error adding medicine: " + error.message);
    }
}
let currentViewingProductId = null;
let currentImageIndex = 0;
let currentProductImages = [];

function showViewModal(id) {
    const product = products.find(p => p.id === id);
    if (!product) return alert("Product not found");

    currentViewingProductId = id;
    currentImageIndex = 0;

    document.getElementById("view-name").textContent = product.name || '';
    document.getElementById("view-description").textContent = product.description || '';

    document.getElementById("view-price").textContent =
        product.price ? Number(product.price).toFixed(2) : '0.00';

    document.getElementById("view-stock").textContent =
        product.stock ?? 0;

    // Handle age restriction
    const ageRestrictionContainer = document.getElementById("view-age-restriction-container");
    if (product.age_restriction) {
        document.getElementById("view-age-restriction").textContent = product.age_restriction;
        ageRestrictionContainer.style.display = 'block';
    } else {
        ageRestrictionContainer.style.display = 'none';
    }

    // Handle expiry date
    const expiryDateContainer = document.getElementById("view-expiry-date-container");
    if (product.expiry_date) {
        document.getElementById("view-expiry-date").textContent = product.expiry_date;
        expiryDateContainer.style.display = 'block';
    } else {
        expiryDateContainer.style.display = 'none';
    }

    // Reset quantity to 1
    document.getElementById("view-quantity").value = 1;
    document.getElementById("view-quantity").max = product.stock;

    // Get all images (from images array or fallback to single image)
    currentProductImages = [];
    if (product.images && Array.isArray(product.images) && product.images.length > 0) {
        currentProductImages = product.images;
    } else if (product.image && product.image.trim() !== '') {
        currentProductImages = [product.image];
    }

    // Display main image
    displayMainImage();

    // Display thumbnails
    displayThumbnails();

    new bootstrap.Modal(document.getElementById('viewModal')).show();
}

function displayMainImage() {
    const imageEl = document.getElementById("view-image");
    
    let imagePath = '/foto/goodb.jpg'; // default
    if (currentProductImages.length > 0) {
        const image = currentProductImages[currentImageIndex];
        if (image) {
            if (image.startsWith('http') || image.startsWith('/')) {
                imagePath = image;
            } else {
                imagePath = '/storage/' + image;
            }
        }
    }
    
    imageEl.src = imagePath;
    imageEl.onerror = () => {
        imageEl.src = '/foto/goodb.jpg';
    };

    imageEl.style.display = 'block';
    imageEl.classList.add('mx-auto', 'd-block');

    // Update counter and buttons
    updateImageCounter();
}

function displayThumbnails() {
    const gallery = document.getElementById("image-gallery");
    gallery.innerHTML = '';
    
    if (currentProductImages.length === 0) {
        gallery.innerHTML = '<p class="text-muted">No images available</p>';
        return;
    }

    currentProductImages.forEach((image, index) => {
        let imagePath = '/foto/goodb.jpg';
        if (image) {
            if (image.startsWith('http') || image.startsWith('/')) {
                imagePath = image;
            } else {
                imagePath = '/storage/' + image;
            }
        }

        const thumb = document.createElement('img');
        thumb.src = imagePath;
        thumb.alt = `Image ${index + 1}`;
        thumb.style.cursor = 'pointer';
        thumb.style.height = '80px';
        thumb.style.width = '80px';
        thumb.style.objectFit = 'cover';
        thumb.style.borderRadius = '5px';
        thumb.style.border = currentImageIndex === index ? '3px solid #007bff' : '2px solid #ddd';
        thumb.onclick = () => {
            currentImageIndex = index;
            displayMainImage();
            displayThumbnails();
        };
        
        gallery.appendChild(thumb);
    });
}

function updateImageCounter() {
    const total = currentProductImages.length || 1;
    const current = currentImageIndex + 1;
    document.getElementById("image-counter").textContent = `Image ${current} of ${total}`;
    
    // Show/hide navigation buttons
    if (currentProductImages.length > 1) {
        document.getElementById("prevImageBtn").style.display = 'block';
        document.getElementById("nextImageBtn").style.display = 'block';
    } else {
        document.getElementById("prevImageBtn").style.display = 'none';
        document.getElementById("nextImageBtn").style.display = 'none';
    }
}

function prevImage() {
    if (currentProductImages.length > 1) {
        currentImageIndex = (currentImageIndex - 1 + currentProductImages.length) % currentProductImages.length;
        displayMainImage();
        displayThumbnails();
    }
}

function nextImage() {
    if (currentProductImages.length > 1) {
        currentImageIndex = (currentImageIndex + 1) % currentProductImages.length;
        displayMainImage();
        displayThumbnails();
    }
}


// ================== EDIT MEDICINE ==================
function showEditModal(id) {
    const product = products.find(p => p.id == id);
    if (!product) return;

    document.getElementById("edit-id").value = id;
    
    ["edit-name","edit-description","edit-price","edit-stock"].forEach(field => {
        const el = document.getElementById(field);
        if (el) {
            const fieldName = field.replace("edit-","");
            el.value = product[fieldName] || '';
        }
    });

    // Set age restriction and expiry date
    const ageRestrictionEl = document.getElementById("edit-age-restriction");
    if (ageRestrictionEl) {
        ageRestrictionEl.value = product.age_restriction || '';
    }

    const expiryDateEl = document.getElementById("edit-expiry-date");
    if (expiryDateEl) {
        expiryDateEl.value = product.expiry_date || '';
    }

    // Set category
    const categorySelect = document.getElementById("edit-category");
    if (categorySelect && product.category_id) {
        categorySelect.value = product.category_id;
    }

    const previewDiv = document.getElementById("edit-image-preview");
    if (previewDiv) {
        previewDiv.innerHTML = '';
        
        // Display existing images
        const images = (product.images && Array.isArray(product.images)) ? product.images : 
                       (product.image ? [product.image] : []);
        
        if (images.length > 0) {
            images.forEach((image, index) => {
                let imagePath = '/foto/goodb.jpg';
                if (image) {
                    if (image.startsWith('http') || image.startsWith('/')) {
                        imagePath = image;
                    } else {
                        imagePath = '/storage/' + image;
                    }
                }
                
                const imgContainer = document.createElement('div');
                imgContainer.style.position = 'relative';
                imgContainer.style.display = 'inline-block';
                
                const img = document.createElement('img');
                img.src = imagePath;
                img.style.maxHeight = '100px';
                img.style.maxWidth = '100px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '5px';
                
                imgContainer.appendChild(img);
                previewDiv.appendChild(imgContainer);
            });
        }
    }

    new bootstrap.Modal(document.getElementById('editModal')).show();
}

async function saveEdit() {
    const id = document.getElementById("edit-id").value;
    const name = document.getElementById("edit-name").value.trim();
    const description = document.getElementById("edit-description").value.trim();
    const price = parseFloat(document.getElementById("edit-price").value);
    const stock = parseInt(document.getElementById("edit-stock").value);
    const category_id = document.getElementById("edit-category").value || null;
    const age_restriction = document.getElementById("edit-age-restriction")?.value.trim() || null;
    const expiry_date = document.getElementById("edit-expiry-date")?.value || null;
    const imageInput = document.getElementById("edit-image");
    const images = imageInput && imageInput.files ? Array.from(imageInput.files) : [];

    // Validate inputs
    if (!name || isNaN(price) || isNaN(stock)) {
        return alert("Please fill all required fields correctly.");
    }

    const formData = new FormData();
    formData.append("name", name);
    formData.append("description", description);
    formData.append("price", price);
    formData.append("stock", stock);
    if (category_id) formData.append("category_id", category_id);
    if (age_restriction) formData.append("age_restriction", age_restriction);
    if (expiry_date) formData.append("expiry_date", expiry_date);
    
    // Append multiple images if provided
    if (images.length > 0) {
        images.forEach((image) => {
            formData.append("images[]", image);
        });
    }
    formData.append("_method", "PUT");

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(`/medicines/${id}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            body: formData,
            credentials: "same-origin"
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || `HTTP ${response.status}: Failed to update medicine`);
        }
        
        const data = await response.json();

        if (data.success) {
            alert("Medicine updated successfully!");
            fetchProducts();
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            if (editModal) editModal.hide();
        } else {
            alert("Failed to update medicine: " + (data.message || "Unknown error"));
        }
    } catch (error) {
        console.error("Edit error:", error);
        alert("Error updating medicine: " + error.message);
    }
}

// ================== DELETE MEDICINE ==================
async function deleteProduct() {
    if (!confirm("Are you sure you want to delete this medicine?")) return;
    const id = document.getElementById("edit-id").value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(`/medicines/${id}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            body: new URLSearchParams({ "_method": "DELETE" }),
            credentials: "same-origin"
        });

        if (!response.ok) throw new Error("Failed to delete medicine");
        const data = await response.json();

        if (data.success) {
            alert("Medicine deleted!");
            fetchProducts();
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
        } else alert("Failed: " + (data.message || "Unknown error"));
    } catch (error) {
        console.error(error);
        alert("Error deleting medicine.");
    }
}

// ================== INIT ==================
document.addEventListener("DOMContentLoaded", () => {
    // Search & sort
    const searchInput = document.getElementById("searchInput");
    if (searchInput) searchInput.addEventListener("input", debounce(() => { currentPage = 1; renderProducts(); }, 250));

    const sortSelect = document.getElementById("sortSelect");
    if (sortSelect) sortSelect.addEventListener("change", () => { currentPage = 1; renderProducts(); });

    // Setup IntersectionObserver for product images (if supported)
    if ('IntersectionObserver' in window) {
        imgObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        }, { rootMargin: '200px' });
    }

    // Image previews
    setupImagePreview('add-image', 'add-image-preview');
    setupImagePreview('edit-image', 'edit-image-preview');

    // Initial fetch
    fetchCategories();
    fetchProducts();
});
