import _ from "lodash";
import enviroment from "src/constants/enviroment";
import { IModel } from "types/model";
import { MpSdk } from "bundle/sdk";
import { jsPDF } from "jspdf";
import { MeshoptDecoder } from "three/examples/jsm/libs/meshopt_decoder.module";

export const RECT_TABLE_DEFAULT = "RECT_TABLE_DEFAULT";
export const ROUND_TABLE_DEFAULT = "ROUND_TABLE_DEFAULT";

export const modelLoader = (url: string, THREE: any) => {
  return new Promise((resolve, reject) => {
    const loader = new THREE.GLTFLoader();
    const dracoLoader = new THREE.DRACOLoader();
    dracoLoader.setDecoderPath(
      "https://raw.githubusercontent.com/mrdoob/three.js/r147/examples/js/libs/draco/"
    );
    loader.setDRACOLoader(dracoLoader).setMeshoptDecoder(MeshoptDecoder);

    loader.load(
      `${url}?t=${Date.now().toString(36)}`,
      (data: any) => resolve(data),
      null,
      reject
    );
  });
};

export const loadGltf = async (
  models: IModel[],
  chunkSize: number,
  THREE: any
) => {
  const chunks = _.chunk(models, chunkSize);

  const amount = _.size(chunks);
  const res = [];
  const optimizedFiles = [
    "3DVG_Banquet_Chair_reverse.gltf",
    "3DVG_Banquet_Chair.gltf",
    "NewRoundTable1.gltf",
    "Table cloth 1.8 x .76.gltf",
  ]
  for (const i in chunks) {
    const ch = chunks[i];

    const chPoints = await Promise.all(
      _.map(ch, async (p) => {
        let url = _.get(p, "model");
        if (optimizedFiles.includes(url)) {
          // url = url.replace(".gltf", ".glb");
        }

        if (_.isNil(url)) {
          console.warn("Model Path is not Exist");
          return p;
        }

        const gltf = await modelLoader(
          `${enviroment.HOST}/${url}`,
          THREE
        );

        // console.log(gltf)
        gltf.scene.traverse((d) => {
          if (d.isMesh) {
            d.material.metalness = 0;
            d.material.roughness = 0.5;
          }
        });
        return { ...p, gltf };
      })
    );

    res.push(chPoints);
  }

  return _.flatten(res);
};

// Export Model
export const exportPNG = async (fileName: string, renderer: MpSdk.Renderer) => {
  const url = await renderer.takeScreenShot({ width: 2000, height: 1500 });

  const a = document.createElement("a");
  a.download = `${fileName}.png`;
  a.href = url;
  a.click();
};

export const exportJPG = async (fileName: string, renderer: MpSdk.Renderer) => {
  const url = await renderer.takeScreenShot({ width: 2000, height: 1500 });

  const a = document.createElement("a");
  a.download = `${fileName}.jpg`;
  a.href = url;
  a.click();
};

export const exportPDF = async (fileName: string, renderer: MpSdk.Renderer) => {
  const pdf = new jsPDF("landscape");
  const pWidth = pdf.internal.pageSize.width;
  const pHeight = pdf.internal.pageSize.height;
  
  const url = await renderer.takeScreenShot({ width: 2000, height: 1500 });
  
  pdf.addImage(url, "JPEG", 0, 0, pWidth, pHeight);
  pdf.save(`${fileName}.pdf`);
};
