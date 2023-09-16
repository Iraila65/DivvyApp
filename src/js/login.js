(function() {
    const inputPass = document.querySelector('#pass');
    if (inputPass) {
        inputPass.addEventListener('input', () => {
            inputPass.type = 'text';
            setTimeout( () => {
                inputPass.type = 'password';
            }, 500);
        } )        
    }
})();