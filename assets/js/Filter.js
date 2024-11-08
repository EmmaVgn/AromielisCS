console.log("Initialisation du filtre");

/**
 * @property {HTMLElement|null} pagination
 * @property {HTMLElement|null} content
 * @property {HTMLElement|null} sorting
 * @property {HTMLFormElement|null} form
 * @property {HTMLElement|null} reset
 */
export default class Filter {
    /**
     * @param {HTMLElement|null} element 
     */
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

        // Initialiser les événements si le formulaire est présent
        if (this.form) {
            this.bindEvents();
        } else {
            console.warn('Formulaire non trouvé');
        }
    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents() {
        const aClickListener = e => {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                this.loadUrl(e.target.getAttribute('href'));
            }
        };

        // Ajout des écouteurs uniquement si les éléments existent
        if (this.sorting) {
            this.sorting.addEventListener('click', aClickListener);
        }

        if (this.pagination) {
            this.pagination.addEventListener('click', aClickListener);
        }

        if (this.form) {
            const inputs = this.form.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('change', this.loadForm.bind(this));
            });

            const searchInput = this.form.querySelector('#q');
            if (searchInput) {
                searchInput.addEventListener('keyup', this.loadForm.bind(this));
            }
        }

        if (this.reset) {
            this.reset.addEventListener('click', this.resetForm.bind(this));
        }
    }
/**
 * Ajoute les comportements aux différents éléments
 */
bindEvents() {
    const aClickListener = e => {
        if (e.target.tagName === 'A') {
            e.preventDefault();
            this.loadUrl(e.target.getAttribute('href'));
        }
    };

    // Écoute des clics pour le tri
    if (this.sorting) {
        this.sorting.addEventListener('click', aClickListener);
    }

    // Écoute des clics pour la pagination
    if (this.pagination) {
        this.pagination.addEventListener('click', aClickListener);
    }

    // Écoute des changements dans le formulaire
    if (this.form) {
        const inputs = this.form.querySelectorAll('.js-filter-checkbox');
        
        // Ajout d'écouteurs pour chaque input
        inputs.forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this));
        });

        // Écoute des touches dans le champ de recherche
        const searchInput = this.form.querySelector('#q');
        if (searchInput) {
            searchInput.addEventListener('keyup', this.loadForm.bind(this));
        }
    }

    // Réinitialisation du formulaire
    if (this.reset) {
        this.reset.addEventListener('click', this.resetForm.bind(this));
    }
}

    /**
     * Soumet le formulaire via AJAX
     */
    async loadForm() {
        if (!this.form) return;

        const data = new FormData(this.form);
        const url = new URL(this.form.getAttribute('action') || window.location.href);
        const params = new URLSearchParams();

        data.forEach((value, key) => {
            params.append(key, value);
        });

        return this.loadUrl(url.pathname + '?' + params.toString());
    }

    /**
     * Charge une URL via AJAX
     */
    async loadUrl(url) {
        if (!this.content) return;
        this.showLoader();

        const params = new URLSearchParams(url.split('?')[1] || '');
        params.set('ajax', 1);
        
        try {
            const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.flipContent(data.content);

                // Mise à jour du tri et de la pagination
                if (this.sorting && data.sorting) {
                    this.sorting.innerHTML = data.sorting;
                }

                if (this.pagination && data.pagination) {
                    this.pagination.innerHTML = data.pagination;
                }

                // Mettre à jour le nombre total d'éléments
                const totalItemsElement = document.getElementById('totalItems');
                if (totalItemsElement) {
                    totalItemsElement.innerText = `${data.totalItems} résultat(s)`;
                }

                // Mettre à jour l'URL sans le paramètre 'ajax'
                params.delete('ajax');
                history.replaceState({}, '', url.split('?')[0] + '?' + params.toString());
            } else {
                console.error('Erreur lors du chargement des résultats');
                this.content.innerHTML = '<p>Une erreur est survenue lors du chargement des résultats.</p>';
            }
        } catch (error) {
            console.error('Erreur AJAX:', error);
            this.content.innerHTML = '<p>Une erreur est survenue lors du chargement des résultats.</p>';
        }

        this.hideLoader();
    }

    /**
     * Réinitialise le formulaire
     */
    resetForm() {
        if (this.form) {
            this.form.reset();
            this.loadForm();
        }
    }

    /**
     * Affiche le loader
     */
    showLoader() {
        if (!this.form) return;
        this.form.classList.add('is-loading');
        const loader = this.form.querySelector('.js-loading');
        if (loader) {
            loader.setAttribute('aria-hidden', 'false');
            loader.style.display = null;
        }
    }

    /**
     * Cache le loader
     */
    hideLoader() {
        if (!this.form) return;
        this.form.classList.remove('is-loading');
        const loader = this.form.querySelector('.js-loading');
        if (loader) {
            loader.setAttribute('aria-hidden', 'true');
            loader.style.display = 'none';
        }
    }

    /**
     * Anime le remplacement du contenu
     * @param {string} content
     */
    flipContent(content) {
        if (!this.content) return;

        const flipper = new Flipper({
            element: this.content
        });

        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                flipId: element.id,
                shouldFlip: false,
                onExit: el => el.style.opacity = 0
            });
        });

        flipper.recordBeforeUpdate();
        this.content.innerHTML = content;

        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                flipId: element.id,
                onAppear: el => el.style.opacity = 1
            });
        });

        flipper.update();
    }
}


