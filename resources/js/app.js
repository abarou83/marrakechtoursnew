import './bootstrap';
import './wishlist';
import './recently-viewed';
import './tour-gallery-alpine';

document.addEventListener('alpine:init', () => {
    Alpine.store('loginModal', {
        open: false,
        activeTab: 'login',

        openModal() {
            this.open = true;
            document.documentElement.classList.add('login-modal-open');
        },

        closeModal() {
            this.open = false;
            document.documentElement.classList.remove('login-modal-open');
        },

        setActiveTab(tab) {
            this.activeTab = tab;
        },
    });
});
