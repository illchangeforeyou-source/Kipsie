console.log('YAO JS loaded');

let products = [];
let cart = [];
let currentEditId = null;

const currentUser = window.catalogUsername ?? 'Guest';

/* --------------------
   LOAD MEDICINES
-------------------- */
function loadMedicines() {
    fetch('/medicines')
        .then(res => res.json())
        .then(result => {
            if (!result.success || !Array.isArray(result.data)) {
                console.error('Invalid response:', result);
                alert('Failed to load medicines');
                return;
            }

            products = result.data.map(m => ({
                id: m.id,
                name: m.name,
                price: Number(m.price),
                stock: Number(m.stock),
                image: window.catalogImages.goodb
            }));

            renderProducts();
        })
        .catch(err => {
            console.error(err);
            alert('Server error while loading medicines');
        });
}

/* --------------------
   RENDER PRODUCTS
-------------------- */
function renderProducts() {
    const list = document.getElementById('product-list');
    if (!list) return;

    list.innerHTML = '';

    products.forEach(p => {
        list.innerHTML += `
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="${p.image}" class="card-img-top">
                <div class="card-body text-center">
                    <h5>${p.name}</h5>
                    <p>$${p.price}</p>
                    <p>Stock: ${p.stock}</p>
                    <button class="btn btn-success" onclick="addToCart(${p.id})">Add to Cart</button>
                    ${window.userLevel === 3 ? `
                        <button class="btn btn-warning mt-2" onclick="openEditModal(${p.id})">Edit</button>
                    ` : ''}
                </div>
            </div>
        </div>`;
    });
}

/* --------------------
   CART
-------------------- */
function addToCart(id) {
    const p = products.find(x => x.id === id);
    if (!p) return;

    const existing = cart.find(i => i.id === id);
    if (existing) existing.qty++;
    else cart.push({ ...p, qty: 1 });

    updateCartUI();
}

function updateCartUI() {
    const list = document.getElementById('cart-items');
    const count = document.getElementById('cart-count');
    const total = document.getElementById('cart-total');

    if (!list || !count || !total) return;

    list.innerHTML = '';
    let sum = 0;
    let qty = 0;

    cart.forEach(i => {
        sum += i.price * i.qty;
        qty += i.qty;

        list.innerHTML += `
        <li class="list-group-item d-flex justify-content-between">
            ${i.name} x${i.qty}
            <span>$${i.price * i.qty}</span>
        </li>`;
    });

    count.textContent = qty;
    total.textContent = sum;
}

/* --------------------
   DOM READY
-------------------- */
document.addEventListener('DOMContentLoaded', () => {
    loadMedicines();

    const search = document.getElementById('searchInput');
    if (search) search.addEventListener('input', renderProducts);

    const sort = document.getElementById('sortSelect');
    if (sort) sort.addEventListener('change', renderProducts);
});

/* --------------------
   GLOBAL EXPORTS
-------------------- */
window.addToCart = addToCart;
window.openEditModal = openEditModal;
window.loadMedicines = loadMedicines;
