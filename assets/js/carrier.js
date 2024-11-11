document.addEventListener('DOMContentLoaded', function () {
    var totalPrice = parseFloat('{{ totalPrice }}') / 100;
    if (totalPrice > 49) {
    Swal.fire({title: 'Bonne nouvelle !', text: 'Votre commande dépasse 49€, profitez de la livraison gratuite, elle sera mise automatiquement. Sélectionnez n\'importe quel transporteur', icon: 'success', confirmButtonText: 'Super'});
    }
    });


