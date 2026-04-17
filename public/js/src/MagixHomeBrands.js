/**
 * Magix HomeBrands Slider
 * Initialise le diaporama Splide avec prise en charge des Tooltips Bootstrap 5
 */
class MagixHomeBrandsSlider {
    constructor(elementId) {
        this.sliderElement = document.getElementById(elementId);

        if (!this.sliderElement || typeof Splide === 'undefined') {
            return;
        }

        this.init();
    }

    init() {
        const brandsSlider = new Splide(this.sliderElement, {
            type: 'loop',
            perPage: 6,
            perMove: 1,
            gap: '2rem',
            pagination: false,
            arrows: false,
            autoplay: true,
            interval: 3000,
            pauseOnHover: true,
            drag: true,
            clones: 6,
            breakpoints: {
                1200: { perPage: 5 },
                992:  { perPage: 4 },
                768:  { perPage: 3 },
                576:  { perPage: 2 }
            }
        });

        brandsSlider.mount();

        // Initialisation des Tooltips optimisée pour le slider
        if (typeof bootstrap !== 'undefined') {
            /* * On n'utilise pas de boucle forEach. On place l'écouteur sur le parent.
             * 1. selector: cible tous les enfants (même les clones) dynamiquement.
             * 2. container: 'body' sort le tooltip du slider pour éviter d'être caché par l'overflow:hidden.
             */
            new bootstrap.Tooltip(this.sliderElement, {
                selector: '[data-bs-toggle="tooltip"]',
                container: 'body'
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new MagixHomeBrandsSlider('magix-homebrands-slider');
});