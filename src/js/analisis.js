(function(){
    
    const urlgrupo = document.querySelector('.grupo-conectado');
    const grafica = document.querySelector('.dashboard__grafica');
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
            
            const categorias = Object.keys(totales);
            const arraycategorias = Object.values(totales);

            const totalesCategoria = [];
            const datasetsMiembros = [];
            const datasetsConceptos = [];
            const nombresMiembros = [];
            const totalesMiembros = [];

            arraycategorias.forEach(categoria => {
                const miembroscategoria = Object.values(categoria);
                let importeCat = 0;
                miembroscategoria.forEach(miembro => {
                    importeCat += miembro.importe;
                });
                totalesCategoria.push(importeCat);
            });

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

                let totalMiembro = miembro.importes.reduce( function(total, importe) {
                    return total + importe
                }, 0)
                totalesMiembros.push(totalMiembro);
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
            });

            // Gráfica de totales por categoría
            
            const ctx = document.getElementById('grafica-totales-categoria');

            const data = {
                labels: categorias,
                datasets: [{
                    label: 'cantidad gastada',
                    data: totalesCategoria,
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
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    animation: true,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
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
                labels: nombresMiembros,
                datasets: [{
                    label: 'cantidad gastada',
                    data: totalesMiembros,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false
                }]
            };
            const config2 = {
                type: 'doughnut',
                data: data2,
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
                            display: false
                        },
                        // legend: {
                        //     position: 'top',
                        //     labels: {
                        //         font: {
                        //             size: 14
                        //         }
                        //     }
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
            
        }

    }

})();