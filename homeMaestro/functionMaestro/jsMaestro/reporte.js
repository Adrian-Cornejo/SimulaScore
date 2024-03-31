function enviarImagenesAlServidor() {
    var imgData = document.getElementById('puntajeGeneralChart').toDataURL('image/png');

    fetch('../../generarPDF/pdfGrupo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ imagenGrafica: imgData })
    })
    .then(response => response.json())
    .then(data => console.log(data))
    .catch((error) => console.error('Error:', error));
}
