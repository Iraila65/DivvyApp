(function() {
    
    const botonesModif = document.querySelectorAll('.table__accion--editar'); 
    const botones = document.querySelectorAll('.table__accion--boton');
    const usuariosId = document.querySelectorAll('.usuario-id');
    const usuario = document.querySelector('.usuario-conectado');
    const propietario = document.querySelector('.propietario-grupo');
    const urlgrupo = document.querySelector('.grupo-conectado');
    const casillas = document.querySelectorAll('.check-usuario');
    const activos = document.querySelectorAll('.check-activo');
    const pesos = document.querySelectorAll('.peso-miembro');
    const usuarioSelec = document.querySelector('.soy-yo'); 

    // Añadimos evento al botón modificar Peso
    if (botonesModif) {
        for(let i=0; i<botonesModif.length; i++ ) {
            botonesModif[i].addEventListener('click', function(e) {
                // Validar que el peso se pueda modificar
                if (pesos[i].classList.contains('no-editable')) {
                    swal.fire(
                        'El peso no se puede modificar porque ya hay movimientos',
                        '',
                        'error'
                    );
                    return;
                }

                // Validar que el peso sea numérico 
                if (validarNumeroDecimal(Number(pesos[i].textContent.replace(',', '.'))) == false) {
                    swal.fire(
                        'El peso debe ser numérico',
                        '',
                        'error'
                    );
                    return;
                }
                
                // Obtener los datos necesarios
                const miembro = {
                    id: e.target.value,
                    peso: Number(pesos[i].textContent.replace(',', '.')).toFixed(1),
                }          
                
                // Llamar a la API de Miembros para actualizar 
                actualizarPeso(miembro);
            });   
            
        }  
    }

    // Añadimos evento a los botones de eliminar Miembro y de Inactivar/Activar
    if (botones) {
        for(let i=0; i<botones.length; i++ ) {
            botones[i].addEventListener('click', function(e) {
                if (e.target.classList.contains('table__accion--eliminar')) {
                    if (usuariosId[i].value == propietario.value) {
                        swal.fire(
                            'El propietario no se puede eliminar',
                            '',
                            'error'
                        );
                    } else {
                        // Llamar a la API de Miembros para eliminar 
                        eliminarMiembro(e.target.value);
                    }
                } else if (e.target.classList.contains('table__accion--inactivar')) {
                    if (activos[i].checked == true) {
                        // Llamar a la API de Miembros para inactivar 
                        const miembro = {
                            id: this.value,
                            activo: 0
                        }    
                        actualizarActivo(miembro);
                    } else {
                        // Llamar a la API de Miembros para activar 
                        const miembro = {
                            id: this.value,
                            activo: 1
                        }  
                        actualizarActivo(miembro);
                    }
                }
            }); 
        }
    }

    // Tratamiento de las casillas de Soy Yo
    
    if (usuarioSelec) {
        if (casillas) {
            casillas.forEach(casilla => casilla.disabled = true);
        }
    } else {
        
        if (casillas) {
            casillas.forEach(casilla => {
                casilla.addEventListener('change', function(e) {
                    if (this.checked) {
                        // Se ha seleccionado un usuario
                        
                        // Deseleccionamos el anterior si lo hubiera
                        const checkAnt = document.querySelector('.soy-yo');
                        if (checkAnt) {
                            checkAnt.classList.remove('soy-yo');
                            checkAnt.checked = false;
                        } 

                        // Mostramos una alerta preguntando si está seguro,
                        // si dice que sí, actualizamos la base de datos
                        
                        Swal.fire({
                            title: '¿De verdad eres este miembro?',
                            showDenyButton: true,
                            showCancelButton: false,
                            confirmButtonText: 'Que sí',
                            denyButtonText: `No, me equivoqué`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.classList.add('soy-yo');
                                // Obtener los datos necesarios
                                const miembro = {
                                    id: this.value,
                                    soy_yo: this.checked,
                                    usuario_id: usuario.value
                                }          
                                // Llamar a la API de Miembros para actualizar 
                                actualizarSoyYo(miembro);

                            } else if (result.isDenied) {
                                this.classList.remove('soy-yo');
                                this.checked = false;
                            }
                        })
                    } else {
                        // Se ha des-seleccionado un usuario
                        this.classList.remove('soy-yo');
                    }
                });
            });
        }
    }

    async function actualizarPeso(miembro) {
        const {id, peso} = miembro;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('peso', peso);
        
        try {
            const url = '/api/miembro/actualizarPeso';
            // const url = `${location.origin}/api/miembro/actualizarPeso`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            swal.fire(
                resultado.mensaje,
                '',
                (resultado.tipo == 'exito') ? 'success' : 'error'
            ).then( () => location.href = `/miembros?url=${urlgrupo.value}`);
                        
        } catch (error) {
            console.log(error);
        }
    }

    async function actualizarSoyYo(miembro) {
        const {id, soy_yo, usuario_id} = miembro;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('soy_yo', soy_yo);
        datos.append('usuario_id', usuario_id);
        try {
            const url = '/api/miembro/actualizarSoyYo';
            // const url = `${location.origin}/api/miembro/actualizarSoyYo`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            swal.fire(
                resultado.mensaje,
                '',
                (resultado.tipo == 'exito') ? 'success' : 'error'
            ).then( () => location.href = `/miembros?url=${urlgrupo.value}`);
                        
        } catch (error) {
            console.log(error);
        }
    }

    async function eliminarMiembro(id) {
        const datos = new FormData();
        datos.append('id', id);
        datos.append('url', urlgrupo.value);
        try {
            const url = '/api/miembro/eliminar';
            // const url = `${location.origin}/api/miembro/eliminar`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            swal.fire(
                resultado.mensaje,
                '',
                (resultado.tipo == 'exito') ? 'success' : 'error'
            ).then( () => location.href = `/miembros?url=${urlgrupo.value}`);
                        
        } catch (error) {
            console.log(error);
        }
    }

    async function actualizarActivo(miembro) {
        const {id, activo} = miembro;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('activo', activo);
        
        try {
            const url = '/api/miembro/actualizarActivo';
            // const url = `${location.origin}/api/miembro/actualizarActivo`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            swal.fire(
                resultado.mensaje,
                '',
                (resultado.tipo == 'exito') ? 'success' : 'error'
            ).then( () => location.href = `/miembros?url=${urlgrupo.value}`);
                        
        } catch (error) {
            console.log(error);
        }
    }

    function validarNumeroDecimal(valor) {
        // Expresión regular que verifica si el valor es numérico decimal
        var regex = /^\d*([.,]?\d+)?$/;
        if (regex.test(valor)) {
            // El valor es un número decimal válido
            return true;
        } else {
            // El valor no es un número decimal válido
            return false;
        }
    }

})();