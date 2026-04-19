require('dotenv').config();
const express = require('express');
const multer = require('multer');
const bodyParser = require('body-parser');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const cors = require('cors');
const https = require('https');

const app = express();
const port = 3005;

const sslOptions = {
    key: fs.readFileSync(path.resolve(__dirname, 'server.key')), // Path to private key
    cert: fs.readFileSync(path.resolve(__dirname, 'server.cert')) // Path to certificate
};

// Load API key from environment variables
const APIKEY = process.env.CLOUDCONVERT_APIKEY;

// Middleware
app.use(bodyParser.json());
app.use(express.static('public')); // Serve static files for frontend

// Allow CORS from specific origin
const corsOptions = {
    origin: '*', // Replace with the exact origin or '*' for all origins
    methods: ['GET', 'POST'], // Specify allowed methods
    allowedHeaders: ['Content-Type', 'Authorization'], // Specify allowed headers
};

app.use(cors(corsOptions));

// Multer for file uploads
const upload = multer({ dest: 'uploads/' });

// Generate a unique filename
const generateUniqueFilename = (ext) => {
    const uniqueId = crypto.randomBytes(8).toString('hex');
    return `${'tmp'}-${uniqueId}${ext}`;
};

// Endpoint to handle file upload and conversion
app.post('/convert', upload.single('file'), async (req, res) => {
    const file = req.file;

    if (!file) {
        return res.status(400).send('No file uploaded.');
    }

    const fileExt = path.extname(file.originalname).toLowerCase();
    const uniqueInputName = generateUniqueFilename(fileExt);
    const inputPath = path.join('uploads', uniqueInputName);

    // Rename uploaded file for unique identification
    fs.renameSync(file.path, inputPath);

    const uniqueOutputBase = path.basename(uniqueInputName, fileExt);
    const dxfPath = path.join('converted', `${uniqueOutputBase}.dxf`);
    const svgPath = path.join('converted', `${uniqueOutputBase}.svg`);

    // Ensure output directory exists
    if (!fs.existsSync('converted')) {
        fs.mkdirSync('converted');
    }

    const cleanupFiles = () => {
        // Clean up all temporary files
        [inputPath, dxfPath, svgPath].forEach((filePath) => {
            if (fs.existsSync(filePath)) {
                fs.unlinkSync(filePath);
            }
        });
    };

    const processFile = () => {
        // Step 2: Convert DXF to SVG using Inkscape CLI
        const inkscapeCmd = `inkscape "${fileExt === '.dxf' ? inputPath : dxfPath}" --export-filename="${svgPath}" --export-type=svg --export-margin=0`;
        exec(inkscapeCmd, (inkErr, inkStdout, inkStderr) => {
            if (inkErr) {
                console.error(`Inkscape error: ${inkStderr}`);
                cleanupFiles();
                return res.status(500).send('Failed to convert to SVG.');
            }

            console.log(`Conversion to SVG complete: ${inkStdout}`);

            // Read the SVG content and send it to the frontend
            fs.readFile(svgPath, 'utf8', (readErr, data) => {
                cleanupFiles();

                if (readErr) {
                    console.error(`Read SVG error: ${readErr}`);
                    return res.status(500).send('Failed to read SVG file.');
                }
                
                res.send({ svgContent: data });
            });
        });
    };

    if (fileExt === '.dxf') {
        // If the file is DXF, directly convert to SVG
        processFile();
    } else if (fileExt === '.dwg') {
        // Step 1: Convert DWG to DXF using CloudConvert CLI
        const cmd = `cloudconvert convert -f dxf --outputdir converted "${inputPath}" --apikey ${APIKEY}`;
        exec(cmd, (err, stdout, stderr) => {
            if (err) {
                console.error(`CloudConvert error: ${stderr}`);
                cleanupFiles();
                return res.status(500).send('Failed to convert DWG to DXF.');
            }

            console.log(`DWG to DXF conversion complete: ${stdout}`);
            processFile(); // Continue to convert DXF to SVG
        });
    } else {
        // Unsupported file type
        cleanupFiles();
        res.status(400).send('Unsupported file type. Please upload a DWG or DXF file.');
    }
});

// Serve a basic frontend
app.get('/', (req, res) => {
    res.send(`
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>DWG/DXF to SVG Converter</title>
        </head>
        <body>
            <h1>DWG/DXF to SVG Converter</h1>
            <form id="uploadForm" enctype="multipart/form-data">
                <input type="file" name="file" id="fileInput" accept=".dwg,.dxf" required />
                <button type="submit">Convert</button>
            </form>
            <h2>Converted SVG</h2>
            <div id="svgContainer"></div>

            <script>
                const form = document.getElementById('uploadForm');
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const fileInput = document.getElementById('fileInput');
                    const file = fileInput.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);

                    try {
                        const response = await fetch('/convert', {
                            method: 'POST',
                            body: formData
                        });

                        if (!response.ok) {
                            throw new Error('Conversion failed.');
                        }

                        const result = await response.json();
                        const svgContainer = document.getElementById('svgContainer');
                        svgContainer.innerHTML = result.svgContent; // Display the SVG
                    } catch (err) {
                        console.error(err);
                        alert('Failed to convert the file.');
                    }
                });
            </script>
        </body>
        </html>
    `);
});

// Start the server
https.createServer(sslOptions, app).listen(port, () => {
    console.log(`Server running at https://localhost:${port}`);
});
