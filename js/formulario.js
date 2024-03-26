document.addEventListener('DOMContentLoaded', function() {
    var inputRutPasaporte = document.getElementById('id_documento');

    inputRutPasaporte.addEventListener('blur', function() {
        var rutPasaporte = this.value;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', './php/buscarSolicitante.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status == 200) {
                var respuesta = JSON.parse(this.responseText);
                if (respuesta.encontrado) {
                    document.getElementById('id_nombres_solicitante').value = respuesta.nombres_solicitante;
                    document.getElementById('id_apellidos_solicitante').value = respuesta.apellidos_solicitante;
                    document.getElementById('id_telefono').value = respuesta.telefono;
                    document.getElementById('id_email').value = respuesta.email;
                }
            }
        };
        xhr.send('rutPasaporte=' + encodeURIComponent(rutPasaporte));
    });
});
