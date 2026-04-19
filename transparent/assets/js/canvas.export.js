var CanvasExport = function (parameter) {
  const main = this;

  this.background = parameter;

  main.fnInit = function () {
    const btnPng = document.getElementById("export-png-icon");
    const btnJPG = document.getElementById("export-jpeg-icon");
    const btnPDF = document.getElementById("export-pdf-icon");

    btnPng.addEventListener("click", () => {
      main.downloadPNG();
    });

    btnJPG.addEventListener("click", () => {
      main.downloadJPG();
    });

    btnPDF.addEventListener("click", () => {
      main.downloadPdf();
    });
  };

  main.downloadPNG = function () {
    Swal.fire({
      title: "Save As JPG",
      html: `File Name <input id="png-file-name">`,
      showCancelButton: true,
      allowOutsideClick: false,
      preConfirm: () => {
        const fileName = Swal.getPopup().querySelector("#png-file-name").value;
        if (!fileName) {
          Swal.showValidationMessage(`Please input filename`);
        }
        return fileName;
      },
    }).then((result) => {
      if (!result.value) return;

      let downloadLink = document.createElement("a");
      downloadLink.setAttribute("download", result.value + ".png");
      let canvas = document.querySelector("canvas");
      // var context = canvas.getContext("experimental-webgl", {preserveDrawingBuffer: true});
      const { width, height } = canvas.getBoundingClientRect();

      const canvas1 = document.createElement("canvas");
      const context = canvas1.getContext("2d");
      canvas1.width = width;
      canvas1.height = height;

      //#https://stackoverflow.com/questions/45624502/html5-canvas-rotate-gradient-around-centre

      if (this.background.colorType === "linear-gradient") {
        const angle = ((this.background.angle - 90) * Math.PI) / 180;

        // const maxWidth = width / 2;
        const maxWidth = width / 2;
        const aspect = height / width;

        const grd = context.createLinearGradient(
          width / 2 - Math.cos(angle) * maxWidth, // start pos
          height / 2 - Math.sin(angle) * maxWidth * aspect,
          width / 2 + Math.cos(angle) * maxWidth, // end pos
          height / 2 + Math.sin(angle) * maxWidth * aspect
        );

        this.background.colorArray.forEach((gColor) => {
          grd.addColorStop(gColor.per / 100, gColor.color);
        });

        context.fillStyle = grd;
        context.fillRect(0, 0, width, height);
      } else if (this.background.colorType === "solid") {
        context.fillStyle = this.background.colorArray[0].color;
        context.fillRect(0, 0, width, height);
      }

      context.drawImage(canvas, 0, 0);

      let dataURL = canvas1.toDataURL("image/png");
      let url = dataURL.replace(
        /^data:image\/png/,
        "data:application/octet-stream"
      );
      downloadLink.setAttribute("href", url);
      downloadLink.click();
    });
  };

  main.downloadJPG = function () {
    Swal.fire({
      title: "Save As JPG",
      html: `File Name <input id="jpg-file-name">`,
      showCancelButton: true,
      allowOutsideClick: false,
      preConfirm: () => {
        const fileName = Swal.getPopup().querySelector("#jpg-file-name").value;
        if (!fileName) {
          Swal.showValidationMessage(`Please input filename`);
        }
        return fileName;
      },
    }).then((result) => {
      if (!result.value) return;

      let downloadLink = document.createElement("a");
      downloadLink.setAttribute("download", result.value + ".jpeg");

      const canvas = document.querySelector("canvas");
      const { width, height } = canvas.getBoundingClientRect();

      const canvas1 = document.createElement("canvas");
      const context = canvas1.getContext("2d");
      canvas1.width = width;
      canvas1.height = height;

      //#https://stackoverflow.com/questions/45624502/html5-canvas-rotate-gradient-around-centre

      if (this.background.colorType === "linear-gradient") {
        const angle = ((this.background.angle - 90) * Math.PI) / 180;

        const maxWidth = width / 2;
        const aspect = height / width;

        const grd = context.createLinearGradient(
          width / 2 - Math.cos(angle) * maxWidth, // start pos
          height / 2 - Math.sin(angle) * maxWidth * aspect,
          width / 2 + Math.cos(angle) * maxWidth, // end pos
          height / 2 + Math.sin(angle) * maxWidth * aspect
        );

        this.background.colorArray.forEach((gColor) => {
          grd.addColorStop(gColor.per / 100, gColor.color);
        });

        context.fillStyle = grd;
        context.fillRect(0, 0, width, height);
      } else if (this.background.colorType === "solid") {
        context.fillStyle = this.background.colorArray[0].color;
        context.fillRect(0, 0, width, height);
      }

      context.drawImage(canvas, 0, 0);

      let dataURL = canvas1.toDataURL("image/jpeg");
      downloadLink.setAttribute("href", dataURL);
      downloadLink.click();
    });
  };

  main.pixelsToMm = function(pixels) {
    return pixels * (25.4 / 72); // Convert pixels to millimeters
  }

  main.downloadPdf = function () {
    Swal.fire({
      title: "Save As PDF",
      html: `File Name <input id="pdf-file-name">`,
      showCancelButton: true,
      allowOutsideClick: false,
      preConfirm: () => {
        const fileName = Swal.getPopup().querySelector("#pdf-file-name").value;
        if (!fileName) {
          Swal.showValidationMessage(`Please input filename`);
        }
        return fileName;
      },
    }).then((result) => {
      if (!result.value) return;
      const canvas = document.querySelector("canvas");
      const imgData = canvas.toDataURL("image/jpeg", 1.0);
      const imgRatio = canvas.width / canvas.height;
      const pdf = new jsPDF({
        orientation: 'l',
        unit: 'px',
        format: [canvas.width / 2, canvas.width * imgRatio / 2 ]
      });
      pdf.addImage(imgData, "JPEG", 0, 0, canvas.width * imgRatio / 2, canvas.width / 2);

      pdf.save(result.value + ".pdf");
    });
  };

  main.fnInit();
};
