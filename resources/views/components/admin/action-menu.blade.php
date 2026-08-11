@props([
    'width' => 'w-56',
])

<div
    x-data="{
        open: false,
        menuTop: 0,
        menuRight: 0,
        openUpward: false,
        updatePosition() {
            const btn = this.$refs.trigger;
            if (!btn) return;

            const rect = btn.getBoundingClientRect();
            const estimatedHeight = this.$refs.menu?.offsetHeight || 280;
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;

            this.openUpward = spaceBelow < estimatedHeight && spaceAbove > spaceBelow;
            this.menuRight = window.innerWidth - rect.right;

            if (this.openUpward) {
                this.menuTop = rect.top - 8;
            } else {
                this.menuTop = rect.bottom + 8;
            }
        },
        toggle() {
            if (this.open) {
                this.open = false;
                return;
            }

            this.open = true;
            this.$nextTick(() => {
                this.updatePosition();
                this.$nextTick(() => this.updatePosition());
            });
        },
        closeUnlessTrigger(event) {
            if (this.$refs.trigger?.contains(event.target)) {
                return;
            }

            this.open = false;
        },
    }"
    class="relative inline-block text-left"
>
    <button
        type="button"
        x-ref="trigger"
        @click.stop="toggle()"
        @keydown.escape.window="open = false"
        class="inline-flex justify-center w-10 h-10 items-center rounded-full border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-200"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        <i class="fas fa-ellipsis-vertical"></i>
    </button>

    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            x-ref="menu"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.away="closeUnlessTrigger($event)"
            @keydown.escape.window="open = false"
            @scroll.window="open && updatePosition()"
            @resize.window="open && updatePosition()"
            :style="openUpward
                ? `position: fixed; top: auto; bottom: ${window.innerHeight - menuTop}px; right: ${menuRight}px;`
                : `position: fixed; top: ${menuTop}px; right: ${menuRight}px;`"
            class="{{ $width }} max-h-[min(70vh,24rem)] overflow-y-auto rounded-lg shadow-xl bg-white ring-1 ring-black/5 z-[9999]"
        >
            <div class="py-1 text-sm text-gray-700 text-left">
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
