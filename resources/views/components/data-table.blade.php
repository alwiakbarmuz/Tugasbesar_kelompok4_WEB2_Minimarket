@props(['headers' => [], 'actions' => false])

<div class="overflow-x-auto relative shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="py-3 px-6">{{ $header }}</th>
                @endforeach
                @if($actions)
                    <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>