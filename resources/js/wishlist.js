/**
 * Wishlist optimistic UI — localStorage + API sync
 */
document.addEventListener('alpine:init', () => {
    const STORAGE_KEY = 'marrakechtours_wishlist';
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

    Alpine.store('wishlist', {
        ids: [],
        loaded: false,

        initFromStorage() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                this.ids = raw ? JSON.parse(raw) : [];
            } catch {
                this.ids = [];
            }
        },

        persist() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(this.ids));
        },

        has(tourId) {
            return this.ids.includes(Number(tourId));
        },

        async loadFromServer() {
            const isAuthenticated = document.querySelector('meta[name="client-authenticated"]')?.content === '1';
            if (!isAuthenticated) {
                this.loaded = true;
                return;
            }

            try {
                const res = await fetch('/api/wishlist', {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data.success && Array.isArray(data.tour_ids)) {
                    this.ids = data.tour_ids.map(Number);
                    this.persist();
                }
            } catch {
                // guest or offline
            } finally {
                this.loaded = true;
            }
        },

        async syncGuestToServer() {
            if (this.ids.length === 0) return;
            try {
                await fetch('/api/wishlist/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ tour_ids: this.ids }),
                });
            } catch {
                // ignore
            }
        },

        async toggle(tourId) {
            tourId = Number(tourId);
            const wasIn = this.has(tourId);

            if (wasIn) {
                this.ids = this.ids.filter((id) => id !== tourId);
            } else {
                this.ids = [...this.ids, tourId];
            }
            this.persist();

            try {
                const res = await fetch('/api/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ tour_id: tourId }),
                });
                const data = await res.json();

                if (res.status === 401) {
                    if (wasIn) this.ids = [...this.ids, tourId];
                    else this.ids = this.ids.filter((id) => id !== tourId);
                    this.persist();
                    Alpine.store('loginModal')?.openModal();
                    return { loginRequired: true };
                }

                if (!data.success) {
                    if (wasIn) this.ids = [...this.ids, tourId];
                    else this.ids = this.ids.filter((id) => id !== tourId);
                    this.persist();
                }

                return data;
            } catch {
                if (wasIn) this.ids = [...this.ids, tourId];
                else this.ids = this.ids.filter((id) => id !== tourId);
                this.persist();
                return { error: true };
            }
        },
    });

    Alpine.store('wishlist').initFromStorage();
    Alpine.store('wishlist').loadFromServer();

    Alpine.data('wishlistButton', (tourId) => ({
        tourId: Number(tourId),
        loading: false,

        get wishlisted() {
            return Alpine.store('wishlist').has(this.tourId);
        },

        async toggle(event) {
            event?.preventDefault();
            event?.stopPropagation();
            if (this.loading) return;
            this.loading = true;
            await Alpine.store('wishlist').toggle(this.tourId);
            this.loading = false;
        },
    }));
});
