import     'nouislider.js';
import 'nouislider.css';


/**
 * @property {HTMLElement|null} pagination
 * @property {HTMLElement|null} content
 * @property {HTMLElement|null} sorting
 * @property {HTMLFormElement|null} form
 * @property {HTMLElement|null} reset
 */
console.log("Initialisation du filtre");

export default class Filter {
    constructor(element) {
        if (element === null) {
            console.warn('Element principal du filtre non trouvé');
            return;
        }
        console.log('Construction du filtre');
        this.pagination = element.querySelector('.js-filter-pagination') || null;
        this.content = element.querySelector('.js-filter-content') || null;
        this.sorting = element.querySelector('.js-filter-sorting') || null;
        this.form = element.querySelector('.js-filter-form') || null;
        this.reset = element.querySelector('#resetBtn') || null;

        if (this.form) {
            this.bindEvents();
        } else {
            console.warn('Formulaire non trouvé');
        }
    }

    bindEvents() {
        const clickHandler = e => {
            const target = e.target.closest('a');
            if (target && target.tagName === 'A') {
                e.preventDefault();
                this.loadUrl(target.getAttribute('href'));
            }
        };

        if (this.sorting) this.sorting.addEventListener('click', clickHandler);
        if (this.pagination) this.pagination.addEventListener('click', clickHandler);

        if (this.form) {
            this.form.addEventListener('change', (e) => {
                if (e.target.matches('.js-filter-checkbox, input, select')) {
                    this.loadForm();
                }
            });

            const searchInput = this.form.querySelector('#q');
            if (searchInput) {
                searchInput.addEventListener('input', this.loadForm.bind(this));
            }
        }

        if (this.reset) {
            this.reset.addEventListener('click', (e) => {
                e.preventDefault();
                this.resetForm();
            });
        }
    }

    resetForm() {
        console.log('Reset form function called');
        
        if (!this.form) return;
    
        // Réinitialiser le formulaire
        this.form.reset();
    
        // Réinitialiser les cases à cocher des catégories
        const categoryCheckboxes = this.form.querySelectorAll('.category-checkbox');
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = false;  // Décocher toutes les catégories
        });
    
        // Réinitialiser tous les autres champs (input, select, etc.)
        const allInputs = this.form.querySelectorAll('input, select');
        allInputs.forEach(input => {
            if (input.type === 'checkbox') {
                input.checked = false;
            } else if (input.type === 'radio') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });
    
        // Réinitialiser le slider de prix
        const priceSlider = document.getElementById('price-slider');
        if (priceSlider) {
            const min = parseInt(priceSlider.dataset.min, 10);
            const max = parseInt(priceSlider.dataset.max, 10);
            
            // Réinitialiser les valeurs des inputs de prix en divisant par 100
            const minPriceInput = this.form.querySelector('input[name="minPrice"]');
            const maxPriceInput = this.form.querySelector('input[name="maxPrice"]');
            
            if (minPriceInput) minPriceInput.value = (min / 100).toFixed(2);
            if (maxPriceInput) maxPriceInput.value = (max / 100).toFixed(2);
    
            // Réinitialiser les valeurs du slider avec les valeurs min et max
            if (window.jQuery) {
                $(priceSlider).slider('values', [min, max]);
            }
        }
    
        // Recharger le formulaire après réinitialisation
        this.loadAllProducts();  // Recharger tous les produits sans les filtres
        this.loadForm();  // Recharger l'affichage de formulaire s'il y a d'autres parties dynamiques à gérer
    }

    async loadAllProducts() {
        if (!this.content) return;
    
        this.showLoader();
    
        try {
            const response = await fetch(`${window.location.href}?ajax=1`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
    
            if (!response.ok) throw new Error('Erreur lors du chargement');
    
            const data = await response.json();
    
            // Vérification si la réponse contient des produits
            if (data.content) {
                this.content.innerHTML = data.content; // Remplacer le contenu avec les produits
            }
    
            // Mettre à jour le tri et la pagination si disponible
            if (this.sorting && data.sorting) {
                this.sorting.innerHTML = data.sorting;
            }
    
            if (this.pagination && data.pagination) {
                this.pagination.innerHTML = data.pagination;
            }
    
            const totalItemsElement = document.getElementById('totalItems');
            if (totalItemsElement) {
                totalItemsElement.innerText = `${data.totalItems} résultat(s)`;
            }
        } catch (error) {
            console.error('Erreur AJAX:', error);
            this.content.innerHTML = '<p>Erreur de chargement.</p>';
        } finally {
            this.hideLoader();
        }
    }
    



    async loadForm() {
        if (!this.form) return;

        const data = new FormData(this.form);
        const url = new URL(this.form.getAttribute('action') || window.location.href);
        const params = new URLSearchParams(data);

        return this.loadUrl(`${url.pathname}?${params}`);
    }

    async loadUrl(url) {
        if (!this.content) return;
        this.showLoader();
    
        try {
            const response = await fetch(`${url}&ajax=1`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
    
            if (!response.ok) throw new Error('Erreur lors du chargement');
    
            const data = await response.json();
            this.flipContent(data.content);

            if (this.sorting && data.sorting) this.sorting.innerHTML = data.sorting;
            if (this.pagination && data.pagination) this.pagination.innerHTML = data.pagination;

            const totalItemsElement = document.getElementById('totalItems');
            if (totalItemsElement) {
                totalItemsElement.innerText = `${data.totalItems} résultat(s)`;
            }
        } catch (error) {
            console.error('Erreur AJAX:', error);
            this.content.innerHTML = '<p>Erreur de chargement.</p>';
        } finally {
            this.hideLoader();
        }
    }

    showLoader() {
        if (!this.form) return;
        this.form.classList.add('is-loading');
        const loader = this.form.querySelector('.js-loading');
        if (loader) loader.style.display = 'block';
    }

    hideLoader() {
        if (!this.form) return;
        this.form.classList.remove('is-loading');
        const loader = this.form.querySelector('.js-loading');
        if (loader) loader.style.display = 'none';
    }

    flipContent(content) {
        if (!this.content) return;
        this.content.innerHTML = content;
    }
}


new Filter(document.querySelector('.js-filter'))
// Initialisation du slider
document.addEventListener('DOMContentLoaded', () => {
    const priceSlider = document.getElementById('price-slider');

    if (priceSlider) {
        const min = document.getElementById('minPrice');
        const max = document.getElementById('maxPrice');
        const minValue = Math.floor(parseInt(priceSlider.dataset.min, 10) / 100);
        const maxValue = Math.ceil(parseInt(priceSlider.dataset.max, 10) / 100);
        const range = noUiSlider.create(priceSlider, {
            start: [min.value || minValue, max.value || maxValue],
            connect: true,
            step: 2,
            range: {
                'min': minValue,
                'max': maxValue
            }
        });
        range.on('slide', function (values, handle) {
            if (handle === 0) {
                min.value = Math.round(values[0])
            }
            if (handle === 1) {
                max.value = Math.round(values[1])
            }
        })
        range.on('end', function (values, handle) {
            if (handle === 0) {
                min.dispatchEvent(new Event('change'))
            } else {
                max.dispatchEvent(new Event('change'))
            }
        })
    }
});

