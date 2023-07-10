(function() {

    const usuario = document.querySelector('.usuario-conectado');
    const nombreUsuario = document.querySelector('.nombre-usuario');
    const propietario = document.querySelector('.propietario-grupo');
    const urlgrupo = document.querySelector('.grupo-conectado');
    
    let miembros = [];
    let conceptos = [];
    let tipos = [];
    let grupo;
    let saldos = [];
    let movimientos = [];
    let filtrados = [];
    let paraQuien = [];
    let fechaFormulario = new Date().toLocaleString('es-ES', { 
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
    let cantidadFormateada = 0;
    let fechaSeleccionada;
    let mvtosficticios = [];
    let mayorSaldo = 0;
    let menorSaldo = 0;
    let mayorSaldo_id = 0;
    let menorSaldo_id = 0;
    let cantidad_a_saldar;
    let saldosficticios = [];
    
    // Botón para añadir una nueva transacción
    const nuevaTxBtn = document.querySelector('#agregar-tx');
    if (nuevaTxBtn) {
        async function iniciarApp() {
            const resultado = await Promise.all([
                obtenerMiembros(), 
                obtenerConceptos(),
                obtenerTipos(),
                obtenerGrupo(urlgrupo.value),
                obtenerSaldos()
            ]);
            miembros = await resultado[0];
            conceptos = await resultado[1];
            tipos = await resultado[2];
            grupo = await resultado[3];
            saldos = await resultado[4];

            // Consulto los movimientos del Grupo
            obtenerMovimientos();
            
            // Añadir un evento al botón de nueva transacción
            nuevaTxBtn.addEventListener("click", function(){
                mostrarFormulario(false);
            })

            // Filtros de búsqueda
            const filtros = document.querySelectorAll('#mvtos_de');
            if (filtros) {
                filtros.forEach(filtro => {
                    filtro.addEventListener('change', function(e) {
                        filtrarMovimientos(e.target.value);
                    });
                });
            }
        }
        iniciarApp();    
    }
    
    async function obtenerMiembros() {    
        try {
            const url = `/api/miembros-activos?url=${urlgrupo.value}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            return resultado.miembros;
        } catch (error) {
            console.log(error);
        }    
    }

    async function obtenerConceptos() {    
        try {
            const url = `/api/conceptos`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            return resultado.conceptos;
        } catch (error) {
            console.log(error);
        }    
    }

    async function obtenerTipos() {    
        try {
            const url = `/api/tipos`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            return resultado.tipos;
        } catch (error) {
            console.log(error);
        }    
    }

    async function obtenerGrupo(urlgrupo) {
        try {
            const url = `/api/grupo/getGrupo?url=${urlgrupo}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            return resultado.grupo;
        } catch (error) {
            console.log(error);
        }   
    }

    async function obtenerSaldos() {    
        try {
            const url = `/api/saldos?url=${urlgrupo.value}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            return resultado.saldos;
        } catch (error) {
            console.log(error);
        }    
    }
        
    function mostrarFormulario(editar = false, movimiento = {}) {  
        
        if (editar) {
            fechaFormulario = new Date(movimiento.fecha).toLocaleString('es-ES', { 
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            let ndec = contarDecimales(movimiento.cantidad);
            cantidadFormateada = formatearImporte(movimiento.cantidad, ndec);
        } else {
            fechaFormulario = new Date().toLocaleString('es-ES', { 
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            cantidadFormateada = 0;
        };

        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        const formulario = document.createElement('FORM');
        formulario.classList.add('formulario');
        formulario.classList.add('nueva-tx');
        formulario.innerHTML = `
                <legend>${editar ? 'Editar transacción' : 'Añade una nueva transacción'}</legend>
                <div class="campo">
                    <label>Quién?</label>                        
                    <select 
                        name="miembro_id" 
                        id="miembro_id"
                        class="miembro__select"
                    >
                    <option value=""> -Seleccionar-</option>
                    ${miembros.map(miembro => `
                        <option ${editar && miembro.nombre == movimiento.miembro.nombre ? 'selected' :
                                (!editar && miembro.nombre == nombreUsuario.value) ? 'selected' : ''}
                            value="${miembro.id}"
                        >${miembro.nombre}</option>
                        `).join('')}
                    </select>  
                </div>

                <div class="campo">
                    <label>realiza un </label>                        
                    <select 
                        name="tipo_id" 
                        id="tipo_id"
                        class="tipo__select"
                    >
                    ${tipos.map(tipo => `
                        <option ${editar && tipo.nombre == movimiento.tipo_nombre ? 'selected' :
                                (!editar && tipo.nombre == "Gasto") ? 'selected' : ''}
                            value="${tipo.id}"
                        >${tipo.nombre}</option>
                        `).join('')}
                    </select>  
                </div>

                <div class="campo">
                    <label>Importe: </label>
                    <input 
                        type="number" 
                        name="cantidad" 
                        id="cantidad"
                        value="${editar ? cantidadFormateada : ''}"
                    >
                </div>

                <div class="campo">
                    <label>Concepto: </label>
                    <input 
                        type="text" 
                        name="descripcion" 
                        id="descripcion"
                        value="${editar ? movimiento.descripcion : ''}"
                    >
                </div>

                <div class="campo">
                    <label>Para quién?</label>   

                    <div class="cajas-para">
                    
                        ${miembros.map(miembro => `
                            <div class="miembro-para">
                                <input 
                                    type="checkbox" 
                                    class="for-whom ${!editar ? 'check' :
                                                    (editar && movimiento.paraquien.includes(miembro.id)) ? 'check' : ''}"
                                    name="" 
                                    value="${miembro.id}"
                                >
                                ${miembro.nombre}
                            </div> 
                            `).join('')}
                
                    </div>    
                </div>

                <div class="campo">
                    <label>Categoría:</label>                        
                    <select 
                        name="concepto_id" 
                        id="concepto_id"
                        class="concepto__select"
                    >
                    <option value=""> -Seleccionar-</option>
                    ${conceptos.map(concepto => `
                        <option ${editar && concepto.id == movimiento.concepto_id ? 'selected' : ''} 
                            value="${concepto.id}"
                        >${concepto.nombre}</option>
                        `).join('')}
                    </select>  
                </div>

                <div class="campo">
                    <label>Fecha: </label>
                    <div class="flatpickr">
                        <input 
                            class="datePicker"
                            id="datePicker"
                            name="datePicker"
                            type="text" 
                            value="${fechaFormulario}"
                            placeholder="Selecciona una fecha"
                            data-input 
                        >
                        <a class="input-button" title="toggle" data-toggle>
                            <i class="icon-calendar fa-solid fa-calendar"></i>
                        </a>

                        <a class="input-button" title="clear" data-clear>
                            <i class="icon-close fa-solid fa-rectangle-xmark"></i>
                        </a>  
                    </div>
                    
                </div>

                <div class="campo">
                    <input 
                        type="hidden" 
                        name="id" 
                        id="id"
                        value="${editar ? movimiento.id : ''}"
                    >
                </div>

                <div class="opciones">
                    <input 
                        type="submit" 
                        class="submit-nueva-tx" 
                        value="${movimiento.id ? 'Guardar cambios' : 'Añadir Transacción'}" 
                    >
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>      
        `;

        setTimeout(() => {
            formulario.classList.add('animar');
        }, 0);

        modal.appendChild(formulario);
        
        modal.addEventListener('click', function(e) {
            e.preventDefault();

            // Tratamiento del botón cancelar
            if(e.target.classList.contains('cerrar-modal')) {
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove();
                }, 500);
            }
            
            // Tratamiento de las casillas del para quién
            if (e.target.classList.contains('for-whom')) {    
                if (e.target.classList.contains('check')) {
                    e.target.classList.remove('check');
                    e.target.classList.add('nocheck');
                } else {
                    e.target.classList.remove('nocheck');
                    e.target.classList.add('check');
                }
            }

            // Tratamiento del input de fecha             
            if (e.target.classList.contains('datePicker')) {
                const fechaInput = document.querySelector(".flatpickr");
                const fp = flatpickr(fechaInput, {
                    enableTime: true,           // Permite seleccionar la hora
                    dateFormat: 'Y-m-d H:i',    // Formato de fecha y hora
                    wrap: true,                 // Permite abrir y cerrar el calendario con los iconos
                    allowInput: true,           // Permite introducir la fecha con el teclado
                    altInput: true,
                    altFormat: "Y-m-d H:i",
                    defaultDate: "today",
                    time_24hr: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        fechaSeleccionada = dateStr;
                    }
                }).toggle();
            }
            
            
            // Tratamiento del botón añadir transacción
            if (e.target.classList.contains('submit-nueva-tx')) {
                
                paraQuien = [];
                const forWhom = document.querySelectorAll('.for-whom');
                if (forWhom) {
                    forWhom.forEach(para => { 
                        if (para.classList.contains('check')) {
                            paraQuien = [...paraQuien, para.value];
                        }
                    });
                }

                // Obtener los datos necesarios
                const cant = document.querySelector('#cantidad').value; 
                const cantidad = Number(cant.replace(',', '.'));            
                
                const campoQuien = document.getElementById("miembro_id");
                var indiceQuien = campoQuien.selectedIndex;
                var miembroSeleccionado = campoQuien.options[indiceQuien];

                const campoTipo = document.getElementById("tipo_id");
                var indiceTipo = campoTipo.selectedIndex;
                var tipoSeleccionado = campoTipo.options[indiceTipo];

                const campoConcepto = document.getElementById("concepto_id");
                var indiceConcepto = campoConcepto.selectedIndex;
                var conceptoSeleccionado = campoConcepto.options[indiceConcepto];
                
                const descripcion = document.querySelector('#descripcion').value;

                if (!fechaSeleccionada || fechaSeleccionada == "") {
                    fechaSeleccionada = formatearFecha(fechaFormulario);
                }

                let movimiento = {};

                if (editar) {
                    movimiento = {
                        'id': document.querySelector('#id').value,
                        'grupo_id': grupo.id,
                        'miembro_id': miembroSeleccionado.value,
                        'cantidad': cantidad,
                        'tipo': tipoSeleccionado.value, 
                        'quien': paraQuien.toString(), 
                        'concepto_id': conceptoSeleccionado.value, 
                        'descripcion': descripcion, 
                        'fecha': fechaSeleccionada, 
                        'actualizador_id': usuario.value
                    };

                } else {
                    movimiento = {
                        'id': document.querySelector('#id').value,
                        'grupo_id': grupo.id,
                        'miembro_id': miembroSeleccionado.value,
                        'cantidad': cantidad,
                        'tipo': tipoSeleccionado.value, 
                        'quien': paraQuien.toString(), 
                        'concepto_id': conceptoSeleccionado.value, 
                        'descripcion': descripcion, 
                        'fecha': fechaSeleccionada, 
                        'creador_id': usuario.value
                    };
                }

                const validacion = validarMovimiento(movimiento);

                if (validacion) {
                    if (editar) {
                        actualizarTx(movimiento);
                    } else {
                        añadirNuevaTx(movimiento);
                    }
                }
            }
        });
        document.querySelector('.dashboard').appendChild(modal);  
    }

    function validarNumeroDecimal(valor) {
        // Expresión regular que verifica si el valor es numérico decimal
        var regex = /^\d*([.,]?\d+)?$/;

        if (valor == "") {
            return false;
        } else {
            if (regex.test(valor)) {
                // El valor es un número decimal válido
                return true;
            } else {
                // El valor no es un número decimal válido
                return false;
            }
        }
    }

    function formatearFecha(f) {
        // Obtener los componentes de la fecha y hora del string
        // f viene en formato "dd/mm/aaaa, hh:mm" el split genera una parte con el espacio después de la coma
        var partes = f.split(/[\/,: ]/); // Dividir el string en partes usando "/", ":", "," y "espacio" como separadores
        
        var dia = partes[0];
        var mes = partes[1];
        var año = partes[2];
        var hora = partes[4];
        var minutos = partes[5];

        // Crear un objeto Date con los componentes extraídos
        var fec = new Date(año, mes - 1, dia, hora, minutos);
       
        // Obtener los componentes de la fecha
        año = fec.getFullYear();
        mes = fec.getMonth() + 1; // Los meses comienzan en 0, por lo tanto se suma 1
        dia = fec.getDate();
        hora = fec.getHours();
        minutos = fec.getMinutes();

        // Formatear los componentes de la fecha, agregando los ceros a la izquierda si hacen falta
        if (mes < 10) mes = '0' + mes; 
        if (dia < 10) dia = '0' + dia; 
        if (hora < 10) hora = '0' + hora; 
        if (minutos < 10) minutos = '0' + minutos; 

        // Construir la cadena de fecha en el formato deseado
        var fechaFormateada = año + '-' + mes + '-' + dia + ' ' + hora + ':' + minutos;
        return fechaFormateada;
    }

    function mostrarAlerta(mensaje, tipo, referencia) {
        const alertaPrevia = document.querySelector('.alerta');
        if (alertaPrevia) {
            alertaPrevia.remove();
        }
        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);  
        // Eliminar la alerta después de 5 segundos
        setTimeout(() => {
            alerta.remove();
        }, 1500);
    }

    function validarMovimiento(movimiento) {
        let validacion = true;

        // Al menos debe haber un miembro en el para quién
        if (paraQuien.length == 0) {
            swal.fire(
                '¿Para quién es la transacción? al menos debes seleccionar un miembro',
                '',
                'error'
            );
            validacion = false;
        }

        // La cantidad debe ser numérica
        if (validarNumeroDecimal(movimiento.cantidad) == false || movimiento.cantidad == 0) {
            swal.fire(
                'La cantidad debe ser numérica y mayor que cero',
                '',
                'error'
            );
            validacion = false;
        }

        // Validar que si el mvto es saldar deuda en el para quien no puede estar el miembro que hace el mvto
        if (movimiento.tipo == 3 && paraQuien.includes(movimiento.miembro_id)) {
            swal.fire(
                'No se puede saldar deuda con uno mismo',
                '',
                'error'
            );
            validacion = false;
        } 

        if (movimiento.tipo == 3 && paraQuien.length > 1) {
            swal.fire(
                'Para saldar deuda debes seleccionar sólo un destinatario',
                '',
                'error'
            );
            validacion = false;
        }

        // La descripcion del movimiento es obligatorio
        if (movimiento.descripcion == "") {
            swal.fire(
                'El concepto es obligatorio',
                '',
                'error'
            );
            validacion = false;
        }

        return validacion;
    }

    async function añadirNuevaTx(movimiento) {
        actualizarSaldos(movimiento)
            .then(saldarDeudas())
            .then(actualizarTablasAlta(movimiento))
            .then(() => {
                mostrarAlerta(
                    "Movimiento agregado correctamente", 
                    "exito",
                    document.querySelector('.formulario legend')
                );
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                    mostrarMovimientos(); 
                }, 1500);
            })
            .catch(() => {
                // Ha habido un error al actualizar los datos
                mostrarAlerta(
                    "Ha habido un error en la actualización de tablas", 
                    "error",
                    document.querySelector('.formulario legend')
                );
            });
    }

    async function eliminarTx(movimiento) {
        movimiento.cantidad = movimiento.cantidad * (-1);
        actualizarSaldos(movimiento)
            .then(saldarDeudas())
            .then(actualizarTablasBaja(movimiento))
            .then(() => {
                Swal.fire("Movimiento eliminado correctamente");
                mostrarMovimientos();
            })
            .catch(() => {
                // Ha habido un error al actualizar los datos
                Swal.fire("Error", "Ha habido un error en la actualización de tablas");
            });
    }

    async function actualizarTx(movimiento) {
        const {id} = movimiento;
        // Busco el movimiento anterior y actualizo saldos anulando el movimiento
        // Hago una copia profunda para que no actualice el movimiento en memoria
        // Luego actualizo saldos con el nuevo importe
        let movimientoAnterior = JSON.parse(JSON.stringify(encontrarMovimiento(id)));
        movimientoAnterior.cantidad = movimientoAnterior.cantidad * (-1);
        actualizarSaldos(movimientoAnterior)
            .then(actualizarSaldos(movimiento))
            .then(saldarDeudas())
            .then(actualizarTablasMod(movimiento))
            .then(() => {
                mostrarAlerta(
                    "Movimiento modificado correctamente", 
                    "exito",
                    document.querySelector('.formulario legend')
                );
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                    mostrarMovimientos(); 
                }, 1500);
            })
            .catch(() => {
                // Ha habido un error al actualizar los datos
                mostrarAlerta(
                    "Ha habido un error en la actualización de tablas", 
                    "error",
                    document.querySelector('.formulario legend')
                );
            });
    }

    async function actualizarSaldos(movimiento, editar=false) {
        const {miembro_id, cantidad, tipo, quien} = movimiento;
        const paraquien = quien.split(",");
        let importemvto = cantidad;
        //console.log(paraquien);
        
        // Si el movimiento es ingresos, multiplicamos la cantidad por -1
        if (tipo == 2) {
            importemvto = importemvto * (-1);
        }

        // Sumamos la cantidad a ingresos del miembro que hace el movimiento
        saldos.forEach(regsaldo => {
            if (regsaldo.miembro_id == miembro_id) {
                regsaldo.ingresos = Number(regsaldo.ingresos) + importemvto;
                regsaldo.saldo = Number(regsaldo.saldo) + importemvto;
            }
        })

        // Si el tipo es gasto o ingreso
        // -- se suma a gastos la cantidad * peso
        if (tipo == 1 || tipo == 2) {
            
            // Calculamos el total de los pesos
            let pesoTotal = 0;
            miembros.forEach(miembro => {
                // si el miembro está incluido en el para quien, su peso suma
                if (paraquien.includes(miembro.id)) {
                    pesoTotal += Number(miembro.peso);
                }
            })
            
            // A cada miembro del para quien, le calculamos su parte de gasto 
            saldos.forEach(saldo => {
                if ( paraquien.includes(saldo.miembro_id) ) {
                    // buscar el peso de este miembro
                    let peso = 0;
                    miembros.forEach(miembro => {
                        // si el miembro coincide con el miembro que hace el movimiento extraemos su peso
                        if (miembro.id == saldo.miembro_id) {
                            peso = Number(miembro.peso);
                        }
                    });
                    saldo.gastos = Number(saldo.gastos) + importemvto * peso / pesoTotal;
                    saldo.saldo = Number(saldo.saldo) - importemvto * peso / pesoTotal;
                }
            })
        }

        // Si el tipo es transferencia
        // -- se resta cantidad de ingresos del miembro que haya en "para quien" 
        if (tipo == 3) {
            saldos.forEach(regsaldo => {
                if (paraquien.includes(regsaldo.miembro_id) ) {
                    regsaldo.ingresos = Number(regsaldo.ingresos) - importemvto;
                    regsaldo.saldo = Number(regsaldo.saldo) - importemvto;
                }
            })
        }
    }

    function saldarDeudas() {  
        // Generar movimientos ficticios de saldar deuda hasta que todos los saldos sean cero

        // Identificamos si hay algún miembro con saldo distinto de cero
        mvtosficticios = [];
        mayorSaldo = 0;
        menorSaldo = 0;
        mayorSaldo_id = 0;
        menorSaldo_id = 0;

        // Necesito una copia "profunda" del array porque contiene objetos
        saldosficticios = JSON.parse(JSON.stringify(saldos));
        
        let haySaldos = comprobarSaldo(saldosficticios);

        let movimientofic;
        let i = 0;
        while (haySaldos && i<100) {
            // generar movimiento saldar deuda
            if (Math.abs(mayorSaldo) > Math.abs(menorSaldo)) {
                cantidad_a_saldar = Math.abs(menorSaldo);
            } else {
                cantidad_a_saldar = Math.abs(mayorSaldo);
            }

            if (cantidad_a_saldar > 0.000001 && menorSaldo_id !=0 && mayorSaldo_id != 0) {
                // genero movimiento desde menorSaldo_id a mayorSaldo_id
                movimientofic = {
                    'miembro_id': menorSaldo_id,
                    'cantidad': cantidad_a_saldar,
                    'tipo': 3,
                    'quien': mayorSaldo_id.toString()
                }
                saldosficticios = actualizarSaldosFicticios(saldosficticios, movimientofic);
                mvtosficticios.push(movimientofic);
            }
            
            i++;
            mayorSaldo = 0;
            menorSaldo = 0;
            mayorSaldo_id = 0;
            menorSaldo_id = 0;
            haySaldos = comprobarSaldo(saldosficticios);
            //imprimirDatos(mayorSaldo, mayorSaldo_id, menorSaldo, menorSaldo_id);
        }

        if (haySaldos) {
            console.log("Error de programación, saldar deudas ha entrado en un bucle");
            return false;
        } else {
            return true;
        }
    }

    function actualizarSaldosFicticios(saldosficticios, movimiento) {
        const {miembro_id, cantidad, tipo, quien} = movimiento;
        const paraquien = quien.split(",");
        let importemvto = cantidad;

        // Sumamos la cantidad a ingresos del miembro que hace el movimiento
        saldosficticios = saldosficticios.map(regsaldo => {
            if (regsaldo.miembro_id == miembro_id) {
                regsaldo.ingresos = Number(regsaldo.ingresos) + importemvto;
                regsaldo.saldo = Number(regsaldo.saldo) + importemvto;
            }
            return regsaldo;
        });

        // Aquí el tipo es siempre transferencia
        // -- se resta cantidad de ingresos del miembro que haya en "para quien" 
        if (tipo == 3) {
            saldosficticios = saldosficticios.map(regsaldo => {
                if (paraquien.includes(regsaldo.miembro_id)) {
                    regsaldo.ingresos = Number(regsaldo.ingresos) - importemvto;
                    regsaldo.saldo = Number(regsaldo.saldo) - importemvto;
                }
                return regsaldo;
            });
        }
        
        return saldosficticios;
    }

    async function actualizarTablasAlta(mvto) {    
        // Grabar movimiento
        grabarMovimiento(mvto).then(() => {
            // Eliminar las deudas actuales del grupo 
            const {grupo_id} = mvto;
            eliminarDeudas(grupo_id).then(() => {
                // Grabar los movimientos ficticios como las deudas actuales
                mvtosficticios.forEach(mvtofic => {
                    grabarDeuda(mvtofic);
                });
                // Se actualiza la tabla de saldos de todos los registros
                saldos.forEach(regsaldo => {
                    actualizarRegistroSaldo(regsaldo);
                });
            });
        });
    }

    async function actualizarTablasBaja(mvto) {    
        // Eliminar movimiento
        eliminarMovimiento(mvto, false).then(() => {
            // Eliminar las deudas actuales del grupo 
            const {grupo_id} = mvto;
            eliminarDeudas(grupo_id).then(() => {
                // Grabar los movimientos ficticios como las deudas actuales
                mvtosficticios.forEach(mvtofic => {
                    grabarDeuda(mvtofic);
                });
                // Se actualiza la tabla de saldos de todos los registros
                saldos.forEach(regsaldo => {
                    actualizarRegistroSaldo(regsaldo);
                });
            });
        });
    }

    async function actualizarTablasMod(mvto) {
        // Actualizar movimiento
        actualizarMovimiento(mvto).then(() => {
            // Eliminar las deudas actuales del grupo 
            const {grupo_id} = mvto;
            eliminarDeudas(grupo_id).then(() => {
                // Grabar los movimientos ficticios como las deudas actuales
                mvtosficticios.forEach(mvtofic => {
                    grabarDeuda(mvtofic);
                });
                // Se actualiza la tabla de saldos de todos los registros
                saldos.forEach(regsaldo => {
                    actualizarRegistroSaldo(regsaldo);
                });
            });
        });

    }

    async function grabarMovimiento(mvto) {
        const {grupo_id, miembro_id, cantidad, tipo, quien, concepto_id, descripcion, fecha, creador_id} = mvto;
        const datos = new FormData();
        datos.append('grupo_id', grupo_id);
        datos.append('miembro_id', miembro_id);
        datos.append('cantidad', cantidad);
        datos.append('tipo', tipo);
        datos.append('quien', quien);    
        datos.append('concepto_id', concepto_id);    
        datos.append('descripcion', descripcion);    
        datos.append('fecha', fecha);    
        datos.append('creador_id', creador_id);    
        
        try {
            const url = '/api/movimiento/crear';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            if (resultado.tipo == 'exito') {
                // Agregar el nuevo movimiento al listado global de movimientos
                movimientos = [...movimientos, resultado.movimiento];
            }
        } catch (error) {
            console.log(error);
        }
    }

    async function actualizarMovimiento(mvto) {
        const {id, grupo_id, miembro_id, cantidad, tipo, quien, concepto_id, descripcion, fecha, actualizador_id} = mvto;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('grupo_id', grupo_id);
        datos.append('miembro_id', miembro_id);
        datos.append('cantidad', cantidad);
        datos.append('tipo', tipo);
        datos.append('quien', quien);    
        datos.append('concepto_id', concepto_id);    
        datos.append('descripcion', descripcion);    
        datos.append('fecha', fecha);    
        datos.append('actualizador_id', actualizador_id);   

        try {
            const url = '/api/movimiento/actualizar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
                        
            if (resultado.tipo == 'exito') { 
                // Actualizar el movimiento de la lista en memoria
                movimientos = movimientos.map(mvto => {
                    if(mvto.id == id) {
                        mvto.miembro_id = resultado.movimiento.miembro_id;
                        mvto.cantidad = resultado.movimiento.cantidad;
                        mvto.tipo = resultado.movimiento.tipo;
                        mvto.quien = resultado.movimiento.quien;
                        mvto.concepto_id = resultado.movimiento.concepto_id;
                        mvto.descripcion = resultado.movimiento.descripcion;
                        mvto.fecha = resultado.movimiento.fecha;
                        mvto.actualizador_id = resultado.movimiento.actualizador_id;
                        mvto.miembro = resultado.movimiento.miembro;
                        mvto.tipo_nombre = resultado.movimiento.tipo_nombre;
                        mvto.paraquien = resultado.movimiento.paraquien;
                        mvto.fecha_date = resultado.movimiento.fecha_date;
                        mvto.fecha_hora = resultado.movimiento.fecha_hora;
                    }
                    return mvto;
                });
            }
        } catch (error) {
            console.log(error);
        }
    }

    async function eliminarDeudas(grupo_id) {
        const datos = new FormData();
        datos.append('grupo_id', grupo_id);
        
        try {
            const url = '/api/deuda/eliminar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
        } catch (error) {
            console.log(error);
        }
    }

    async function grabarDeuda(mvtofic) {
        const {miembro_id, cantidad, quien} = mvtofic;
        
        const datos = new FormData();
        datos.append('grupo_id', grupo.id);
        datos.append('from_miembro_id', miembro_id);
        datos.append('to_miembro_id', quien);
        datos.append('importe', cantidad);
            
        try {
            const url = '/api/deuda/crear';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();          
        } catch (error) {
            console.log(error);
        }
    }

    async function actualizarRegistroSaldo(regsaldo) {
        const {id, grupo_id, miembro_id, gastos, ingresos, saldo} = regsaldo;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('grupo_id', grupo.id);
        datos.append('miembro_id', miembro_id);
        datos.append('gastos', gastos);
        datos.append('ingresos', ingresos);
        datos.append('saldo', saldo);
            
        try {
            const url = '/api/saldo/actualizar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();          
        } catch (error) {
            console.log(error);
        }
    }

    async function obtenerMovimientos() {
        try {
            const url = `/api/movimientos?url=${urlgrupo.value}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            movimientos = resultado.movimientos;
            mostrarMovimientos();
        } catch (error) {
            console.log(error);
        }
    }

    function mostrarMovimientos(filtro = "") {
        // Primero tenemos que limpiar el html de los movimientos anteriores para que no se dupliquen
        limpiarMovimientos();

        if (filtro == "") {
            obtenerFiltro();
        }

        const contenedorMovimientos = document.querySelector('#listado-txs');
        if (filtrados.length == 0) {
            const textoNoMovimientos = document.createElement('LI');
            textoNoMovimientos.textContent = "No hay movimientos";
            textoNoMovimientos.classList.add('no-txs');
            contenedorMovimientos.appendChild(textoNoMovimientos);
        } else {

            filtrados.forEach(movimiento => {
                const lineaMovimiento = document.createElement('LI');
                lineaMovimiento.dataset.movtoId = movimiento.id;
                lineaMovimiento.classList.add('tx');

                // Fulanito realizó un ingreso de cantidad por concepto el día x
                const mvto = document.createElement('P');
                let importeFormateado = formatearImporte(movimiento.cantidad, 2);
                
                mvto.textContent = `
                    ${movimiento.miembro.nombre} 
                    realizó un ${movimiento.tipo_nombre} 
                    de ${importeFormateado} 
                    por ${movimiento.descripcion}
                    el dia ${movimiento.fecha_date}
                    a las ${movimiento.fecha_hora}
                `;

                const opcionesDiv = document.createElement('DIV');
                opcionesDiv.classList.add('opciones');

                const btnEditar = document.createElement('BUTTON');
                btnEditar.classList.add('editar-mvto');
                btnEditar.dataset.movtoId = movimiento.id;
                btnEditar.textContent = "Editar";
                btnEditar.onclick = function() {
                    mostrarFormulario(true, {...movimiento});
                }

                const btnEliminar = document.createElement('BUTTON');
                btnEliminar.classList.add('eliminar-mvto');
                btnEliminar.dataset.movtoId = movimiento.id;
                btnEliminar.textContent = "Eliminar";
                btnEliminar.onclick = function() {
                    confirmarEliminarMvto({...movimiento});   // le paso una copia en memoria del objeto movimiento
                }

                opcionesDiv.appendChild(btnEditar);
                opcionesDiv.appendChild(btnEliminar);
                lineaMovimiento.appendChild(mvto);
                lineaMovimiento.appendChild(opcionesDiv);
                contenedorMovimientos.appendChild(lineaMovimiento);
            });
        }
    }

    function confirmarEliminarMvto(movimiento) {
        Swal.fire({
            title: '¿Eliminar movimiento?',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarTx(movimiento);
            } else {
                Swal.fire('No se ha eliminado el movimiento', '', 'info')
            }
        })
    }

    function limpiarMovimientos() {
        const listadoMovimientos = document.querySelector('#listado-txs');
        while (listadoMovimientos.firstChild) {
            listadoMovimientos.removeChild(listadoMovimientos.firstChild);
        }
    }

    function obtenerFiltro() {
        const mvtosDe = document.getElementById("mvtos_de");
        var indicemvtosDe = mvtosDe.selectedIndex;
        var miembroFiltro = mvtosDe.options[indicemvtosDe];

        if (miembroFiltro.value == "T") {
            filtrados = movimientos;
        } else {
            filtrados = movimientos.filter(movimiento => movimiento.miembro_id == miembroFiltro.value);
        }
    }

    function filtrarMovimientos(filtro) {
        if (filtro !== "T") {
            filtrados = movimientos.filter(movimiento => movimiento.miembro_id == filtro);
        } else {
            filtrados = movimientos;
        }
        mostrarMovimientos(miembros, tipos, conceptos, grupo, filtro);
    }

    async function eliminarMovimiento(movimiento, comunicar=true) {
        const {id} = movimiento;
        const datos = new FormData();
        datos.append('id', id);
        try {
            const url = '/api/movimiento/eliminar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            if (comunicar) Swal.fire('Eliminado', resultado.mensaje, 'success');
            if (resultado.tipo == 'exito') { 
                // Borrar el movimiento en memoria
                movimientos = movimientos.filter(mvto => mvto.id !== id );
                mostrarMovimientos();
            }
        } catch (error) {
            console.log(error);
        }
    }

    function comprobarSaldo(sald) {
        let s = false;
        sald.forEach(saldo => {
            if (Number(saldo.saldo) != 0) {
                s = true;
                if (Number(saldo.saldo) > mayorSaldo) {
                    mayorSaldo = Number(saldo.saldo);
                    mayorSaldo_id = saldo.miembro_id;
                }
                if (Number(saldo.saldo) < menorSaldo) {
                    menorSaldo = Number(saldo.saldo);
                    menorSaldo_id = saldo.miembro_id;
                }
            }   
        })
        return s;
    }

    function imprimirDatos(mayorSaldo, mayorSaldo_id, menorSaldo, menorSaldo_id) {
        console.log("Mayor saldo");
        console.log("Id: ");
        console.log(mayorSaldo_id);
        console.log("Saldo: ");
        console.log(mayorSaldo);
        console.log("Menor saldo");
        console.log("Id: ");
        console.log(menorSaldo_id);
        console.log("Saldo: ");
        console.log(menorSaldo);
    }

    function formatearImporte(imp, ndec) {
        return parseFloat(parseFloat(imp).toFixed(ndec)).toString();
    }

    // Función para encontrar un movimiento por su ID
    function encontrarMovimiento(id) {
        let movimientoEncontrado = movimientos.find(function(movimiento) {
            return movimiento.id === id;
        });
    
        return movimientoEncontrado;
    }

    function contarDecimales(numero) {
        const cadenaNumero = numero.toString(); // Convertir el número a una cadena de texto
        const partes = cadenaNumero.split('.'); // Dividir la cadena en partes usando el punto decimal como separador
        
        // Si no hay parte decimal, el número no tiene decimales significativos
        if (partes.length === 1 || partes[1] === '0') {
          return 0;
        }
        
        // Eliminar los ceros finales para obtener el número de decimales significativos
        const decimales = partes[1].replace(/0+$/, '');
        
        return decimales.length;
    }

})();




