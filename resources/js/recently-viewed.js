/**
 * Track recently viewed tours in localStorage.
 */
export function trackRecentlyViewed(tour) {
    const key = 'recently_viewed_tours';
    let stored = [];

    try {
        stored = JSON.parse(localStorage.getItem(key)) || [];
    } catch {
        stored = [];
    }

    stored = stored.filter((t) => t.id !== tour.id);

    stored.unshift({
        id: tour.id,
        title: tour.title,
        image: tour.image,
        price: tour.price,
        url: tour.url,
        viewedAt: new Date().toISOString(),
    });

    stored = stored.slice(0, 10);
    localStorage.setItem(key, JSON.stringify(stored));
}

window.trackRecentlyViewed = trackRecentlyViewed;
