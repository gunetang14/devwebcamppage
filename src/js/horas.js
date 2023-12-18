(function(){ // IIFI Function
    const URLEditar = '/admin/eventos/editar';
    const horas = document.querySelector('#horas');
    if(horas){
        
        const categoria = document.querySelector('[name="categoria_id"]')
        const dias = document.querySelectorAll('[name="dia"]');
        const inputHiddenDia = document.querySelector('[name="dia_id"]');
        const inputHiddenHora = document.querySelector('[name="hora_id"]');
        const URLActual = window.location.href;
        const inputDiaInicial = inputHiddenDia.value;
        const inputHoraInicial = inputHiddenHora.value;
        const inputCategInicial = categoria.value;
        

        categoria.addEventListener('change', terminoBusqueda);
        dias.forEach( dia => dia.addEventListener('change',terminoBusqueda))

        let busqueda = {
            categoria_id: +categoria.value || '',
            dia: +inputHiddenDia.value || '',
            
        }

        if(!Object.values(busqueda).includes('')) {
            // se coloca un async-await ya que el add-seleccionada no funciona porque buscarEventos cuando limpia vuelve a colocar deshabilitada
            //manera 1
                        
            async function esperarAwait() {
                await buscarEventos();
                //habilita hora que viene de la base de datos
                habilitaHoraInicial();
            }
            esperarAwait();
            //manera 2
            //  ( async () => {
            //    await buscarEventos();
            //    const id = inputHiddenHora.value;
            //    const horaSelecionada = document.querySelector(`[data-hora-id="${id}"]`);
            //    horaSelecionada.classList.remove('horas__hora--deshabilitada');
            //    horaSelecionada.classList.add('horas__hora--seleccionada');
            //})()
        }
        function habilitaHoraInicial(){
            const id = inputHoraInicial;
            const horaInicial = document.querySelector(`[data-hora-id="${id}"]`);
            horaInicial.classList.remove('horas__hora--deshabilitada');
            horaInicial.classList.add('horas__hora--seleccionada');
            horaInicial.onclick = seleccionarHora;
        }
        function terminoBusqueda(e){
            busqueda[e.target.name] = e.target.value;
            
            //reniciar los campos ocultos y el selector de horas
            if(!URLActual.includes(URLEditar)){
                inputHiddenHora.value = '';
                inputHiddenDia.value = '';
            }
            
            const horaPrevia = document.querySelector('.horas__hora--seleccionada');
            
            if(horaPrevia ){
                horaPrevia.classList.remove('horas__hora--seleccionada');
            }
                                 
            if(Object.values(busqueda).includes('')){
                return;
            }
            buscarEventos();
        }
        async function buscarEventos(){
            const {dia , categoria_id} = busqueda;
            const url = `/api/eventos-horario?dia_id=${dia}&categoria_id=${categoria_id}`;
            const resultado = await fetch(url);
            const eventos = await resultado.json();
            obtenerHorasDisponibles(eventos);
        }
        function obtenerHorasDisponibles(eventos){
            //reiniciar las horas
            const listadoHoras = document.querySelectorAll('#horas li');
            listadoHoras.forEach( li => {
                li.classList.add('horas__hora--deshabilitada');
            });
            //comprobrar eventos ya tomados y quitar la variable de deshabilitado
            const horasTomadas = eventos.map( evento => evento.hora_id);
            const listadoHorasArray = Array.from(listadoHoras); // se cambia de un NodeList a un Array porque sino el filter no funciona
            // const resultado = listadoHoras.filter( li => horasTomadas.includes(li.dataset.horaId)); // asi no sirve porque filter es un array y listadoHoras es un NodeList
            const resultado = listadoHorasArray.filter( li => !horasTomadas.includes(li.dataset.horaId));
            
            resultado.forEach(li => li.classList.remove('horas__hora--deshabilitada'));
            
            if(URLActual.includes(URLEditar)){
                if(busqueda.categoria_id == inputCategInicial && busqueda.dia == inputDiaInicial){
                    habilitaHoraInicial();
                }
            }                     
            
            const horasDisponibles = document.querySelectorAll('#horas li:not(.horas__hora--deshabilitada)');
            horasDisponibles.forEach(hora => hora.addEventListener('click',seleccionarHora));
            const horasDeshabilitadas = document.querySelectorAll('.horas__hora--deshabilitada');
            horasDeshabilitadas.forEach(hora => hora.removeEventListener('click', seleccionarHora));
            
        }
        function seleccionarHora(e){
            //deshabilitar la hora previa si hay un nuevo click
            const horaPrevia = document.querySelector('.horas__hora--seleccionada');
            if(horaPrevia){
                horaPrevia.classList.remove('horas__hora--seleccionada');
                horaPrevia.classList.remove('horas__hora--deshabilitada');
            }
            //agregar clase de seleccionado
            e.target.classList.add('horas__hora--seleccionada');
            //llenar campo oculto de hora
            inputHiddenHora.value = e.target.dataset.horaId;
            //llenar campo oculto de dia
            inputHiddenDia.value = document.querySelector('[name="dia"]:checked').value;
            
            
        }
    }
})();