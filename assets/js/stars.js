document.addEventListener('DOMContentLoaded', () => {
    const ratingInputs = document.querySelectorAll('.star-rating');
    
    ratingInputs.forEach((starRating) => {
        const ratingInput = document.querySelector('#comment_form_rating') || document.querySelector('#ratingInput');
        
        if (starRating && ratingInput) {
            // Gestion du clic sur les étoiles
            starRating.addEventListener('click', (event) => {
                if (event.target.matches('i')) {
                    const ratingValue = event.target.getAttribute('data-rating');
                    ratingInput.value = ratingValue;

                    // Mise à jour des étoiles affichées
                    updateStarsDisplay(starRating, ratingValue);
                }
            });

            // Gestion du survol des étoiles
            starRating.addEventListener('mouseover', (event) => {
                if (event.target.matches('i')) {
                    const ratingValue = event.target.getAttribute('data-rating');
                    updateStarsDisplay(starRating, ratingValue);
                }
            });

            // Réinitialisation des étoiles lors du survol
            starRating.addEventListener('mouseout', () => {
                updateStarsDisplay(starRating, ratingInput.value);
            });
        }
    });

    // Fonction pour mettre à jour l'affichage des étoiles
    function updateStarsDisplay(starRating, value) {
        starRating.querySelectorAll('i').forEach((star) => {
            const starRatingValue = star.getAttribute('data-rating');
            if (starRatingValue <= value) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }
});
