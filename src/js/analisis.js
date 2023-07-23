(function(){
    
    const urlgrupo = document.querySelector('.grupo-conectado');
    const grafica = document.querySelector('.dashboard__grafica');
    let detallesFiltrados = [];
    const paleta = 
        [
            '#ea580c',
            '#84cc16',
            '#22d3ee',
            '#a855f7',
            '#ef4444',
            '#14b8a6',
            '#db2777',
            '#e11d48',
            '#7e22ce'
        ];

    if (grafica) {
        obtenerDatos();
        async function obtenerDatos() {
            const url = `/api/movimientos/gastos?url=${urlgrupo.value}`;
            const respuesta = await fetch(url)
            const resultado = await respuesta.json();

            const resultadoObject = Object.values(resultado);
            const totales = resultadoObject[0];
            const miembros = resultadoObject[1];
            const conceptos = resultadoObject[2];
            const fechas = resultadoObject[3];
            const detalles = resultadoObject[4];
            
            const categorias = Object.keys(totales);
            //const arraycategorias = Object.values(totales);

            const datasetsMiembros = [];
            const datasetsConceptos = [];
            const nombresMiembros = [];
            const labelFechas = [];
            const datasetsFechaM = [];
            const datasetsFechaC = [];

            
            // Cálculos de totales por concepto y totales por miembro a partir del array de detalle

            let totalPorConcepto = detalles.reduce((totalPorConcepto, elemento) => {
                const { concepto, importe } = elemento;
                totalPorConcepto[concepto] = (totalPorConcepto[concepto] || 0) + importe;
                return totalPorConcepto;
            }, {});
            const nombresConceptos = Object.keys(totalPorConcepto);
            const importesTotalConcepto = Object.values(totalPorConcepto);
           
            let totalPorMiembro = detalles.reduce((totalPorMiembro, elemento) => {
                const { nombre, importe } = elemento;
                totalPorMiembro[nombre] = (totalPorMiembro[nombre] || 0) + importe;
                return totalPorMiembro;
            }, {});
            const nombres = Object.keys(totalPorMiembro);
            const importesTotalMiembro = Object.values(totalPorMiembro);
           
            // Cálculos para los gráficos de desglose por miembro y desglose por categoría

            miembros.forEach(miembro => {
                const dataMiembro =
                {
                    label: miembro.nombre,
                    data: miembro.importes,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                };
                datasetsMiembros.push(dataMiembro);

                nombresMiembros.push(miembro.nombre);

                const dataFechaM =
                {
                    label: miembro.nombre,
                    data: miembro.importesMeses
                }
                datasetsFechaM.push(dataFechaM);
            });

            conceptos.forEach(concepto => {
                const dataConcepto =
                {
                    label: concepto.concepto,
                    data: concepto.importes,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                };
                datasetsConceptos.push(dataConcepto);

                const dataFechaC =
                {
                    label: concepto.concepto,
                    data: concepto.importesMeses
                }
                datasetsFechaC.push(dataFechaC);
            });

            fechas.forEach(fecha => {
                labelFechas.push(fecha.mes);
            })

            
            // Dibujar las gráficas

            // Gráfica de totales por categoría
            
            const ctx = document.getElementById('grafica-totales-categoria');

            const data = {
                labels: nombresConceptos,
                datasets: [{
                    label: 'cantidad gastada',
                    data: importesTotalConcepto,
                    //backgroundColor: paleta,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false
                }]
            };
            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    // scales: {
                    //     y: {
                    //         beginAtZero: true
                    //     }
                    // },
                    animation: true,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            enabled: true
                        },
                        title: {
                            display: true,
                            text: 'Totales por categoría',
                            font: {
                                size: 20
                            }
                        }
                    }
                }
            };

            new Chart(ctx, config);

            // Gráfica de totales por miembro

            const ctx2 = document.getElementById('grafica-totales-miembros');
            
            const data2 = {
                labels: nombres,
                datasets: [{
                    label: 'cantidad gastada',
                    data: importesTotalMiembro,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false
                }]
            };
            const config2 = {
                type: 'doughnut',
                data: data2,
                options: {
                    // scales: {
                    //     y: {
                    //         beginAtZero: true
                    //     }
                    // },
                    animation: true,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        // legend: {
                        //     display: false
                        // },
                        tooltip: {
                            enabled: true
                        },
                        title: {
                            display: true,
                            text: 'Totales por miembro',
                            font: {
                                size: 20
                            }
                        }
                    }
                },
            };

            new Chart(ctx2, config2);

            // Grafica de desglose por miembro

            const ctx3 = document.getElementById('grafica-desglose-miembro');
            
            const data3 = {
                labels: categorias,
                datasets: datasetsMiembros
            };
            const config3 = {
                type: 'bar',
                data: data3,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    animation: true,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            enabled: true
                        },
                        title: {
                            display: true,
                            text: 'Desglose por miembro',
                            font: {
                                size: 20
                            }
                        }
                    },
                    // layout: {
                    //     padding: {
                    //         top: 50
                    //     }
                    // }
                },
            };

            new Chart(ctx3, config3);
            
            
            // Grafica de desglose por categoria

            const ctx4 = document.getElementById('grafica-desglose-categoria');
            
            const data4 = {
                labels: nombresMiembros,
                datasets: datasetsConceptos
            };
            const config4 = {
                type: 'bar',
                data: data4,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    animation: true,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            enabled: true
                        },
                        title: {
                            display: true,
                            text: 'Desglose por categoría',
                            font: {
                                size: 20
                            }
                        }
                    }
                },
            };

            new Chart(ctx4, config4);


            // Grafica de serie mensual, gastos de miembros
             
            const ctx5 = document.getElementById('grafica-serie-mensual-miembros');
            const data5 = {
                labels: labelFechas,
                datasets: datasetsFechaM
            }   
            const config5 = {
                type: 'line',
                data: data5,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                align: 'start'
                            }
                        }
                    },
                    animations: {
                        radius: {
                            duration: 400,
                            easing: 'linear',
                            loop: (context) => context.active
                          }
                    },
                    hoverRadius: 12,
                    hoverBackgroundColor: 'yellow',
                    interaction: {
                        mode: 'nearest',
                        intersect: false,
                        axis: 'x'
                    },
                    responsive: true,
                    elements: {
                        line: {
                            tension: 0.4
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            enabled: true
                        },
                        title: {
                            display: true,
                            text: 'Serie mensual gastos de miembros',
                            font: {
                                size: 20
                            }
                        }
                    }
                }
            }

            new Chart(ctx5, config5);


            // Grafica de serie mensual, gastos por conceptos

            const ctx6 = document.getElementById('grafica-serie-mensual-categorias');
            
            const data6 = {
                labels: labelFechas,
                datasets: datasetsFechaC
            }
            const config6 = {
                type: 'line',
                data: data6,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                align: 'start'
                            }
                        }
                    },
                    animations: {
                        radius: {
                            duration: 400,
                            easing: 'linear',
                            loop: (context) => context.active
                          }
                    },
                    hoverRadius: 12,
                    hoverBackgroundColor: 'yellow',
                    interaction: {
                        mode: 'nearest',
                        intersect: false,
                        axis: 'x'
                    },
                    responsive: true,
                    elements: {
                        line: {
                            tension: 0.4
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            enabled: true
                        },
                        title: {
                            display: true,
                            text: 'Serie mensual gastos por categorías',
                            font: {
                                size: 20
                            }
                        }
                    }
                }
            }

            new Chart(ctx6, config6);

            // Tabla de detalle (pestaña 4)

            // Filtros de búsqueda

            const filtrosDetalle = document.createElement('DIV');
            filtrosDetalle.classList.add('filtros-inputs');

            const tituloFiltros = document.createElement('H2');
            tituloFiltros.textContent = "Filtros: ";
            filtrosDetalle.appendChild(tituloFiltros);

            // Filtro de Miembro
            const campoMiembro = document.createElement('DIV');
            campoMiembro.classList.add('campo');
            const labelMiembro = document.createElement('LABEL');
            labelMiembro.textContent = "Miembro: ";
            campoMiembro.appendChild(labelMiembro);
            const selectMiembro = document.createElement('SELECT');
            selectMiembro.id = "detalle_de";
            selectMiembro.classList.add("miembro__select");
            selectMiembro.innerHTML = `                     
                <select>
                    <option value="T" 'selected'> Todos </option>
                    ${miembros.map(miembro => `
                        <option value="${miembro.miembro_id}">${miembro.nombre}</option>
                        `).join('')}
                </select>  
            `;

            selectMiembro.addEventListener('change', function(e) {
                mostrarDetalles();
            });
            campoMiembro.appendChild(selectMiembro);
            filtrosDetalle.appendChild(campoMiembro);

            // Filtro de Concepto
            const campoConcepto = document.createElement('DIV');
            campoConcepto.classList.add('campo');
            const labelConcepto = document.createElement('LABEL');
            labelConcepto.textContent = "Categoría: ";
            campoConcepto.appendChild(labelConcepto);
            const selectConcepto = document.createElement('SELECT');
            selectConcepto.id = "detalle_concepto";
            selectConcepto.classList.add("concepto__select");
            selectConcepto.innerHTML = `   
                <select>
                    <option value="T" 'selected'> Todas </option>
                    ${conceptos.map(concepto => `
                        <option value="${concepto.concepto}">${concepto.concepto}</option>
                        `).join('')}
                </select>                      
            `;
            selectConcepto.addEventListener('change', function(e) {
                mostrarDetalles();
            });
            campoConcepto.appendChild(selectConcepto);
            filtrosDetalle.appendChild(campoConcepto);

            // Filtro de mes
            const campoMes = document.createElement('DIV');
            campoMes.classList.add('campo');
            const labelMes = document.createElement('LABEL');
            labelMes.textContent = "Mes: ";
            campoMes.appendChild(labelMes);
            const selectMes = document.createElement('SELECT');
            selectMes.id = "detalle_mes";
            selectMes.classList.add("mes__select");
            selectMes.innerHTML = `   
                <select>
                    <option value="T" 'selected'> Todos </option>
                    ${fechas.map(fecha => `
                        <option value="${fecha['mes']}">${fecha['mes']}</option>
                        `).join('')}
                </select>                      
            `;
            selectMes.addEventListener('change', function(e) {
                mostrarDetalles();
            });
            campoMes.appendChild(selectMes);
            filtrosDetalle.appendChild(campoMes);

            document.querySelector('#filtros-detalle').appendChild(filtrosDetalle);

            mostrarDetalles();

            function limpiarDetalles() {
                const listadoDetalles = document.querySelector('#listado-detalle');
                while (listadoDetalles.firstChild) {
                    listadoDetalles.removeChild(listadoDetalles.firstChild);
                }
            }
        
            function obtenerFiltro() {
                const detalleDe = document.querySelector('#detalle_de');
                var indicedetalleDe = detalleDe.selectedIndex;
                var miembroFiltro = detalleDe.options[indicedetalleDe];
        
                if (miembroFiltro.value == "T") {
                    detallesFiltrados = detalles;
                } else {
                    detallesFiltrados = detalles.filter(detalle => detalle.miembro == miembroFiltro.value);
                }

                const detalleConcepto = document.querySelector('#detalle_concepto');
                var indicedetalleConcepto = detalleConcepto.selectedIndex;
                var conceptoFiltro = detalleConcepto.options[indicedetalleConcepto];
        
                if (conceptoFiltro.value !== "T") {
                    detallesFiltrados = detallesFiltrados.filter(detalle => detalle.concepto == conceptoFiltro.value);
                }

                const detalleMes = document.querySelector('#detalle_mes');
                var indicedetalleMes = detalleMes.selectedIndex;
                var mesFiltro = detalleMes.options[indicedetalleMes];
        
                if (mesFiltro.value !== "T") {
                    detallesFiltrados = detallesFiltrados.filter(detalle => detalle.mes == mesFiltro.value);
                }
            }
        
            function mostrarDetalles() {
                
                // Primero tenemos que limpiar el html de los detalles anteriores para que no se dupliquen
                limpiarDetalles();
                obtenerFiltro();
        
                const contenedorDetalles = document.querySelector('#listado-detalle');
               
                if (detallesFiltrados.length == 0) {
                    const textoNoDetalles = document.createElement('P');
                    textoNoDetalles.textContent = "No hay movimientos con estos filtros";
                    textoNoDetalles.classList.add('no-txs');
                    contenedorDetalles.appendChild(textoNoDetalles);
                } else {
    
                    const tablaHtml = document.createElement('TABLE');
                    tablaHtml.classList.add('table');
                    tablaHtml.innerHTML = `
                        <thead class="table__thead">
                            <tr>
                                <th scope="col" class="table__th">Nombre</th>
                                <th scope="col" class="table__th">Concepto</th>
                                <th scope="col" class="table__th">Mes</th>
                                <th scope="col" class="table__th">Importe</th>
                            </tr>
                        </thead>
    
                        <tbody class="table__tbody">
                            ${detallesFiltrados.map(detalle => `
                                <tr class="table__tr">
                                    <td class="table__td">
                                        ${detalle.nombre}
                                    </td>
                                    <td class="table__td">
                                        ${detalle.concepto}
                                    </td>
                                    <td class="table__td">
                                        ${detalle.mes}
                                    </td>
                                    <td class="table__td">
                                        ${parseFloat(parseFloat(detalle.importe).toFixed(2)).toString()}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    `;
                    contenedorDetalles.appendChild(tablaHtml);  
                }
            }

        }

        
    }

})();