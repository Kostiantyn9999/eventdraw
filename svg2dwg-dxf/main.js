const express = require('express');
const busboy = require('connect-busboy');
const path = require('path');
const fs = require('fs-extra');
var exec = require('child_process').exec;
var cors = require('cors')

const PORT = 3200;
const APIKEY = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOGJiZDViZmM1M2RlNWE4ZmI2MmEzNmU2MGQ1NmI5YzhlOWQ0OWU5ZmQ3ZjE1ZjU0ZWIxN2IxZmNjN2JhNWU3ZGI3Yzc0M2RlYzk4YzgxYTQiLCJpYXQiOjE3MjMxMjk1NjAuNzczMTk4LCJuYmYiOjE3MjMxMjk1NjAuNzczMTk5LCJleHAiOjQ4Nzg4MDMxNjAuNzY5MTIsInN1YiI6IjY0MDg1OTE3Iiwic2NvcGVzIjpbInVzZXIucmVhZCIsInVzZXIud3JpdGUiLCJ0YXNrLnJlYWQiLCJ0YXNrLndyaXRlIiwid2ViaG9vay5yZWFkIiwid2ViaG9vay53cml0ZSIsInByZXNldC5yZWFkIiwicHJlc2V0LndyaXRlIl19.Z-3P2rNMBsYCnBSvvBrfp1q5-KUkxO0lkvrogk44ghT5usGmIb5vTp4tAviJRWzsn1syzT0CxIJrMYuQmsHQj0RF2Ic7_jlOthMxJKa7jMuqhye_IXKiiYYPwzHpiP08EeL8bWkNvk-JVbzciqhnn7HjPvfPTjgPMokyQGFN_shmRLue9yHJkluUtcIFimYKyRr5aW8ez5j5E9TaIdXPlQE6-R0lgHUkUVNdJUI68NjQBJcgQ10F_DkJH1XO1IADWt4Q9zORaxNWOIsyoIgCzRxqdX4iq-YFYAnQ9g1coUlvtmLBoVtpOlUMkd4H4e5ZzdBT6NrAFjYVLndtaojeMpwxBH-pim_yavzby5qIEQ0n-Fr5edt_IL6VxN2zJ6wMt8sphBbIO50J0z7YU_0Vyt2POPL5BHW2V-wNnXrPU2Xeul0bKKsCrJV9udrh0Kv_7-212yGLufKWxziHZueA-g4vFWlCxb_WAMomItlYPxxUEjN0RsVUJ5Ee1piJxQAdwlH9XxXwty8IQVcex5xsKA4F3PDCi5D1cJwdPXtBXrK87xs9RzPk1CI5yCAY2D8Ys2pt-095hXPD-x3Q9mcXRmdYeJYBGPhb_VvYw7feoX2aL_ZCW_gVx8YKOYeiYP7N5eFUcIgoDYSeTZBEZVuCk3i7P7qMpR_Qy_CzE6NNJAw';
const UPLOADLIMIT = 2 * 1024 * 1024 * 1204;

const app = express();
app.use(express.static('files'))
app.use(cors())
app.use(busboy({
    highWaterMark: UPLOADLIMIT,
}));

const uploadPath = path.join(__dirname, 'files/');
fs.ensureDir(uploadPath);

const download = async (req, res, callback) => {
    try {
        await fs.remove(uploadPath);
        fs.ensureDir(uploadPath);
        req.pipe(req.busboy);

        req.busboy.on('file', (fieldname, file, filename) => {
            const filePath = path.join(uploadPath, filename);
            const onlyName = path.parse(filePath).name;
            const fstream = fs.createWriteStream(filePath);
            file.pipe(fstream);

            fstream.on('close', () => {
                callback(onlyName, filePath);
            });
        });
    } catch (err) {
        console.error(err);
    }
}

app.route('/svg2dxf').post((req, res, next) => {
    download(req, res, function (onlyName, filePath) {
        const dxfFile = path.join(uploadPath, `${onlyName}.dxf`);

        const cmd = `cloudconvert convert -f dxf --outputdir files "${filePath}" --apikey ${APIKEY}`;
        exec(cmd, function (error, stdout, stderr) {
            if (error) {
                res.redirect('back');
                return;
            }
            res.download(dxfFile);
        });
    });
});

app.route('/svg2dwg').post((req, res, next) => {
    download(req, res, function (onlyName, filePath) {
        const dxfFile = path.join(uploadPath, `${onlyName}.dxf`);
        const cmd = `cloudconvert convert -f dxf --outputdir files "${filePath}" --apikey ${APIKEY}`;
        exec(cmd, function (error, stdout, stderr) {
            if (error) {
                res.redirect('back');
                return;
            }
            const dwgFile = path.join(uploadPath, `${onlyName}.dwg`);
            const cmd = `cloudconvert convert -f dwg --outputdir files "${dxfFile}" --apikey ${APIKEY}`;
            exec(cmd, function (error, stdout, stderr) {
                if (error) {
                    res.redirect('back');
                    return;
                }
                res.download(dwgFile);
            });
        });
    });
});

app.route('/dxf2svg').post((req, res, next) => {
    download(req, res, function (onlyName, filePath) {
        const svgFile = path.join(uploadPath, `${onlyName}.svg`);
        const cmd = `inkscape ${filePath}.dxf -o ${svgFile}`;
        exec(cmd, function (error, stdout, stderr) {
            if (error) {
                res.redirect('back');
                return;
            }
            res.send(dxfFile);
        });
    });
});

app.route('/dwg2svg').post((req, res, next) => {
    download(req, res, function (onlyName, filePath) {
        const dwgFile = path.join(uploadPath, `${onlyName}.dwg`);
        const cmd = `cloudconvert convert -f dxf --outputdir files "${dwgFile}" --apikey ${APIKEY}`;
        exec(cmd, function (error, stdout, stderr) {
            if (error) {
                res.redirect('back');
                return;
            }
            const dxfFile = path.join(uploadPath, `${onlyName}.dxf`);
            const svgFile = path.join(uploadPath, `${onlyName}.svg`);
            const cmd = `inkscape ${dxfFile} -o ${svgFile}`;
            exec(cmd, function (error, stdout, stderr) {
                if (error) {
                    res.redirect('back');
                    return;
                }
                res.download(svgFile);
            });
        });
    });
});

app.route('/svg2dxf_ajax').post((req, res, next) => {
    download(req, res, function (onlyName, filePath) {
        const dxfFile = path.join(uploadPath, `${onlyName}.dxf`);

        const cmd = `cloudconvert convert -f dxf --outputdir files "${filePath}" --apikey ${APIKEY}`;
        exec(cmd, function (error, stdout, stderr) {
            if (error) {
                res.redirect('back');
                return;
            }
            fs.readFile(dxfFile, "utf8", function (err, data) {
                if (err) throw err;
                res.download({ fileName: `${onlyName}.dxf` });
            });
        });
    });
});

app.route('/svg2dwg_ajax').post((req, res, next) => {
    download(req, res, function (onlyName, filePath) {
        const dxfFile = path.join(uploadPath, `${onlyName}.dxf`);
        const cmd = `cloudconvert convert -f dxf --outputdir files "${filePath}" --apikey ${APIKEY}`;
        exec(cmd, function (error, stdout, stderr) {
            if (error) {
                res.redirect('back');
                return;
            }
            const dwgFile = path.join(uploadPath, `${onlyName}.dwg`);
            const cmd = `cloudconvert convert -f dwg --outputdir files "${dxfFile}" --apikey ${APIKEY}`;
            exec(cmd, function (error, stdout, stderr) {
                if (error) {
                    res.redirect('back');
                    return;
                }
                fs.readFile(dwgFile, "utf8", function (err, data) {
                    if (err) throw err;
                    res.send({ fileName: `${onlyName}.dwg` });
                });
            });
        });
    });
});

app.route('/').get((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/html' });
    res.write('<h1>Svg to Dwg</h1>');
    res.write('<form action="svg2dwg" method="post" enctype="multipart/form-data">');
    res.write('<input type="file" name="svg2dwg">');
    res.write('<input type="submit">');
    res.write('</form>');

    res.write('<h1>Svg to Dxf</h1>');
    res.write('<form action="svg2dxf" method="post" enctype="multipart/form-data">');
    res.write('<input type="file" name="svg2dxf">');
    res.write('<input type="submit">');
    res.write('</form>')
    
    res.write('<h1>Svg to Dxf</h1>');
    res.write('<form action="svg2dxf" method="post" enctype="multipart/form-data">');
    res.write('<input type="file" name="svg2dxf">');
    res.write('<input type="submit">');
    res.write('</form>');
    
    res.write('<h1>Dxf to Svg</h1>');
    res.write('<form action="dxf2svg" method="post" enctype="multipart/form-data">');
    res.write('<input type="file" name="dxf2svg">');
    res.write('<input type="submit">');
    res.write('</form>');
    
    res.write('<h1>Svg to Dxf</h1>');
    res.write('<form action="dwg2svg" method="post" enctype="multipart/form-data">');
    res.write('<input type="file" name="dwg2svg">');
    res.write('<input type="submit">');
    res.write('</form>');

    return res.end();
});

const server = app.listen(PORT, function () {
    console.log(`Listening on port ${server.address().port}`);
});