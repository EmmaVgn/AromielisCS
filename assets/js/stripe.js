document.addEventListener("DOMContentLoaded", function () {
    const stripe = Stripe("pk_test_51QmCfVD8VVdng5P0pjQnr1BrS6JiiP5S0RLWsDP6Fobd7udLnZAC6kktH6JIQBg3K8UBXSGVmI6q9vCj969mfrt100S3I6TlVF"); // Remplace avec ta clé publique
    const elements = stripe.elements();

    // Créer l'élément carte Stripe
    const card = elements.create("card", {
        style: {
            base: {
                fontSize: "16px",
                color: "#32325d",
                fontFamily: "Arial, sans-serif"
            }
        }
    });
    card.mount("#card-element");

    // Récupération des éléments HTML
    const form = document.getElementById("payment-form");
    const errorElement = document.getElementById("card-errors");

    // Récupération de la référence de commande (ajoute data-order-reference dans ton HTML)
    const orderReference = form.dataset.orderReference;

    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        // Création du PaymentMethod Stripe
        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: "card",
            card: card
        });

        if (error) {
            errorElement.textContent = error.message;
        } else {
            // Envoi du PaymentMethod au backend Symfony
            fetch("/process_payment", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    paymentMethodId: paymentMethod.id,
                    orderReference: orderReference
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Paiement réussi !");
                    window.location.href = "/commande/valide/" + orderReference;
                } else {
                    errorElement.textContent = data.error;
                }
            })
            .catch(error => {
                console.error("Erreur lors de la requête : ", error);
                errorElement.textContent = "Une erreur est survenue. Veuillez réessayer.";
            });
        }
    });
});
