<footer class="mt-auto py-3 px-4 bg-white border-top text-muted small">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        
        {{-- Left Side: Copyright --}}
        <div class="mb-2 mb-md-0">
            <span class="fw-bold text-dark">&copy; {{ date('Y') }} Nexus Platform.</span>
            <span class="mx-1 text-muted opacity-50">|</span>
            <span class="opacity-75">Secure Architecture v2.0</span>
        </div>

        {{-- Right Side: Links & Policy --}}
        <div class="d-flex align-items-center gap-3">
            <a href="#" class="text-decoration-none text-muted hover-text-primary transition-base">Privacy Policy</a>
            <a href="#" class="text-decoration-none text-muted hover-text-primary transition-base">Terms of Service</a>
            
            {{-- Optional: Only show Support link to registered users --}}
            @auth
                <a href="#" class="text-decoration-none text-muted hover-text-primary transition-base">Support</a>
            @endauth
        </div>
    </div>
</footer>