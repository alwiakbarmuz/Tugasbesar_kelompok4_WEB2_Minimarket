<x-app-layout title="Transaksi Baru">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Transaksi Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1" id="branch-info">
                    @if(isset($branch) && $branch)
                        {{ $branch->name }} - <span id="real-time-datetime">{{ now()->format('d/m/Y H:i:s') }}</span>
                    @else
                        {{ Auth::user()->name }} - <span id="real-time-datetime">{{ now()->format('d/m/Y H:i:s') }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-1">
                <i class="fas fa-arrow-left text-sm"></i>
                <span>Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Products Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="p-4 border-b">
                            <div class="relative">
                                <i class="fas fa-barcode absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="barcodeInput" 
                                       placeholder="Scan barcode atau cari produk..." 
                                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                            </div>
                        </div>
                        <div class="p-4 max-h-[500px] overflow-y-auto">
                            <div id="productList" class="space-y-2">
                                @forelse($products as $p)
                                <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition"
                                     onclick="addProductToCart({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $p->stock }})">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $p->name }}</p>
                                        <p class="text-xs text-gray-500">Stok: {{ $p->stock }} {{ $p->unit }} | Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                                    </div>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm transition">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                                @empty
                                <div class="text-center text-gray-500 py-12">
                                    <i class="fas fa-box-open text-4xl text-gray-300 mb-3 block"></i>
                                    <p>Belum ada produk tersedia</p>
                                    <p class="text-xs mt-1">Silakan tambah produk terlebih dahulu</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Section -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-4 border-b bg-gray-50 rounded-t-xl">
                        <h3 class="font-semibold text-gray-800">
                            <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>
                            Keranjang Belanja
                        </h3>
                    </div>
                    
                    <div class="p-4 h-[400px] overflow-y-auto" id="cartItems">
                        @if(empty($cart))
                        <div class="text-center text-gray-500 py-12">
                            <i class="fas fa-cart-plus text-4xl text-gray-300 mb-3 block"></i>
                            <p>Keranjang kosong</p>
                            <p class="text-xs mt-1">Klik produk di samping untuk menambah</p>
                        </div>
                        @else
                            @foreach($cart as $item)
                            <div class="flex justify-between items-center mb-3 pb-3 border-b" id="cart-item-{{ $item['product_id'] }}">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $item['name'] }}</p>
                                    <p class="text-xs text-gray-500">
                                        @ {{ number_format($item['price'], 0, ',', '.') }} x 
                                        <span id="qty-{{ $item['product_id'] }}">{{ $item['quantity'] }}</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                    <button onclick="removeFromCart({{ $item['product_id'] }})" 
                                            class="text-red-600 hover:text-red-800 text-xs transition">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <div class="p-4 border-t bg-gray-50">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span id="cartSubtotal" class="font-semibold">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Pajak (11%)</span>
                                <span id="cartTax" class="font-semibold">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t">
                                <span>Total</span>
                                <span id="cartTotal" class="text-blue-600">Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-money-bill-wave mr-1 text-green-600"></i>
                                Jumlah Tunai
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                <input type="number" id="cashInput" 
                                       class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 text-sm"
                                       placeholder="0" 
                                       oninput="calculateChange()">
                            </div>
                        </div>
                        
                        <div class="mt-3 flex justify-between items-center">
                            <span class="text-sm text-gray-600">Kembalian</span>
                            <span id="changeAmount" class="text-xl font-bold text-green-600">Rp 0</span>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-sticky-note mr-1 text-gray-500"></i>
                                Catatan (Opsional)
                            </label>
                            <textarea id="notes" rows="2" class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm p-2"
                                      placeholder="Contoh: Pembayaran dengan kartu, dll"></textarea>
                        </div>
                        
                        <div class="mt-5">
                            <button onclick="submitTransaction()" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i>
                                <span>Proses Transaksi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function updateRealTimeClock() {
        const now = new Date();
        
        // Nama hari
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const dayName = days[now.getDay()];
        
        // Format tanggal
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const formattedDateTime = `${dayName}, ${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        
        const datetimeElement = document.getElementById('real-time-datetime');
        if (datetimeElement) {
            datetimeElement.textContent = formattedDateTime;
        }
    }

    setInterval(updateRealTimeClock, 1000);

    updateRealTimeClock();

    let cart = @json($cart);
    let subtotal = 0;
    
    // SweetAlert2 notification functions
    function showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: message,
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    }
    
    function showWarning(message) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: message,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'OK'
        });
    }
    
    function showInfo(message) {
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: message,
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'OK'
        });
    }
    
    function showLoading(message) {
        Swal.fire({
            title: message || 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    function updateCartDisplay() {
        let container = document.getElementById('cartItems');
        
        if (Object.keys(cart).length === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 py-12"><i class="fas fa-cart-plus text-4xl text-gray-300 mb-3 block"></i><p>Keranjang kosong</p><p class="text-xs mt-1">Klik produk di samping untuk menambah</p></div>';
            subtotal = 0;
        } else {
            let html = '';
            subtotal = 0;
            for (let id in cart) {
                let item = cart[id];
                subtotal += item.subtotal;
                html += `<div class="flex justify-between items-center mb-3 pb-3 border-b" id="cart-item-${id}">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">${escapeHtml(item.name)}</p>
                                <p class="text-xs text-gray-500">@ ${new Intl.NumberFormat('id-ID').format(item.price)} x <span id="qty-${id}">${item.quantity}</span></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</p>
                                <button onclick="removeFromCart(${id})" class="text-red-600 hover:text-red-800 text-xs transition">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                        </div>`;
            }
            container.innerHTML = html;
        }
        
        let tax = Math.round(subtotal * 0.11);
        let total = subtotal + tax;
        
        document.getElementById('cartSubtotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        document.getElementById('cartTax').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(tax);
        document.getElementById('cartTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        calculateChange();
    }
    
    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
    
    function addProductToCart(id, name, price, stock) {
        if (!cart[id]) {
            cart[id] = {
                product_id: id,
                name: name,
                price: price,
                quantity: 1,
                subtotal: price
            };
            showSuccess(`${name} ditambahkan ke keranjang`);
        } else {
            if (cart[id].quantity + 1 > stock) {
                showWarning(`Stok ${name} tidak mencukupi! Tersedia: ${stock}`);
                return;
            }
            cart[id].quantity++;
            cart[id].subtotal = cart[id].price * cart[id].quantity;
            showSuccess(`${name} ditambahkan (${cart[id].quantity} item)`);
        }
        updateCartDisplay();
    }
    
    function removeFromCart(id) {
        let productName = cart[id].name;
        Swal.fire({
            title: 'Hapus Item?',
            text: `Apakah Anda yakin ingin menghapus ${productName} dari keranjang?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                delete cart[id];
                updateCartDisplay();
                showSuccess(`${productName} dihapus dari keranjang`);
            }
        });
    }
    
    function calculateChange() {
        let totalText = document.getElementById('cartTotal').innerText;
        let total = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;
        let cash = parseInt(document.getElementById('cashInput').value) || 0;
        let change = cash - total;
        document.getElementById('changeAmount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(change >= 0 ? change : 0);
        
        let changeElement = document.getElementById('changeAmount');
        if (cash < total) {
            changeElement.classList.remove('text-green-600');
            changeElement.classList.add('text-red-600');
        } else {
            changeElement.classList.remove('text-red-600');
            changeElement.classList.add('text-green-600');
        }
    }
    
    function submitTransaction() {
        if (Object.keys(cart).length === 0) {
            showWarning('Keranjang masih kosong! Silakan tambahkan produk terlebih dahulu.');
            return;
        }
        
        let totalText = document.getElementById('cartTotal').innerText;
        let total = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;
        let cash = parseInt(document.getElementById('cashInput').value) || 0;
        
        if (cash < total) {
            Swal.fire({
                icon: 'error',
                title: 'Uang Kurang!',
                html: `
                    <div style="text-align: left;">
                        <p>Uang tunai kurang dari total belanja!</p>
                        <hr class="my-2">
                        <table style="width: 100%;">
                            <tr><td>Total Belanja</td><td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(total)}</td></tr>
                            <tr><td>Tunai</td><td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(cash)}</td></tr>
                            <tr style="color: red;"><td>Kekurangan</td><td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(total - cash)}</td></tr>
                        </table>
                    </div>
                `,
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        let items = [];
        for (let id in cart) {
            items.push({
                product_id: id,
                quantity: cart[id].quantity
            });
        }
        
        // Konfirmasi sebelum proses
        Swal.fire({
            title: 'Konfirmasi Transaksi',
            html: `
                <div style="text-align: left;">
                    <table style="width: 100%;">
                        <tr><td>Total Belanja</td><td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(total)}</td></tr>
                        <tr><td>Tunai</td><td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(cash)}</td></tr>
                        <tr style="color: green;"><td>Kembalian</td><td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(cash - total)}</td></tr>
                    </table>
                    <hr>
                    <p class="text-center text-gray-500">Apakah data sudah benar?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses Transaksi...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Ambil CSRF token dari meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch('{{ route("transactions.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        items: items,
                        cash: cash,
                        notes: document.getElementById('notes').value
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.redirect) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil!',
                            text: 'Struk akan dicetak',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Transaksi Gagal!',
                            text: data.error,
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: 'Gagal memproses transaksi. Silakan coba lagi.',
                        confirmButtonColor: '#d33'
                    });
                });
            }
        });
    }
    
    // Barcode scanner
    document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            let barcode = this.value.trim();
            if (barcode === '') return;
            
            fetch('/product/' + encodeURIComponent(barcode))
                .then(response => response.json())
                .then(product => {
                    if (product.id) {
                        addProductToCart(product.id, product.name, product.price, product.stock);
                    } else if (product.error) {
                        showWarning(product.error);
                    } else {
                        showWarning('Produk tidak ditemukan! Periksa kembali barcode.');
                    }
                    this.value = '';
                })
                .catch(() => {
                    showError('Error scanning barcode. Pastikan koneksi internet stabil.');
                    this.value = '';
                });
        }
    });
    
    // Initialize cart display
    updateCartDisplay();
</script>