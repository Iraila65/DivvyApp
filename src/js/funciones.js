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

function formatearImporte(imp, ndec) {
    return parseFloat(parseFloat(imp).toFixed(ndec)).toString();
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

async function obtenerMiembros(urlgrupo) {    
    try {
        const url = `/api/miembros-activos?url=${urlgrupo}`;
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

async function obtenerSaldos(urlgrupo) {    
    try {
        const url = `/api/saldos?url=${urlgrupo}`;
        const respuesta = await fetch(url);
        const resultado = await respuesta.json();
        return resultado.saldos;
    } catch (error) {
        console.log(error);
    }    
}

function imprimirDatos(haySaldos, mayorSaldo, mayorSaldo_id, menorSaldo, menorSaldo_id) {
    console.log("Hay saldo: ", haySaldos);
    console.log("Mayor saldo");
    console.log("Id: ", mayorSaldo_id);
    console.log("Saldo: ", mayorSaldo);
    console.log("Menor saldo");
    console.log("Id: ", menorSaldo_id);
    console.log("Saldo: ", menorSaldo);
}

export {
    contarDecimales,
    formatearImporte,
    validarNumeroDecimal,
    formatearFecha, 
    mostrarAlerta,
    obtenerMiembros,
    obtenerConceptos,
    obtenerTipos,
    obtenerGrupo,
    obtenerSaldos,
    imprimirDatos
}