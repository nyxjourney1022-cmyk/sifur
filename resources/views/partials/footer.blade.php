<footer class="container footer">
    <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
    {{-- BACKEND: $address & $phone (string, opsional) --}}
    <span>{{ $address ?? '[ALAMAT SHOWROOM]' }} · {{ $phone ?? '[NOMOR TELEPON]' }}</span>
</footer>
