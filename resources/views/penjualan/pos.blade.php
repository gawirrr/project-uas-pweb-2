@extends('layouts.app')
@section('title', 'Transaksi Penjualan (POS)')

@push('styles')
{{-- Menambahkan style khusus untuk halaman POS --}}
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    .product-search-results {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
    }

    .product-search-results .list-group-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .product-search-results .list-group-item:hover {
        background-color: #f8fafc;
    }

    .cart-item-actions .btn {
        padding: 0.2rem 0.5rem;
        font-size: 0.8rem;
    }
    
    #pembayaran-info {
        font-size: 1.25rem;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-cash-register"></i> Point of Sale</h1>
    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-list-ul"></i> Riwayat Penjualan
    </a>
</div>

<div class="pos-container">
    {{-- Kolom Kiri: Pencarian Barang dan Keranjang --}}
    <div class="form-container">
        {{-- Pencarian Barang --}}
        <div class="form-group">
            <label for="product-search">Cari Barang</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                <input type="text" id="product-search" class="form-control" placeholder="Ketik nama barang...">
            </div>
            <div id="product-list" class="product-search-results mt-2"></div>
        </div>

        <hr class="my-4">

        {{-- Keranjang Belanja --}}
        <h3><i class="fa-solid fa-shopping-cart"></i> Keranjang</h3>
        <div class="table-responsive">
            <table class="table" id="cart-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th class="text-center">Jumlah</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Item keranjang akan ditambahkan di sini oleh JavaScript --}}
                    <tr id="cart-empty-row">
                        <td colspan="5" class="text-center text-muted">Keranjang masih kosong.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kolom Kanan: Detail Pembayaran --}}
    <div class="form-container">
        <h3><i class="fa-solid fa-receipt"></i> Detail Pembayaran</h3>
        <div class="form-group">
            <label for="customer-name">Nama Pelanggan (Opsional)</label>
            <input type="text" id="customer-name" class="form-control" placeholder="Umum">
        </div>

        <div id="pembayaran-info" class="p-3 bg-light rounded text-end my-3">
            <div class="text-muted">Grand Total</div>
            <div id="grand-total" class="display-6">Rp 0</div>
        </div>
        
        <button id="process-payment-btn" class="btn btn-primary w-100 btn-lg" disabled>
            <i class="fa-solid fa-check"></i> Proses Pembayaran
        </button>

        {{-- Elemen untuk notifikasi --}}
        <div id="notification" class="alert mt-3" style="display: none;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSearchInput = document.getElementById('product-search');
    const productList = document.getElementById('product-list');
    const cartTableBody = document.querySelector('#cart-table tbody');
    const cartEmptyRow = document.getElementById('cart-empty-row');
    const grandTotalElement = document.getElementById('grand-total');
    const customerNameInput = document.getElementById('customer-name');
    const processPaymentBtn = document.getElementById('process-payment-btn');
    const notificationElement = document.getElementById('notification');
    
    let cart = []; // [ {id, nama_barang, harga, jumlah, stok}, ... ]
    let searchTimeout;

    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type = 'success') {
        notificationElement.textContent = message;
        notificationElement.className = `alert alert-${type}`;
        notificationElement.style.display = 'block';
        setTimeout(() => {
            notificationElement.style.display = 'none';
        }, 5000);
    }

    // Event listener untuk input pencarian
    productSearchInput.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        const query = this.value;

        if (query.length < 2) {
            productList.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('penjualan.search_products') }}?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    productList.innerHTML = ''; // Kosongkan daftar hasil
                    if (data.length > 0) {
                        const ul = document.createElement('ul');
                        ul.className = 'list-group';
                        data.forEach(product => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center';
                            li.textContent = `${product.nama_barang} (Stok: ${product.stok})`;
                            li.dataset.product = JSON.stringify(product);
                            li.addEventListener('click', () => addProductToCart(product));
                            ul.appendChild(li);
                        });
                        productList.appendChild(ul);
                    } else {
                        productList.innerHTML = '<div class="p-3 text-muted">Produk tidak ditemukan.</div>';
                    }
                });
        }, 300); // Debounce untuk mengurangi request
    });

    // Fungsi untuk menambah produk ke keranjang
    function addProductToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);
        
        if (existingItem) {
            if (existingItem.jumlah < product.stok) {
                existingItem.jumlah++;
            } else {
                showNotification(`Stok maksimum untuk ${product.nama_barang} telah tercapai.`, 'warning');
            }
        } else {
            cart.push({ ...product, jumlah: 1 });
        }
        productSearchInput.value = '';
        productList.innerHTML = '';
        renderCart();
    }

    // Fungsi untuk merender tampilan keranjang
    function renderCart() {
        cartTableBody.innerHTML = '';
        if (cart.length === 0) {
            cartTableBody.appendChild(cartEmptyRow);
            processPaymentBtn.disabled = true;
        } else {
            cart.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.nama_barang}</td>
                    <td class="text-center cart-item-actions">
                        <button class="btn btn-sm btn-secondary" onclick="updateCartItem(${index}, -1)">-</button>
                        <span class="mx-2">${item.jumlah}</span>
                        <button class="btn btn-sm btn-secondary" onclick="updateCartItem(${index}, 1)">+</button>
                    </td>
                    <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td>Rp ${(item.harga * item.jumlah).toLocaleString('id-ID')}</td>
                    <td><button class="btn btn-sm btn-danger" onclick="removeCartItem(${index})"><i class="fa-solid fa-trash"></i></button></td>
                `;
                cartTableBody.appendChild(row);
            });
            processPaymentBtn.disabled = false;
        }
        updateGrandTotal();
    }
    
    // Fungsi yang diekspos ke window untuk bisa dipanggil dari HTML
    window.updateCartItem = function(index, change) {
        const item = cart[index];
        if (item) {
            const newJumlah = item.jumlah + change;
            if (newJumlah > 0) {
                if (newJumlah <= item.stok) {
                    item.jumlah = newJumlah;
                } else {
                    showNotification(`Stok ${item.nama_barang} tidak mencukupi.`, 'warning');
                }
            } else {
                // Jika jumlah jadi 0 atau kurang, hapus item
                removeCartItem(index);
            }
        }
        renderCart();
    }

    window.removeCartItem = function(index) {
        cart.splice(index, 1);
        renderCart();
    }

    // Fungsi untuk menghitung dan menampilkan grand total
    function updateGrandTotal() {
        const total = cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        grandTotalElement.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    }

    // Event listener untuk tombol proses pembayaran
    processPaymentBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

        const transactionData = {
            pelanggan: customerNameInput.value || 'Umum',
            cart: cart.map(item => ({ id: item.id, jumlah: item.jumlah })),
        };

        fetch('{{ route('penjualan.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(transactionData)
        })
        .then(response => {
            if (!response.ok) {
                // Jika response error (seperti 422 atau 500)
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            showNotification(data.message, 'success');
            // Reset state setelah berhasil
            cart = [];
            customerNameInput.value = '';
            renderCart();
            // Redirect atau tampilkan detail transaksi
            setTimeout(() => {
                window.location.href = `/penjualan/${data.penjualan_id}`;
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'Terjadi kesalahan.', 'danger');
        })
        .finally(() => {
            // Kembalikan state tombol
            this.disabled = false;
            this.innerHTML = '<i class="fa-solid fa-check"></i> Proses Pembayaran';
        });
    });
});
</script>
@endpush
