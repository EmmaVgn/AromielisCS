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



import * as Chart from './js/chart.umd.js';
const ctx = document.getElementById('visitsChart');
console.log("📊 Canvas détecté :", ctx);

console.log("✅ Chart.js est bien chargé :", Chart);

//Graphique
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('visitsChart');

    if (!ctx) {
        console.warn("⚠️ Aucun élément #visitsChart trouvé !");
        return;
    }

    console.log("✅ Canvas trouvé, création du graphique...");

    new Chart.Chart(ctx, {  // Ajout de `.Chart`
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai'],
            datasets: [{
                label: 'Exemple de données',
                data: [10, 20, 15, 30, 25],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    console.log("✅ Graphique créé !");
});