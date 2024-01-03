class PaypalPayment {
  constructor() {
    this.init();
  }
  init() {
    const self = this; // Ajout de cette ligne pour conserver la référence à l'instance

    paypal
      .Buttons({
        style: {
          layout: "vertical",
          color: "black",
          shape: "rect",
          label: "paypal",
          disableMaxWidth: true,
        },
        createOrder: function (data, actions) {
          return actions.order.create({
            purchase_units: [
              {
                amount: {
                  value: totalPrice,
                },
              },
            ],
          });
        },
        onApprove: function (data, actions) {
          return actions.order.capture().then(function (details) {
            // Rediriger l'utilisateur vers l'URL souhaitée après la capture du paiement
            // alert(`Okidoki`);
            self.redirectToSuccessPage(); // Appel de la méthode pour rediriger
          });
        },
      })
      .render("#paypal-button-container");
  }

  // Méthode pour rediriger l'utilisateur vers l'URL de succès
  redirectToSuccessPage() {
    // Remplacez l'URL ci-dessous par l'URL de votre choix
    window.location.href =
      "http://corvaxxarea.com/aveemana/index.php?route=proceedOrder";
  }
}

export default PaypalPayment;
