const ratingInput = document.querySelector('#comment_form_rating');
if (ratingInput) {
    const starRating = document.querySelector('.star-rating');
    starRating.addEventListener('click', function (event) {
        if (event.target.matches('i')) {
            const ratingValue = event.target.getAttribute('data-rating');
            ratingInput.value = ratingValue;
            // Remove 'far' class and add 'fas' class for selected stars
            starRating.querySelectorAll('i').forEach(function (star) {
                const starRatingValue = star.getAttribute('data-rating')
                if (starRatingValue <= ratingValue) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            })
        }
    })
}

document.addEventListener('DOMContentLoaded', () => {
    const ratingInput = document.querySelector('#ratingInput');
    const starRating = document.querySelector('.star-rating');
    if (starRating && ratingInput) {
    starRating.addEventListener('click', function (event) {
    if (event.target.matches('i')) {
    const ratingValue = event.target.getAttribute('data-rating');
    ratingInput.value = ratingValue;
    // Mise à jour des étoiles affichées
    starRating.querySelectorAll('i').forEach(function (star) {
    const starRatingValue = star.getAttribute('data-rating');
    if (starRatingValue <= ratingValue) {
    star.classList.remove('far');
    star.classList.add('fas');
    } else {
    star.classList.remove('fas');
    star.classList.add('far');
    }
    });
    }
    });
    // Met à jour l'affichage des étoiles lors du survol
    starRating.addEventListener('mouseover', function (event) {
    if (event.target.matches('i')) {
    const ratingValue = event.target.getAttribute('data-rating');
    starRating.querySelectorAll('i').forEach(function (star) {
    const starRatingValue = star.getAttribute('data-rating');
    if (starRatingValue <= ratingValue) {
    star.classList.add('fas');
    star.classList.remove('far');
    } else {
    star.classList.add('far');
    star.classList.remove('fas');
    }
    });
    }
    });
    // Réinitialise les étoiles lors du survol
    starRating.addEventListener('mouseout', function () {
    const currentRating = ratingInput.value;
    starRating.querySelectorAll('i').forEach(function (star) {
    const starRatingValue = star.getAttribute('data-rating');
    if (starRatingValue <= currentRating) {
    star.classList.add('fas');
    star.classList.remove('far');
    } else {
    star.classList.add('far');
    star.classList.remove('fas');
    }
    });
    });
    }
    });
    document.addEventListener('DOMContentLoaded', () => {
    const reviewsCarousel = document.getElementById('reviewsCarousel');
    if (reviewsCarousel) {
    new bootstrap.Carousel(reviewsCarousel, {
    interval: 5000, // Temps entre chaque slide (en millisecondes)
    wrap: true // Permet de faire des boucles infinies
    });
    }
    });