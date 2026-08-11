export function readTourPageConfig() {
    const el = document.getElementById('tour-page-config');
    if (!el) {
        return null;
    }

    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
}
