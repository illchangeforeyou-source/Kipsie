// Store order data for receipt display
let historyOrderData = null;

async function showOrderReceipt(orderId, customerName, total, orderDate) {
    try {
        // Fetch order details from server
        const response = await fetch(`/api/order/${orderId}`);
        if (!response.ok) throw new Error('Failed to fetch order');

        const order = await response.json();
        historyOrderData = order;

        // Populate receipt modal
        document.getElementById('history-receipt-customer-name').textContent = customerName || 'Customer';
        document.getElementById('history-receipt-date').textContent = orderDate;
        document.getElementById('history-receipt-total').textContent = '$' + parseFloat(total).toFixed(2);

        // Set status badge with color
        const statusBadge = document.getElementById('history-receipt-status-badge');
        const status = (order.status || 'pending').toLowerCase();
        const statusText = status.charAt(0).toUpperCase() + status.slice(1);
        
        statusBadge.textContent = `[ ${statusText} ]`;
        
        // Color code the status
        switch(status) {
            case 'delivered':
                statusBadge.style.backgroundColor = '#28a745';
                break;
            case 'cancelled':
                statusBadge.style.backgroundColor = '#dc3545';
                break;
            case 'pending':
                statusBadge.style.backgroundColor = '#ffc107';
                statusBadge.style.color = '#333';
                break;
            case 'in_process':
                statusBadge.style.backgroundColor = '#17a2b8';
                break;
            default:
                statusBadge.style.backgroundColor = '#6c757d';
        }

        // Populate items table
        const itemsTable = document.getElementById('history-receipt-items-table');
        itemsTable.innerHTML = '';

        const items = order.items ? (typeof order.items === 'string' ? JSON.parse(order.items) : order.items) : [];
        
        if (Array.isArray(items)) {
            items.forEach(item => {
                const row = document.createElement('tr');
                row.style.borderBottom = '1px solid var(--table-border)';
                row.innerHTML = `
                    <td style="padding: 10px; border: 1px solid var(--table-border);">${item.name || 'Unknown'}</td>
                    <td style="padding: 10px; text-align: center; border: 1px solid var(--table-border);">${item.quantity || 1}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid var(--table-border);">$${parseFloat(item.price || 0).toFixed(2)}</td>
                `;
                itemsTable.appendChild(row);
            });
        }

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('history-receipt-modal'));
        modal.show();
    } catch (error) {
        console.error('Error fetching order:', error);
        alert('Failed to load receipt. Please try again.');
    }
}

function printHistoryReceipt() {
    if (!historyOrderData) {
        alert('No order data available');
        return;
    }

    const printWindow = window.open('', '_blank');
    const customerName = document.getElementById('history-receipt-customer-name').textContent;
    const orderDate = document.getElementById('history-receipt-date').textContent;
    const total = document.getElementById('history-receipt-total').textContent;
    const itemsHTML = document.getElementById('history-receipt-items-table').innerHTML;

    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order Receipt</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h2 { text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                th { background-color: #f2f2f2; }
                .summary { text-align: right; margin-top: 20px; font-weight: bold; }
                .info { margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <h2>Order Receipt</h2>
            <div class="info">
                <p><strong>Customer Name:</strong> ${customerName}</p>
                <p><strong>Order Date:</strong> ${orderDate}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHTML}
                </tbody>
            </table>
            <div class="summary">
                Total: ${total}
            </div>
        </body>
        </html>
    `;

    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

async function sendReceiptEmailHistory() {
    if (!historyOrderData) {
        alert('No order data available');
        return;
    }

    try {
        const customerName = document.getElementById('history-receipt-customer-name').textContent;
        const orderDate = document.getElementById('history-receipt-date').textContent;
        const total = document.getElementById('history-receipt-total').textContent;
        const statusBadge = document.getElementById('history-receipt-status-badge').textContent;
        
        // Convert items to JSON string if it's an object
        let itemsStr = historyOrderData.items;
        if (typeof itemsStr === 'object') {
            itemsStr = JSON.stringify(itemsStr);
        }

        const response = await fetch('/send-receipt-email-history', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                orderId: historyOrderData.id,
                customerName: customerName,
                orderDate: orderDate,
                total: total,
                items: itemsStr,
                status: statusBadge
            })
        });

        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.error || 'Failed to send receipt');
        }

        alert('Receipt sent successfully to email!');
    } catch (error) {
        console.error('Error sending receipt:', error);
        alert('Failed to send receipt: ' + error.message);
    }
}

async function sendReceiptWhatsAppHistory() {
    if (!historyOrderData) {
        alert('No order data available');
        return;
    }

    try {
        const customerName = document.getElementById('history-receipt-customer-name').textContent;
        const orderDate = document.getElementById('history-receipt-date').textContent;
        const total = document.getElementById('history-receipt-total').textContent;
        const items = historyOrderData.items ? (typeof historyOrderData.items === 'string' ? JSON.parse(historyOrderData.items) : historyOrderData.items) : [];

        // Format items list
        let itemsText = '';
        items.forEach(item => {
            itemsText += `\n- ${item.name} x${item.quantity} ($${parseFloat(item.price || 0).toFixed(2)})`;
        });

        const message = `*Order Receipt*\n\nCustomer: ${customerName}\nDate: ${orderDate}\n\n*Items:*${itemsText}\n\n*Total: ${total}*\n\nThank you for your order!`;

        // Show WhatsApp preview
        const preview = `WhatsApp Message Preview:\n\n${message}`;
        alert(preview + '\n\nOpening WhatsApp...');

        // Open WhatsApp (requires user's phone number)
        const whatsappUrl = `https://web.whatsapp.com/send?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    } catch (error) {
        console.error('Error preparing WhatsApp message:', error);
        alert('Failed to prepare WhatsApp message. Please try again.');
    }
}
