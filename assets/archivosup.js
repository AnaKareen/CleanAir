const express = require('express');
const multer = require('multer');
const mongoose = require('mongoose');
const fs = require('fs');

const app = express();
const upload = multer({ dest: 'uploads/' });

mongoose.connect('mongodb://localhost:27017/tu_base_de_datos', { useNewUrlParser: true, useUnifiedTopology: true });

const archivoSchema = new mongoose.Schema({
    tipo: String,
    nombre: String,
    contenido: Buffer,
});

const Archivo = mongoose.model('Archivo', archivoSchema);

app.post('/subir', upload.single('archivo'), (req, res) => {
    const nuevoArchivo = new Archivo({
        tipo: req.file.mimetype,
        nombre: req.file.originalname,
        contenido: fs.readFileSync(req.file.path),
    });

    nuevoArchivo.save()
        .then(() => {
            fs.unlinkSync(req.file.path); // Elimina el archivo temporal
            res.send('Archivo subido exitosamente');
        })
        .catch(err => {
            res.status(500).send('Error al subir el archivo: ' + err);
        });
});

app.listen(3000, () => {
    console.log('Servidor escuchando en el puerto 3000');
});