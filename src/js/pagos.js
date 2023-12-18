
    import { loadScript } from "@paypal/paypal-js";
    let paypal;
    const urlActual = window.location.href;
    const urlNecesaria = `${process.env.HOST}/finalizar-registro`

    if( urlActual == urlNecesaria){
      try {
          paypal = await loadScript({ clientId: `${process.env.PP_CI}` });
      } catch (error) {
          console.error("failed to load the PayPal JS SDK script", error);
      }
    
    
      function initPayPalButton() {
          paypal.Buttons({
            style: {
              shape: 'rect',
              color: 'blue', 
              layout: 'vertical',
              label: 'pay',
            },
    
            createOrder: function(data, actions) {
              return actions.order.create({
                purchase_units: [{"description":"1","amount":{"currency_code":"USD","value":199}}]
              });
            },
    
            onApprove: function(data, actions) {
              return actions.order.capture().then(function(orderData) {
    
                // Full available details
                //console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
    
                // Show a success message within this page, e.g.
                //const element = document.getElementById('paypal-button-container');
                //element.innerHTML = '';
                //element.innerHTML = '<h3>Thank you for your payment!</h3>';
    
                // Or go to another URL:  actions.redirect('thank_you.html');
                const datos = new FormData();
                datos.append('paquete_id', orderData.purchase_units[0].description);
                datos.append('pago_id', orderData.purchase_units[0].payments.captures[0].id);
                fetch('/finalizar-registro/pagar', {
                    method: 'POST',
                    body: datos
                }).then( respuesta => respuesta.json())
                .then( resultado =>{
                    if(resultado.resultado){
                        actions.redirect(`${process.env.HOST}/finalizar-registro/conferencias`);
                    }
                })
              });
            },
    
            onError: function(err) {
              console.log(err);
            }
          }).render('#paypal-button-container');
    
          //pase virtual
          paypal.Buttons({
            style: {
              shape: 'rect',
              color: 'blue',
              layout: 'vertical',
              label: 'pay',
            },
    
            createOrder: function(data, actions) {
              return actions.order.create({
                purchase_units: [{"description":"2","amount":{"currency_code":"USD","value":49}}]
              });
            },
    
            onApprove: function(data, actions) {
              return actions.order.capture().then(function(orderData) {
    
                // Full available details
                //console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
    
                // Show a success message within this page, e.g.
                //const element = document.getElementById('paypal-button-container');
                //element.innerHTML = '';
                //element.innerHTML = '<h3>Thank you for your payment!</h3>';
    
                // Or go to another URL:  actions.redirect('thank_you.html');
                const datos = new FormData();
                datos.append('paquete_id', orderData.purchase_units[0].description);
                datos.append('pago_id', orderData.purchase_units[0].payments.captures[0].id);
                fetch('/finalizar-registro/pagar', {
                    method: 'POST',
                    body: datos
                }).then( respuesta => respuesta.json())
                .then( resultado =>{
                    if(resultado.resultado){ 
                        actions.redirect(`${process.env.HOST}/finalizar-registro/conferencias`);
                    }
                })
                
              });
            },
    
            onError: function(err) {
              console.log(err);
            }
          }).render('#paypal-button-container-virtual');
    
        }
    
      initPayPalButton();
    }
