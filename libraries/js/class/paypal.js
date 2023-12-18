// paypal
//   .Buttons({
//     createOrder: function (data, actions) {
//       return actions.order.create({
//         purchase_units: [
//           {
//             amount: {
//               value: totalPrice,
//             },
//           },
//         ],
//       });
//     },
//     onApprove: function (data, actions) {
//       return actions.order.capture().then(function (details) {});
//     },
//   })
//   .render("#paypal-button-container");
