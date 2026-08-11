function tourGalleryFactory(initialImages, allImages) {
    return {
        images: initialImages,
        allImages,
        galleryOpen: false,
        currentGalleryIndex: 0,
        selectMainImage(index) {
            if (index === 0) {
                return;
            }
            const selected = this.images[index];
            const currentMain = this.images[0];
            if (index === 1 || index === 2) {
                this.images[0] = selected;
                this.images[index] = currentMain;
            } else {
                this.images[0] = selected;
                this.images[1] = this.images[1] || currentMain;
                this.images[2] = this.images[2] || currentMain;
            }
        },
        openGallery(index) {
            if (typeof index !== 'number') {
                index = 0;
            }
            if (this.allImages.length === 0) {
                return;
            }
            this.currentGalleryIndex = index >= this.allImages.length ? 0 : index;
            this.galleryOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeGallery() {
            this.galleryOpen = false;
            document.body.style.overflow = '';
        },
        nextImage() {
            if (this.currentGalleryIndex < this.allImages.length - 1) {
                this.currentGalleryIndex++;
            }
        },
        prevImage() {
            if (this.currentGalleryIndex > 0) {
                this.currentGalleryIndex--;
            }
        },
    };
}

function registerTourGallery() {
    if (typeof window.Alpine === 'undefined') {
        return;
    }

    window.Alpine.data('tourGallery', tourGalleryFactory);
}

function initGalleryTrees() {
    if (typeof window.Alpine === 'undefined') {
        return;
    }

    document.querySelectorAll('#tour-hero[x-data]').forEach((el) => {
        if (!el._x_dataStack) {
            window.Alpine.initTree(el);
        }
    });
}

document.addEventListener('livewire:init', registerTourGallery);
document.addEventListener('alpine:init', registerTourGallery);
document.addEventListener('DOMContentLoaded', () => {
    registerTourGallery();
    initGalleryTrees();
});
