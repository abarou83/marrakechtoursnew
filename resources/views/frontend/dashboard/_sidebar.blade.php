<aside class="lg:w-64 flex-shrink-0">
    <nav class="card p-4 space-y-1 sticky top-24">
        <a href="{{ route('dashboard.index') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-primary-50 text-primary-700 font-medium' => request()->routeIs('dashboard.index'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('dashboard.index'),
           ])>
            <x-heroicon-o-home class="w-5 h-5" />
            {{ __('Tableau de bord') }}
        </a>

        <a href="{{ route('dashboard.bookings') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-primary-50 text-primary-700 font-medium' => request()->routeIs('dashboard.bookings*'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('dashboard.bookings*'),
           ])>
            <x-heroicon-o-ticket class="w-5 h-5" />
            {{ __('Mes réservations') }}
        </a>

        <a href="{{ route('dashboard.wishlist') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-primary-50 text-primary-700 font-medium' => request()->routeIs('dashboard.wishlist'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('dashboard.wishlist'),
           ])>
            <x-heroicon-o-heart class="w-5 h-5" />
            {{ __('Mes favoris') }}
        </a>

        <a href="{{ route('dashboard.reviews') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-primary-50 text-primary-700 font-medium' => request()->routeIs('dashboard.reviews*'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('dashboard.reviews*'),
           ])>
            <x-heroicon-o-star class="w-5 h-5" />
            {{ __('Mes avis') }}
        </a>

        <hr class="my-2 border-sand-200" />

        <a href="{{ route('dashboard.profile') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-primary-50 text-primary-700 font-medium' => request()->routeIs('dashboard.profile'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('dashboard.profile'),
           ])>
            <x-heroicon-o-user class="w-5 h-5" />
            {{ __('Mon profil') }}
        </a>

        <a href="{{ route('dashboard.notifications') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-primary-50 text-primary-700 font-medium' => request()->routeIs('dashboard.notifications'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('dashboard.notifications'),
           ])>
            <x-heroicon-o-bell class="w-5 h-5" />
            {{ __('Notifications') }}
        </a>

        <a href="{{ route('dashboard.referral') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-primary-50 text-primary-700 font-medium' => request()->routeIs('dashboard.referral'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('dashboard.referral'),
           ])>
            <x-heroicon-o-gift class="w-5 h-5" />
            {{ __('Parrainage') }}
        </a>

        <hr class="my-2 border-sand-200" />

        <p class="px-4 py-2 text-xs font-semibold text-sand-400 uppercase tracking-wider">
            {{ __('Vie privée') }}
        </p>

        <a href="{{ route('client.gdpr.export.request') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-primary-50 text-primary-700 font-medium' => request()->routeIs('client.gdpr.export*'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('client.gdpr.export*'),
           ])>
            <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
            {{ __('Exporter mes données') }}
        </a>

        <a href="{{ route('client.gdpr.delete.request') }}" 
           @class([
               'flex items-center gap-3 px-4 py-3 rounded-lg transition',
               'bg-red-50 text-red-700 font-medium' => request()->routeIs('client.gdpr.delete*'),
               'text-sand-600 hover:bg-sand-50' => !request()->routeIs('client.gdpr.delete*'),
           ])>
            <x-heroicon-o-trash class="w-5 h-5" />
            {{ __('Supprimer mon compte') }}
        </a>

        <hr class="my-2 border-sand-200" />

        <form method="POST" action="{{ route('client.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition">
                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                {{ __('Déconnexion') }}
            </button>
        </form>
    </nav>
</aside>
