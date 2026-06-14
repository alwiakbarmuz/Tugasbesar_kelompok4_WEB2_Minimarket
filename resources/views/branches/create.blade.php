<x-app-layout title="Tambah Cabang">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Tambah Cabang Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Tambahkan cabang baru ke jaringan minimarket Jayusman
                </p>
            </div>
            <a href="{{ route('branches.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Peringatan Maksimal Cabang -->
            @php
                $maxBranches = $maxBranches ?? 5;
                $remainingSlots = $remainingSlots ?? 0;
                $isFull = $isFull ?? false;
            @endphp

            @if($isFull)
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-red-800">Slot Cabang Penuh!</p>
                        <p class="text-sm text-red-700">
                            Maksimal cabang adalah <strong>{{ $maxBranches }}</strong> cabang. 
                            Saat ini sudah mencapai batas maksimal.
                            Tidak dapat menambahkan cabang baru.
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-blue-800">Informasi Slot Cabang</p>
                        <p class="text-sm text-blue-700">
                            Maksimal cabang: <strong>{{ $maxBranches }}</strong> cabang |
                            <strong>Sisa slot: {{ $remainingSlots }}</strong> cabang
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <form method="POST" action="{{ route('branches.store') }}" class="p-6 space-y-6">
                    @csrf
                    
                    <!-- Code & Name Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-barcode mr-2 text-blue-500"></i>
                                Kode Cabang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" value="{{ old('code') }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                   placeholder="Contoh: JKT01, BDG01, SBY01">
                            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-store mr-2 text-blue-500"></i>
                                Nama Cabang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                   placeholder="Contoh: Mini Market Jayusman Jakarta">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-city mr-2 text-blue-500"></i>
                            Kota <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                               placeholder="Contoh: Jakarta, Bandung, Surabaya, Semarang, Yogyakarta">
                            @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" rows="3" required
                                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                  placeholder="Jl. Sudirman No. 123, RT/RW...">{{ old('address') }}</textarea>
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Phone & Status Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-phone mr-2 text-blue-500"></i>
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                   placeholder="021-1234567">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="flex items-center cursor-pointer mt-8">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    <i class="fas fa-check-circle mr-1 text-green-500"></i>
                                    Aktifkan cabang ini
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('branches.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                            Batal
                        </a>
                        @if(!$isFull)
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg shadow-md hover:shadow-lg transition">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Cabang
                        </button>
                        @else
                        <button type="button" disabled
                                class="px-6 py-2 bg-gray-400 cursor-not-allowed text-white rounded-lg shadow-md"
                                title="Slot cabang sudah penuh">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Cabang
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>