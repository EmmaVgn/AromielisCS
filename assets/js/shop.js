document.addEventListener('DOMContentLoaded', function() {
    const incrementBtn = document.getElementById('incrementBtn');
    const decrementBtn = document.getElementById('decrementBtn');
    const quantityInput = document.getElementById('quantityInput');
    const form = document.getElementById('addToCartForm');

    if (incrementBtn && decrementBtn && quantityInput && form) {
        // Fonction pour incrémenter la quantité
        incrementBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value, 10);
            const maxValue = parseInt(quantityInput.max, 10);

            // S'assurer que la quantité ne dépasse pas le stock
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });

        // Fonction pour décrémenter la quantité
        decrementBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value, 10);

            // S'assurer que la quantité ne soit pas inférieure à 1
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

        // Vérifier la quantité avant l'envoi du formulaire
        form.addEventListener('submit', function(e) {
            const quantity = parseInt(quantityInput.value, 10);

            // Validation de la quantité
            if (quantity < 1) {
                e.preventDefault();  // Empêche l'envoi si la quantité est inférieure à 1
                alert('La quantité doit être supérieure ou égale à 1');
                return;
            }
        });
    } else {
        console.warn('Certains éléments du DOM sont manquants :', { incrementBtn, decrementBtn, quantityInput, form });
    }
});
