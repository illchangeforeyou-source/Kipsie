@extends('layouts.app')

@section('title', 'LODIT - Point of Sale')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">
    
    <style>
        /* POS specific dark theme */
        html.dark-mode .card {
            background-color: #2a2a2a;
            border-color: #333;
            color: #f5f5f5;
        }

        html.dark-mode .product-card {
            background-color: #252525;
            border-color: #333;
        }

        html.light-mode .card {
            background-color: #ffffff;
            border-color: #e0e0e0;
            color: #1a1a1a;
        }

        html.light-mode .product-card {
            background-color: #ffffff;
            border-color: #e0e0e0;
        }

        /* Dark mode for modals */
        html.dark-mode .modal-content {
            background-color: #2a2a2a;
            border-color: #333;
            color: #f5f5f5;
        }

        html.dark-mode .modal-header {
            background-color: #1f1f1f !important;
            border-color: #333;
            color: #f5f5f5;
        }

        html.dark-mode .modal-body {
            color: #f5f5f5;
        }

        html.dark-mode .modal-footer {
            background-color: #1f1f1f;
            border-color: #333;
        }

        html.dark-mode .form-control,
        html.dark-mode .form-select,
        html.dark-mode textarea {
            background-color: #1f1f1f;
            border-color: #333;
            color: #f5f5f5;
        }

        html.dark-mode .form-control:focus,
        html.dark-mode .form-select:focus,
        html.dark-mode textarea:focus {
            background-color: #252525;
            border-color: #555;
            color: #f5f5f5;
        }

        html.dark-mode .list-group-item {
            background-color: #1f1f1f;
            border-color: #333;
            color: #f5f5f5;
        }

        html.dark-mode .text-muted {
            color: #aaa !important;
        }

        html.dark-mode .form-label {
            color: #f5f5f5;
        }

        html.dark-mode .btn-outline-secondary {
            color: #f5f5f5;
            border-color: #555;
        }

        html.dark-mode .btn-outline-secondary:hover {
            background-color: #333;
            border-color: #555;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h2 class="mb-0">Medicine Catalog</h2>
        @if(session('level') == 3 || session('level') == 4)
        <button id="addMedicineBtn"
                class="btn btn-success"
                onclick="showAddModal()">
            <i class="bi bi-plus-lg"></i> Add Medicine
        </button>
        @endif

        <div class="d-flex gap-2 ms-auto">
            <input 
                type="text" 
                id="searchInput" 
                class="form-control"
                placeholder="Search medicine..."
                style="width: 220px;"
            >

            <select id="sortSelect" class="form-select" style="width: 180px;">
                <option value="">Sort by</option>
                <option value="name-asc">Name A–Z</option>
                <option value="name-desc">Name Z–A</option>
                <option value="price-asc">Price Ascending</option>
                <option value="price-desc">Price Descending</option>
            </select>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cartModal">
                <i class="bi bi-cart3"></i> Cart (<span id="cart-count">0</span>)
            </button>
        </div>
    </div>

    <!-- Category Filter Dropdown -->
    <div class="mb-4">
        <label for="category-filter" class="form-label"><strong>Filter by Category:</strong></label>
        <select id="category-filter" class="form-select">
            <option value="">All Medicine</option>
        </select>
    </div>

    <div class="row" id="product-list"></div>
    <div class="d-flex justify-content-center mt-3">
      <nav aria-label="Product pagination">
        <ul class="pagination" id="product-pagination"></ul>
      </nav>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Your Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <ul class="list-group mb-3" id="cart-items"></ul>
                    <h5>Total: $ <span id="cart-total">0</span></h5>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" onclick="saveOrder()">Pay Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">💳 Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <h6 class="mb-4">Select Payment Method</h6>
                    
                    <div class="mb-4">
                        <label class="d-flex align-items-center justify-content-center p-3 border rounded mb-3" style="cursor: pointer;">
                            <input type="radio" name="payment-method" value="credit_card" checked>
                            <span class="ms-2">💳 Credit Card</span>
                        </label>
                        
                        <label class="d-flex align-items-center justify-content-center p-3 border rounded mb-3" style="cursor: pointer;">
                            <input type="radio" name="payment-method" value="debit_card">
                            <span class="ms-2">🏦 Debit Card</span>
                        </label>
                        
                        <label class="d-flex align-items-center justify-content-center p-3 border rounded mb-3" style="cursor: pointer;">
                            <input type="radio" name="payment-method" value="digital_wallet">
                            <span class="ms-2">📱 Digital Wallet</span>
                        </label>
                        
                        <label class="d-flex align-items-center justify-content-center p-3 border rounded" style="cursor: pointer;">
                            <input type="radio" name="payment-method" value="cash">
                            <span class="ms-2">💰 Cash</span>
                        </label>
                    </div>

                    <div id="payment-processing" style="display: none;">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                        <p>Processing your payment...</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="process-payment-btn" class="btn btn-primary" onclick="processPayment()">Complete Payment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- RECEIPT MODAL -->
    <div class="modal fade" id="receiptModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">📄 Order Receipt</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="receipt-container" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                        <div class="text-center mb-4">
                            <h4>LODIT</h4>
                            <p class="text-muted">Order Receipt</p>
                        </div>

                        <div class="mb-3">
                            <p><strong>Customer:</strong> <span id="receipt-customer-name"></span></p>
                            <p><strong>Date:</strong> <span id="receipt-date"></span></p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6>Order Details:</h6>
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="receipt-items-table"></tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="text-end">
                            <h5>Total: <strong style="color: #28a745;">$<span id="receipt-total">0.00</span></strong></h5>
                        </div>

                        <div class="text-center text-muted mt-4">
                            <small>Thank you for your purchase!</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex gap-2">
                    <button type="button" class="btn btn-info" onclick="sendReceiptViaEmail(this)">📧 Send via Email</button>
                    <button type="button" class="btn btn-success" onclick="sendReceiptViaWhatsApp(this)">💬 Send via WhatsApp</button>
                    <button type="button" class="btn btn-primary" onclick="saveReceiptAndFinalize()">✓ Save & Complete</button>
                </div>
            </div>
        </div>
    </div>

     <!-- ADD MODAL -->
    <div class="modal fade" id="addModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Add New Medicine</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="add-name" class="form-label">Medicine Name</label>
              <input type="text" id="add-name" class="form-control">
            </div>
            <div class="mb-3">
              <label for="add-description" class="form-label">Description</label>
              <textarea id="add-description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
              <label for="add-price" class="form-label">Price ($)</label>
              <input type="number" id="add-price" class="form-control" min="0">
            </div>
            <div class="mb-3">
              <label for="add-stock" class="form-label">Stock</label>
              <input type="number" id="add-stock" class="form-control" min="0">
            </div>
            <div class="mb-3">
              <label for="add-category" class="form-label">Category</label>
              <select id="add-category" class="form-select">
                <option value="">Select a category</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="add-age-restriction" class="form-label">Age Restriction</label>
              <input type="text" id="add-age-restriction" class="form-control" placeholder="e.g., 18+, 21+">
            </div>
            <div class="mb-3">
              <label for="add-expiry-date" class="form-label">Expiry Date</label>
              <input type="date" id="add-expiry-date" class="form-control">
            </div>
            <div class="mb-3">
              <label for="add-image" class="form-label">Images (upload multiple)</label>
              <input type="file" id="add-image" class="form-control" accept="image/*" multiple>
              <div id="add-image-preview" class="mt-2 d-flex gap-2 flex-wrap"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-success" onclick="saveAdd()">Add Medicine</button>
          </div>
        </div>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title">Edit Medicine</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="edit-id">
            <div class="mb-3">
              <label for="edit-name" class="form-label">Medicine Name</label>
              <input type="text" id="edit-name" class="form-control">
            </div>
            <div class="mb-3">
              <label for="edit-description" class="form-label">Description</label>
              <textarea id="edit-description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
              <label for="edit-price" class="form-label">Price ($)</label>
              <input type="number" id="edit-price" class="form-control" min="0">
            </div>
            <div class="mb-3">
              <label for="edit-stock" class="form-label">Stock</label>
              <input type="number" id="edit-stock" class="form-control" min="0">
            </div>
            <div class="mb-3">
              <label for="edit-category" class="form-label">Category</label>
              <select id="edit-category" class="form-select">
                <option value="">Select a category</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="edit-age-restriction" class="form-label">Age Restriction</label>
              <input type="text" id="edit-age-restriction" class="form-control" placeholder="e.g., 18+, 21+">
            </div>
            <div class="mb-3">
              <label for="edit-expiry-date" class="form-label">Expiry Date</label>
              <input type="date" id="edit-expiry-date" class="form-control">
            </div>
            <div class="mb-3">
              <label for="edit-image" class="form-label">Images (upload multiple to replace)</label>
              <input type="file" id="edit-image" class="form-control" accept="image/*" multiple>
              <div id="edit-image-preview" class="mt-2 d-flex gap-2 flex-wrap"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
            <button id="deleteBtn" class="btn btn-danger" onclick="deleteProduct()">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- VIEW MODAL -->
    <div class="modal fade" id="viewModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="view-name">Medicine Name</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <!-- Main Image -->
              <div class="col-md-8">
                <div class="position-relative d-inline-block w-100">
                  <img id="view-image" class="img-fluid mx-auto d-block mb-3" style="max-height:400px; width:100%; object-fit:contain;" alt="Medicine Image">
                  <button class="btn btn-sm btn-light position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);" id="prevImageBtn" onclick="prevImage()" title="Previous Image">
                    <i class="bi bi-chevron-left"></i>
                  </button>
                  <button class="btn btn-sm btn-light position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%);" id="nextImageBtn" onclick="nextImage()" title="Next Image">
                    <i class="bi bi-chevron-right"></i>
                  </button>
                  <div class="text-center mt-2">
                    <small id="image-counter" class="text-muted">Image 1 of 1</small>
                  </div>
                </div>
              </div>

              <!-- Details -->
              <div class="col-md-4">
                <p id="view-description" class="text-muted mb-3"></p>
                <p><strong>Price:</strong> $<span id="view-price"></span></p>
                <p><strong>Stock:</strong> <span id="view-stock"></span></p>
                <p id="view-age-restriction-container" style="display: none;"><strong>Age Restriction:</strong> <span id="view-age-restriction"></span></p>
                <p id="view-expiry-date-container" style="display: none;"><strong>Expiry Date:</strong> <span id="view-expiry-date"></span></p>
                
                <!-- Quantity Selector -->
                <div class="mt-4">
                  <label class="form-label"><strong>Quantity:</strong></label>
                  <div class="input-group">
                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">−</button>
                    <input type="number" id="view-quantity" class="form-control text-center" value="1" min="1" style="max-width: 80px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">+</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Image Thumbnails -->
            <div class="mt-4">
              <label class="form-label"><strong>Image Gallery</strong></label>
              <div id="image-gallery" class="d-flex gap-2 flex-wrap" style="max-height: 120px; overflow-y: auto; padding: 10px; background: #f8f9fa; border-radius: 5px;">
              </div>
            </div>
          </div>
          <div class="modal-footer d-flex justify-content-between">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button class="btn btn-primary" onclick="addToCartFromView()" style="min-width: 150px;">Add to Cart</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.userLevel = {{ session('level') ?? 0 }};
        window.catalogUsername = "{{ $username ?? 'Guest' }}";
        window.catalogImages = {
            goodb: "{{ asset('foto/goodb.jpg') }}",
            wkai: "{{ asset('foto/wkai.jpg') }}",
            huh: "{{ asset('foto/huh.jpg') }}",
            kaister: "{{ asset('foto/kaister.jpg') }}",
            chipster: "{{ asset('foto/chipster.jpg') }}",
            dont: "{{ asset('foto/dont.jpg') }}"
        };
    </script>
    <script src="{{ asset('js/yao.js') }}"></script>
@endsection

