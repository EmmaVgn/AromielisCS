import './bootstrap.js';
import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import './styles/carousel.css';
import './js/cookieconsent.min.js';
import './styles/cookieconsent.min.css';


import Filter from './js/Filter.js';
new Filter(document.querySelector('.js-filter'))



/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

//Scroll to top
document.addEventListener("DOMContentLoaded", function () {
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");
  
    scrollToTopBtn.addEventListener("click", function () {
      console.log("Bouton cliqué !");
      window.scrollTo({
        top: 0,
        behavior: "smooth", // Défilement fluide
      });
    });
  });
  

//Cookie
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (
        value || ""
    ) + expires + "; path=/; SameSite=None; Secure";
}
window.addEventListener('load', function () {
    console.log('Initialisation de CookieConsent');
    if (window.cookieconsent) {
        window.cookieconsent.initialise({
            "palette": {
                "popup": {
                    "background": "#000"
                },
                "button": {
                    "background": "#f1d600"
                }
            },
            "theme": "classic",
            "position": "bottom",
            "content": {
                "message": "Nous utilisons des cookies pour améliorer votre expérience sur notre site.",
                "dismiss": "Accepter",
                "link": "En savoir plus",
                "href": "/politique-de-confidentialite"
            },
            onStatusChange: function (status) {
                setCookie('cookieconsent_status', status, 365);
            }
        });
    } else {
        console.error('CookieConsent library not loaded.');
    }
});

//graphique stats
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('visitsChart');

    if (!ctx) {
        console.warn("⚠️ Aucun élément #visitsChart trouvé !");
        return;
    }

    console.log("📊 Données utilisées pour le graphique :", visitsData);

    new Chart(ctx, {
        type: 'bar',
        data: visitsData,
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    console.log("✅ Graphique mis à jour avec les vraies données !");
});

