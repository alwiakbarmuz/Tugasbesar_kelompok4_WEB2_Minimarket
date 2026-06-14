<x-app-layout title="Edit Cabang">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Cabang') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Mengedit informasi cabang: {{ $branch->name }}
                </p>
            </div>
            <a href="{{ route('branches.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <form method="POST" action="{{ route('branches.update', $branch) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Code & Name Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-barcode mr-2 text-blue-500"></i>
                                Kode Cabang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" value="{{ old('code', $branch->code) }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-store mr-2 text-blue-500"></i>
                                Nama Cabang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $branch->name) }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-city mr-2 text-blue-500"></i>
                            Kota <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" value="{{ old('city', $branch->city) }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" rows="3" required
                                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">{{ old('address', $branch->address) }}</textarea>
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Phone & Status Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-phone mr-2 text-blue-500"></i>
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="flex items-center cursor-pointer mt-8">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    <i class="fas fa-check-circle mr-1 text-green-500"></i>
                                    Cabang aktif
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('branches.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg shadow-md hover:shadow-lg transition">
                            <i class="fas fa-save mr-2"></i>
                            Update Cabang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>