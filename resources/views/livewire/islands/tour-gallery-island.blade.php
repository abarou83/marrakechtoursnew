<div>
@if($tourImages->count() > 0)
<div id="tour-hero" wire:ignore x-data="{
    images: @js($galleryImages),
    allImages: @js($allGalleryImages),
    galleryOpen: false,
    currentGalleryIndex: 0,
    selectMainImage(index) {
        if (index === 0) return;
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
    openGallery(index = 0) {
        if (this.allImages.length === 0) return;
        this.currentGalleryIndex = index >= this.allImages.length ? 0 : index;
        this.galleryOpen = true;
        document.body.style.overflow = 'hidden';
    },
    closeGallery() {
        this.galleryOpen = false;
        document.body.style.overflow = '';
    },
    nextImage() { if (this.currentGalleryIndex < this.allImages.length - 1) this.currentGalleryIndex++; },
    prevImage() { if (this.currentGalleryIndex > 0) this.currentGalleryIndex--; }
}" class="mb-8 island-gallery">
    <div class="flex flex-col lg:flex-row gap-4">
        <div class="{{ $tourImages->count() > 1 ? 'lg:w-2/3' : 'w-full' }} relative group bg-white rounded-xl overflow-hidden shadow-lg cursor-pointer" @click="openGallery(0)">
            <div class="relative w-full h-[300px] md:h-[350px] lg:h-[400px] overflow-hidden">
                <img :src="images[0].path" :alt="images[0].alt" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 text-gray-800 p-3 rounded-full shadow-lg">
                        <i class="fas fa-expand text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        @if($tourImages->count() > 1)
        <div class="lg:w-1/3 flex flex-col gap-4 h-[300px] md:h-[350px] lg:h-[400px]">
            <div class="flex-1 relative group bg-white rounded-xl overflow-hidden shadow-lg cursor-pointer" @click="selectMainImage(1)" x-show="images.length > 1">
                <img :src="images[1]?.path" :alt="images[1]?.alt" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
            </div>
            @if($tourImages->count() > 2)
            <div class="flex-1 relative group bg-white rounded-xl overflow-hidden shadow-lg cursor-pointer" @click="openGallery(2)" x-show="images.length > 2">
                <img :src="images[2]?.path" :alt="images[2]?.alt" class="absolute inset-0 w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                    <span class="text-white font-semibold">{{ __('View more') }}</span>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>

    <template x-if="galleryOpen">
        <div @keydown.escape.window="closeGallery()" @click.self="closeGallery()" class="fixed inset-0 bg-black/90 flex items-center justify-center z-[99999]">
            <button @click="closeGallery()" class="absolute top-4 right-4 text-white bg-black/50 rounded-full p-3"><i class="fas fa-times text-2xl"></i></button>
            <button @click="prevImage()" x-show="currentGalleryIndex > 0" class="absolute left-4 text-white bg-black/50 rounded-full p-4"><i class="fas fa-chevron-left text-2xl"></i></button>
            <button @click="nextImage()" x-show="currentGalleryIndex < allImages.length - 1" class="absolute right-4 text-white bg-black/50 rounded-full p-4"><i class="fas fa-chevron-right text-2xl"></i></button>
            <img x-bind:src="allImages[currentGalleryIndex]?.path" class="max-w-full max-h-[90vh] object-contain rounded-lg" alt="">
        </div>
    </template>
</div>
@else
<div class="mb-8 p-8 text-center text-gray-500 bg-gray-50 rounded-xl">{{ __('No images available') }}</div>
@endif
</div>
