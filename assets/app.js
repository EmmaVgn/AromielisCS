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

    let chart = new Chart(ctx, {
        type: 'pie',
        data: visitsData,
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

    function attachFilterEvents() {
        document.querySelectorAll(".filters a").forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault();  

                console.log("🔍 Bouton cliqué :", this.href);

                fetch(this.href)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        let scriptElement = Array.from(doc.scripts).find(s => s.textContent.includes("var visitsData"));

                        if (!scriptElement) {
                            console.error("❌ Aucune donnée trouvée dans le script !");
                            return;
                        }

                        let matches = scriptElement.textContent.match(/var visitsData = (\{.*\});/);

                        if (!matches || matches.length < 2) {
                            console.error("❌ Impossible d'extraire les données !");
                            return;
                        }

                        let newStats = JSON.parse(matches[1]);
                        console.log("🔄 Nouvelles données reçues :", newStats);

                        if (!newStats.labels || newStats.labels.length === 0) {
                            console.warn("⚠️ Les nouvelles données sont vides !");
                            return;
                        }

                        // 🔥 Supprimer l'ancien graphique avant de recréer un nouveau
                        chart.destroy();

                        chart = new Chart(ctx, {
                            type: 'pie',
                            data: newStats,
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

                        console.log("✅ Nouveau graphique mis à jour !");
                    })
                    .catch(error => console.error("❌ Erreur lors du fetch :", error));
            });
        });
    }

    attachFilterEvents();
    console.log("✅ Graphique initialisé !");
});

