import './bootstrap.js';
import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import './styles/carousel.css';
import './js/cookieconsent.min.js';
import './styles/cookieconsent.min.css';

import Filter from './js/Filter.js';
new Filter(document.querySelector('.js-filter'))

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
    let chart;

    if (!ctx) {
        console.warn("⚠️ Aucun élément #visitsChart trouvé !");
        return;
    }

    function createChart(data) {
        // Si un graphique existe déjà, on le détruit
        if (chart) {
            chart.destroy();
        }

        // Créer un nouveau graphique avec les données
        chart = new Chart(ctx, {
            type: 'pie', // Utilisation du graphique en camembert (pie)
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }

    // Appeler createChart avec les données initiales
    createChart(visitsData);

    // Écouter les changements de filtre et mettre à jour le graphique
    document.querySelectorAll(".filters a").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            // Récupérer les nouvelles données via une requête fetch
            fetch(this.href)
                .then(response => response.text())
                .then(html => {
                    // Analyser le contenu HTML de la page renvoyée
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Trouver le script contenant les données du graphique
                    let scriptElement = Array.from(doc.scripts).find(s => s.textContent.includes("var visitsData"));

                    if (!scriptElement) {
                        console.error("❌ Aucune donnée trouvée dans le script !");
                        return;
                    }

                    // Extraire les données du script trouvé
                    let matches = scriptElement.textContent.match(/var visitsData = (\{.*\});/);

                    if (!matches || matches.length < 2) {
                        console.error("❌ Impossible d'extraire les données !");
                        return;
                    }

                    // Parser les données JSON
                    let newStats = JSON.parse(matches[1]);
                    console.log("🔄 Nouvelles données reçues :", newStats);

                    // Mettre à jour le graphique avec les nouvelles données
                    createChart(newStats);
                })
                .catch(error => console.error("❌ Erreur lors du fetch :", error));
        });
    });
});
