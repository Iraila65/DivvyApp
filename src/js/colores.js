(function() {
    // alert("Hola mundo");
  
    // Escuchamos el evento 'click' en cada círculo
    const circles = document.querySelectorAll('.circle');
    const colorSelec = document.querySelector('.color-seleccionado');
    
    //console.log(circles);
    if (circles) {
        circles.forEach(circle => {
            circle.addEventListener('click', function(e) {
                const colorAnt = document.querySelector('.color-marcado');
                if (colorAnt) {
                    colorAnt.classList.remove('color-marcado');
                }
                colorSelec.value = e.target.id;
                circle.classList.add('color-marcado');
            });
        });
    }

})();