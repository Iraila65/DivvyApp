import flatpickr from "flatpickr";
(function() {
    document.addEventListener("DOMContentLoaded", function() {
        const ventanaModal = document.getElementsByClassName('modal');
        if (ventanaModal) {
            setTimeout(function() {
                flatpickr('#datePicker', {
                    enableTime: true,  // Permite seleccionar la hora
                    dateFormat: 'Y-m-d H:i',  // Formato de fecha y hora
                    // EN ESTA PARTE ES DONDE SE REGISTRA EL EVENTO
                    onChange: function(selectedDates, dateStr, instance) {
                        console.log(dateStr)
                    }
                });
                
                console.log("he inicializado el datepicker");
            }, 0);
        }
    });
    
    // const fp = flatpickr('#datePicker', {
    //     enableTime: true,  // Permite seleccionar la hora
    //     dateFormat: 'Y-m-d H:i',  // Formato de fecha y hora
    //     "onChange": [function(){
    //             // extract the week number
    //             // note: "this" is bound to the flatpickr instance
    //             const diaSeleccionado = this.selectedDates[0] ? this.selectedDates[0] : null;
    //             console.log(diaSeleccionado);
    //     }]
    // }).open();

})();
