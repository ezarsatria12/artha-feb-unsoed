<footer class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.02)] flex justify-around py-3 pb-6 z-50 max-w-[480px] mx-auto left-0 right-0">
    
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 w-1/5 group">
        <i class="ph ph-house text-2xl {{ request()->routeIs('dashboard') ? 'text-[#37967D] ph-fill' : 'text-gray-300 group-hover:text-[#37967D]' }} transition-colors"></i>
        <span class="text-[10px] font-medium {{ request()->routeIs('dashboard') ? 'text-[#37967D]' : 'text-gray-300 group-hover:text-[#37967D]' }}">Beranda</span>
    </a>

    <a href="{{ route('menu.index') }}" class="flex flex-col items-center gap-1 w-1/5 group">
        <i class="ph ph-fork-knife text-2xl {{ request()->routeIs('menu.*') ? 'text-[#37967D] ph-fill' : 'text-gray-300 group-hover:text-[#37967D]' }} transition-colors"></i>
        <span class="text-[10px] font-medium {{ request()->routeIs('menu.*') ? 'text-[#37967D]' : 'text-gray-300 group-hover:text-[#37967D]' }}">Menu</span>
    </a>

    <a href="{{ route('orders.create') }}" class="flex flex-col items-center gap-1 w-1/5 group">
        <i class="ph ph-receipt text-2xl {{ request()->routeIs('orders.*') ? 'text-[#37967D] ph-fill' : 'text-gray-300 group-hover:text-[#37967D]' }} transition-colors"></i>
        <span class="text-[10px] font-medium {{ request()->routeIs('orders.*') ? 'text-[#37967D]' : 'text-gray-300 group-hover:text-[#37967D]' }}">Pesanan</span>
    </a>

    <a href="{{ route('reports.index') }}" class="flex flex-col items-center gap-1 w-1/5 group">
        <i class="ph ph-chart-line-up text-2xl {{ request()->routeIs('reports.*') ? 'text-[#37967D] ph-fill' : 'text-gray-300 group-hover:text-[#37967D]' }} transition-colors"></i>
        <span class="text-[10px] font-medium {{ request()->routeIs('reports.*') ? 'text-[#37967D]' : 'text-gray-300 group-hover:text-[#37967D]' }}">Penjualan</span>
    </a>

    <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 w-1/5 group">
        <i class="ph ph-user text-2xl {{ request()->routeIs('profile.*') ? 'text-[#37967D] ph-fill' : 'text-gray-300 group-hover:text-[#37967D]' }} transition-colors"></i>
        <span class="text-[10px] font-medium {{ request()->routeIs('profile.*') ? 'text-[#37967D]' : 'text-gray-300 group-hover:text-[#37967D]' }}">Profile</span>
    </a>
</footer>