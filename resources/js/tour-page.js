import { trackRecentlyViewed } from './recently-viewed';
import { readTourPageConfig } from './tour-page-config';

function formatPrice(price, config) {
    if (price === null || price === undefined) {
        return null;
    }

    return `${new Intl.NumberFormat(config.locale, {
        style: 'decimal',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(price)} ${config.currencySymbol}`;
}

function findPricingForParticipants(participants, pricingData) {
    const matchingPricings = pricingData.filter((p) => {
        const matchesMin = p.min_participants <= participants;
        const matchesMax = p.max_participants === null || p.max_participants >= participants;
        return matchesMin && matchesMax;
    });

    if (matchingPricings.length > 0) {
        return matchingPricings.sort((a, b) => b.min_participants - a.min_participants)[0];
    }

    return null;
}

function calculateTotalPrice(adults, children, infants, pricing) {
    if (!pricing) {
        return 0;
    }

    const adultPrice = pricing.price || 0;
    const childPrice = pricing.child_price !== null && pricing.child_price !== undefined ? pricing.child_price : adultPrice;
    const infantPrice = pricing.infant_price !== null && pricing.infant_price !== undefined ? pricing.infant_price : 0;

    return adults * adultPrice + children * childPrice + infants * infantPrice;
}

function initTourPricing(config) {
    const { pricingData, hasPromo, labels } = config;

    function updatePriceDisplay() {
        const adults = parseInt(document.getElementById('adults')?.value, 10) || 1;
        const children = parseInt(document.getElementById('children')?.value, 10) || 0;
        const infants = parseInt(document.getElementById('infants')?.value, 10) || 0;
        const total = adults + children + infants;

        const pricing = findPricingForParticipants(total, pricingData);
        const priceDisplay = document.getElementById('price-display-text');
        const originalPriceDisplayEl = document.getElementById('original-price-display');
        const savingsDisplayEl = document.getElementById('savings-display');
        const priceLabel = document.getElementById('price-label');
        const consultationMessage = document.getElementById('consultation-message');

        if (!pricing) {
            return;
        }

        if (pricing.requires_consultation) {
            if (consultationMessage) {
                consultationMessage.classList.remove('hidden');
            }
            if (priceDisplay) {
                priceDisplay.textContent = labels.onRequest;
                priceDisplay.classList.add('text-orange-600');
                priceDisplay.classList.remove('text-[#333]');
            }
            if (originalPriceDisplayEl) {
                originalPriceDisplayEl.style.display = 'none';
            }
            if (savingsDisplayEl) {
                savingsDisplayEl.style.display = 'none';
            }
            if (priceLabel) {
                priceLabel.textContent = labels.priceOnRequest;
            }
            return;
        }

        if (consultationMessage) {
            consultationMessage.classList.add('hidden');
        }
        if (priceDisplay) {
            priceDisplay.classList.remove('text-orange-600');
            priceDisplay.classList.add('text-[#333]');
        }
        if (originalPriceDisplayEl) {
            originalPriceDisplayEl.style.display = '';
        }
        if (savingsDisplayEl) {
            savingsDisplayEl.style.display = '';
        }
        if (priceLabel) {
            priceLabel.textContent = labels.perPerson;
        }

        const totalPrice = calculateTotalPrice(adults, children, infants, pricing);
        const pricePerPerson = total > 0 ? totalPrice / total : pricing.price;

        const priceBreakdown = document.getElementById('price-breakdown');
        const priceBreakdownContent = document.getElementById('price-breakdown-content');
        if (priceBreakdown && priceBreakdownContent && (children > 0 || infants > 0)) {
            let breakdownHtml = '';
            if (adults > 0) {
                breakdownHtml += `<div class="flex justify-between mb-1"><span>${adults} ${labels.adults}:</span><span>${formatPrice(adults * pricing.price, config)}</span></div>`;
            }
            if (children > 0) {
                const childPrice = pricing.child_price !== null && pricing.child_price !== undefined ? pricing.child_price : pricing.price;
                breakdownHtml += `<div class="flex justify-between mb-1 text-green-700"><span>${children} ${labels.children}${pricing.child_discount_percentage ? ` (-${pricing.child_discount_percentage}%)` : ''}:</span><span>${formatPrice(children * childPrice, config)}</span></div>`;
            }
            if (infants > 0) {
                const infantPrice = pricing.infant_price !== null && pricing.infant_price !== undefined ? pricing.infant_price : 0;
                breakdownHtml += `<div class="flex justify-between mb-1 text-blue-700"><span>${infants} ${labels.babies}:</span><span>${infantPrice === 0 ? labels.free : formatPrice(infants * infantPrice, config)}</span></div>`;
            }
            breakdownHtml += `<div class="flex justify-between mt-2 pt-2 border-t border-gray-300 font-bold"><span>${labels.total}:</span><span>${formatPrice(totalPrice, config)}</span></div>`;
            priceBreakdownContent.innerHTML = breakdownHtml;
            priceBreakdown.classList.remove('hidden');
        } else if (priceBreakdown) {
            priceBreakdown.classList.add('hidden');
        }

        if (pricing.price !== null && pricing.price !== undefined && priceDisplay) {
            const formatted = formatPrice(pricePerPerson, config);
            if (formatted) {
                priceDisplay.textContent = formatted;

                if (hasPromo && pricing.original_price && originalPriceDisplayEl) {
                    const originalTotal = adults * pricing.original_price
                        + children * (pricing.child_price || pricing.original_price)
                        + infants * (pricing.infant_price || 0);
                    const originalPerPerson = total > 0 ? originalTotal / total : pricing.original_price;
                    const formattedOriginal = formatPrice(originalPerPerson, config);
                    if (formattedOriginal) {
                        originalPriceDisplayEl.textContent = formattedOriginal;
                        originalPriceDisplayEl.style.display = '';
                    }

                    if (savingsDisplayEl && originalPerPerson && pricePerPerson) {
                        const savings = originalPerPerson - pricePerPerson;
                        const formattedSavings = formatPrice(savings, config);
                        if (formattedSavings) {
                            savingsDisplayEl.innerHTML = `<i class="fas fa-piggy-bank mr-1"></i> ${labels.save} ${formattedSavings}`;
                            savingsDisplayEl.style.display = '';
                        }
                    }
                } else {
                    if (originalPriceDisplayEl) {
                        originalPriceDisplayEl.style.display = 'none';
                    }
                    if (savingsDisplayEl) {
                        savingsDisplayEl.style.display = 'none';
                    }
                }
            }
        }
    }

    function updateTravelersDisplay() {
        const adultsInput = document.getElementById('adults');
        const childrenInput = document.getElementById('children');
        const infantsInput = document.getElementById('infants');

        if (!adultsInput || !childrenInput || !infantsInput) {
            return;
        }

        const adults = parseInt(adultsInput.value, 10) || 0;
        const children = parseInt(childrenInput.value, 10) || 0;
        const infants = parseInt(infantsInput.value, 10) || 0;
        const total = adults + children + infants;

        const participantsHidden = document.getElementById('participants');
        if (participantsHidden) {
            participantsHidden.value = total;
        }

        if (pricingData.length > 0) {
            updatePriceDisplay();
        }
    }

    const adultsInput = document.getElementById('adults');
    const childrenInput = document.getElementById('children');
    const infantsInput = document.getElementById('infants');
    const participantsHidden = document.getElementById('participants');

    if (!adultsInput || !childrenInput || !infantsInput) {
        return;
    }

    adultsInput.value = 1;
    childrenInput.value = 0;
    infantsInput.value = 0;
    if (participantsHidden) {
        participantsHidden.value = 1;
    }

    updateTravelersDisplay();

    [adultsInput, childrenInput, infantsInput].forEach((input) => {
        input.addEventListener('change', updateTravelersDisplay);
    });
}

function initTourSwipers() {
    if (typeof Swiper === 'undefined') {
        return;
    }

    if (document.querySelector('.googleReviewsTourCarousel')) {
        const tourGoogleEl = document.querySelector('.googleReviewsTourCarousel');
        const tourGoogleN = tourGoogleEl ? tourGoogleEl.querySelectorAll('.swiper-slide').length : 0;
        new Swiper('.googleReviewsTourCarousel', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: false,
            watchOverflow: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: { el: '.googleReviewsTour-pagination', clickable: true },
            navigation: {
                nextEl: '.googleReviewsTour-next',
                prevEl: '.googleReviewsTour-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: Math.min(2, tourGoogleN) || 1,
                    spaceBetween: 20,
                    watchOverflow: true,
                },
                1024: {
                    slidesPerView: Math.min(2, tourGoogleN) || 1,
                    spaceBetween: 30,
                    watchOverflow: true,
                },
            },
        });
    }

    if (document.querySelector('.reviewsCarousel')) {
        new Swiper('.reviewsCarousel', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: false,
            watchOverflow: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: {
                nextEl: '.reviewsCarousel-next',
                prevEl: '.reviewsCarousel-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                    loop: false,
                    watchOverflow: true,
                },
                1024: {
                    slidesPerView: 2,
                    spaceBetween: 30,
                    loop: false,
                    watchOverflow: true,
                },
            },
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const config = readTourPageConfig();
    if (!config) {
        return;
    }

    if (config.pricingData?.length) {
        initTourPricing(config);
    }

    initTourSwipers();

    if (config.recentlyViewed) {
        trackRecentlyViewed(config.recentlyViewed);
    }
});
