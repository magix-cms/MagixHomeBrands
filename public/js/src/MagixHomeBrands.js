/**
 * Magix HomeBrands Slider
 * Initialise le diaporama Splide uniquement si l'élément cible existe dans le DOM.
 */
class MagixHomeBrandsSlider {
    constructor(elementId) {
        this.sliderElement = document.getElementById(elementId);

        // Sécurité : on s'arrête immédiatement si l'élément n'existe pas ou si Splide n'est pas chargé
        if (!this.sliderElement || typeof Splide === 'undefined') {
            return;
        }

        this.init();
    }

    init() {
        const brandsSlider = new Splide(this.sliderElement, {
            type: 'slide',//type: 'loop',
            perPage: 6,
            perMove: 1,
            gap: '2rem',
            pagination: false,
            arrows: true,
            autoplay: true,
            interval: 4000,
            pauseOnHover: true,
            breakpoints: {
                1200: { perPage: 5 },
                992:  { perPage: 4 },
                768:  { perPage: 3 },
                576:  { perPage: 2, arrows: false }
            }
        });

        brandsSlider.mount();

        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = this.sliderElement.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
    }
}

// Initialisation au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    new MagixHomeBrandsSlider('magix-homebrands-slider');
});