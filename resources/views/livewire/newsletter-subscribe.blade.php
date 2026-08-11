<div>
    @if($subscribed)
        <p class="text-sm text-emerald-400 leading-snug">{{ $message }}</p>
    @else
        <form wire:submit="subscribe"
              @class([
                  'w-full',
                  'flex flex-col gap-2' => $compact,
                  'flex flex-col sm:flex-row gap-3' => ! $compact,
              ])>
            <input type="email"
                   wire:model="email"
                   placeholder="{{ __('Votre email') }}"
                   @class([
                       'w-full min-w-0 rounded-lg bg-white/10 border border-white/15 text-white placeholder-white/40 text-sm focus:outline-none focus:ring-2 focus:ring-primary/80 focus:border-transparent',
                       'px-3 py-2' => $compact,
                       'flex-1 px-4 py-3' => ! $compact,
                   ])>
            <button type="submit"
                    wire:loading.attr="disabled"
                    @class([
                        'bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition shrink-0 disabled:opacity-60',
                        'px-3 py-2 w-full' => $compact,
                        'px-5 py-3 whitespace-nowrap' => ! $compact,
                    ])>
                {{ __('S\'inscrire') }}
            </button>
        </form>
        @error('email') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
    @endif
</div>
