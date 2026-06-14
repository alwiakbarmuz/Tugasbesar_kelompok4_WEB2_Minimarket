<x-app-layout title="Informasi Cabang">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Detail Cabang') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Informasi lengkap cabang {{ $branch->name }}
                </p>
            </div>
            <div class="flex space-x-3 flex-wrap gap-2">
                <!-- Reset Password Massal -->
                <button onclick="confirmResetPasswords({{ $branch->id }}, '{{ $branch->name }}')" 
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition shadow-sm">
                    <i class="fas fa-key"></i>
                    <span>Reset Password</span>
                </button>
                
                <!-- Edit Cabang -->
                <a href="{{ route('branches.edit', $branch) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition shadow-sm">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                
                <!-- Toggle Status -->
                <form action="{{ route('branches.toggle-status', $branch) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition shadow-sm">
                        <i class="fas fa-power-off"></i>
                        <span>{{ $branch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                    </button>
                </form>
                
                <!-- Kembali -->
                <a href="{{ route('branches.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Branch Info Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-store text-white text-2xl"></i>
                                <div>
                                    <h3 class="text-white font-bold text-xl">{{ $branch->name }}</h3>
                                    <p class="text-blue-100 text-sm">Kode: {{ $branch->code }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-start space-x-3 border-b pb-3">
                                <i class="fas fa-map-marker-alt text-blue-500 mt-1"></i>
                                <div>
                                    <p class="font-medium text-gray-700">Alamat</p>
                                    <p class="text-gray-600">{{ $branch->address }}</p>
                                    <p class="text-gray-500 text-sm">{{ $branch->city }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 border-b pb-3">
                                <i class="fas fa-phone text-blue-500"></i>
                                <div>
                                    <p class="font-medium text-gray-700">Telepon</p>
                                    <p class="text-gray-600">{{ $branch->phone }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-calendar-alt text-blue-500"></i>
                                <div>
                                    <p class="font-medium text-gray-700">Bergabung Sejak</p>
                                    <p class="text-gray-600">{{ $branch->created_at->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-blue-100 text-sm">Total Produk</p>
                                <p class="text-3xl font-bold">{{ $stats['products_count'] }}</p>
                            </div>
                            <i class="fas fa-boxes text-3xl text-blue-200"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-green-100 text-sm">Total Karyawan</p>
                                <p class="text-3xl font-bold">{{ $stats['employees_count'] }}</p>
                            </div>
                            <i class="fas fa-users text-3xl text-green-200"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-purple-100 text-sm">Total Transaksi</p>
                                <p class="text-3xl font-bold">{{ number_format($stats['transactions_count']) }}</p>
                            </div>
                            <i class="fas fa-shopping-cart text-3xl text-purple-200"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-sm mt-6 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Pendapatan 12 Bulan Terakhir</h3>
                </div>
                <div class="p-6">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
            
            <!-- Daftar Karyawan -->
            <div class="bg-white rounded-xl shadow-sm mt-6 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-users mr-2 text-blue-500"></i>
                                Daftar Karyawan
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $stats['employees_count'] }} karyawan terdaftar di cabang ini
                            </p>
                        </div>
                        <button onclick="confirmResetPasswords({{ $branch->id }}, '{{ $branch->name }}')" 
                                class="text-sm bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1.5 rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-key"></i>
                            Reset Semua Password
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Nama</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3">Status Password</th>
                                <th class="px-6 py-3">Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($user->roles->first()->name ?? 'User') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->must_change_password)
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-triangle"></i> Perlu Ganti
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle"></i> Aman
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-user-slash text-4xl text-gray-300 mb-3 block"></i>
                                    <p>Belum ada karyawan di cabang ini</p>
                                    <p class="text-xs mt-1">Karyawan akan otomatis dibuat saat cabang ditambahkan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Recent Transactions -->
            <div class="bg-white rounded-xl shadow-sm mt-6 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Invoice</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Kasir</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr class="border-b">
                                <td class="px-6 py-4">{{ $transaction->invoice_number }}</td>
                                <td class="px-6 py-4">{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">{{ $transaction->cashier->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $transaction->status == 'completed' ? 'Selesai' : 'Dibatalkan' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-8 text-gray-500">Belum ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Low Stock Products -->
            @if($lowStockProducts->count() > 0)
            <div class="bg-white rounded-xl shadow-sm mt-6 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-yellow-50">
                    <h3 class="text-lg font-semibold text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Peringatan Stok Menipis
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Produk</th>
                                <th class="px-6 py-3 text-center">Stok Saat Ini</th>
                                <th class="px-6 py-3 text-center">Stok Minimal</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                            <tr class="border-b">
                                <td class="px-6 py-4">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-center font-bold text-red-600">{{ $product->stock }}</td>
                                <td class="px-6 py-4 text-center">{{ $product->min_stock }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Perlu Restok</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Reset Password Confirmation Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Reset Password Massal</h3>
                </div>
                <p class="text-gray-600 mb-3">
                    Apakah Anda yakin ingin mereset <strong>SEMUA password</strong> di cabang 
                    <span id="resetBranchName" class="font-semibold text-orange-600"></span>?
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4 rounded">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-500 mt-0.5 mr-2"></i>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Dampak Reset Password:</p>
                            <ul class="list-disc list-inside text-xs mt-1 space-y-1">
                                <li>Semua karyawan akan mendapatkan password default: <strong>password123</strong></li>
                                <li>Semua karyawan akan diwajibkan mengganti password saat login berikutnya</li>
                                <li>Password lama akan tidak dapat digunakan lagi</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form id="resetPasswordForm" method="POST" action="">
                    @csrf
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeResetModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-key"></i> Ya, Reset Semua Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: { 
            labels: @json($monthlyData['labels']), 
            datasets: [{ 
                label: 'Pendapatan (Rp)', 
                data: @json($monthlyData['revenue']), 
                borderColor: 'rgb(59, 130, 246)', 
                backgroundColor: 'rgba(59, 130, 246, 0.1)', 
                tension: 0.4, 
                fill: true,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }] 
        },
        options: { 
            responsive: true, 
            plugins: { 
                tooltip: { 
                    callbacks: { 
                        label: (ctx) => 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                    } 
                } 
            }, 
            scales: { 
                y: { 
                    beginAtZero: true,
                    ticks: { 
                        callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
                    } 
                } 
            } 
        }
    });
    
    function confirmResetPasswords(branchId, branchName) {
        document.getElementById('resetBranchName').textContent = branchName;
        document.getElementById('resetPasswordForm').action = `/branches/${branchId}/reset-passwords`;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
        document.getElementById('resetPasswordModal').classList.add('flex');
    }
    
    function closeResetModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
        document.getElementById('resetPasswordModal').classList.remove('flex');
    }
</script>