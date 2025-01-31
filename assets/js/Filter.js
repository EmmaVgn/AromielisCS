


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
        this.bindEvents()
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

    rresetForm() {
    console.log('Reset form function called');

    if (!this.form) return;

    // Réinitialiser le formulaire
    this.form.reset();
    console.log('Formulaire réinitialisé');

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

    // NE PAS réinitialiser le slider de prix
    // Laisser les prix actuels du slider sans modification
    const priceSlider = document.getElementById('price-slider');
    if (priceSlider) {
        // On ne modifie pas le slider ici pour qu'il garde ses valeurs actuelles
        console.log('Slider des prix non modifié');
    }

    console.log('Formulaire réinitialisé');

    // Recharger les produits après réinitialisation sans changer les prix
    this.loadForm();  // Recharger l'affichage de formulaire s'il y a d'autres parties dynamiques à gérer
}

    
    
    async loadAllProducts() {
        console.log('Chargement de tous les produits sans filtre');
        if (!this.content) return;
    
        this.showLoader();
    
        try {
            // Ajoutez un log pour vérifier l'URL utilisée pour la requête
            console.log('Requête URL pour AJAX:', `${window.location.href}?ajax=1`);
    
            const response = await fetch(`${window.location.href}?ajax=1`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
    
            if (!response.ok) throw new Error('Erreur lors du chargement');
    
            const data = await response.json();
            console.log('Réponse reçue', data);
    
            // Vérification si la réponse contient des produits
            if (data.content) {
                this.content.innerHTML = data.content; // Met à jour le contenu avec les produits
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
