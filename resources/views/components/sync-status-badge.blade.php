<!-- Sync Status Badge Component -->
<!-- Affiche le statut global de synchronisation avec couleur et indicator -->

@php
    use Illuminate\Support\Facades\DB;
    
    $pending = DB::table('sync_queue')->where('status', 'pending')->count();
    $error = DB::table('sync_queue')->where('status', 'error')->count();
@endphp

<div class="flex items-center gap-2" title="Statut de synchronisation">
    @if($error > 0)
        <!-- Erreurs en attente -->
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
            <span class="inline-block w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
            {{ $error }} erreur{{ $error > 1 ? 's' : '' }}
        </div>
    @elseif($pending > 0)
        <!-- Opérations en attente -->
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
            <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
            {{ $pending }} en attente
        </div>
    @else
        <!-- Tout synchronisé -->
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
            <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
            Synchronisé
        </div>
    @endif
    
    <!-- Lien vers le dashboard de sync -->
    <a href="{{ route('sync.status') }}" class="ml-2 text-gray-600 hover:text-gray-900" title="Ouvrir le dashboard de synchronisation">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
    </a>
</div>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
