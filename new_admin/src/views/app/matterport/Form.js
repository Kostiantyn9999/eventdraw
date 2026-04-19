import React, { useEffect, useState, Suspense, useMemo } from "react";
import * as Mui from "@mui/material";
import { Link, useNavigate } from "react-router-dom";
import { Formik, Form } from "formik";
import { useDispatch, useSelector } from "react-redux";
import { getMatterport, getAllEvent, getAllTemplate, addMatterport, editMatterport } from "reduxs/actions";
import { StyledCard, StyledButton, FileUpload } from "ui";
import { InputField, MultiSelectField } from "ui/form/field";
import { StyledCheckbox } from "ui/form";
import { toaster } from "helpers";
import ScreenLock from "./components/ScreenLock";
// import ViewSwitch from "./components/ViewSwitch";
import { makeScene } from "./components/Scene";
import { makeSdk } from "./components/Sdk";
import Frame from "./components/Frame";
import { SceneLoader, sidToScene } from "./components/SceneLoader";
import { formatDate } from "helpers/util";
import { getDimension, mid, loadAsyncFile } from "helpers/util";
import { INIT_SCENE_DATA } from "configs/constants";
import Req from "interceptors/TokenInterceptor";

const pdfjsLib = window["pdfjs-dist/build/pdf"];
pdfjsLib.GlobalWorkerOptions.workerSrc = process.env.PUBLIC_URL + "/assets/js/pdf.worker.js";

const MatterportForm = (props) => {
  const { editId } = props;

  const theme = Mui.useTheme();
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const [fileList, setFileList] = useState([]);
  const [overLay, setOverLay] = useState(true);
  const [unlockAspect, setUnlockAspect] = useState(false);

  const { loading, matterportData } = useSelector((state) => state.matterport);

  /* matterport scene section */
  const [attrItems, setAttrItems] = useState({
    x: { min: 0, max: 10 }, y: { min: 0, max: 10 },
    matterId: "",
    name: "",
    admin: "",
    image: "",
    regData: formatDate(),
    baseElevation: 0,
    floor: 1,
    axis: false,
    rotation: 0,
    fileName: "",
  });
  const matterId = useMemo(() => attrItems.matterId, [attrItems]);
  const [sdk, setSdk] = useState(null);
  const [slot, setSlot] = useState(null);
  const [centerSlot, setCenterSlot] = useState(null);

  const [comparedBackImage, setComparedBackImage] = useState(null);
  const [lockScreen, setLockScreen] = useState(true);

  const [previousX, setPreviousX] = useState(0);
  const [previousZ, setPreviousY] = useState(0);
  const [isDrag, setIsDrag] = useState(false);
  const [tempMatterId, setTempMatterId] = useState(matterId);
  
  const [poseCache, setPoseCache] = useState(null);
  const [firstPosition, setFirstPosition] = useState(false);
  const [startPosition, setStartPosition] = useState(false);
  const [fileName, setFileName] = useState(false);
  const [imageFile, setImageFile] = useState(false);

  const onSubmit = async (values) => {
    if (!attrItems.name) {
      toaster("warn", "Please enter a Name");
      return;
    }
    if (!tempMatterId) {
      toaster("warn", "Please enter a Matterport ID");
      return;
    }
    if (!loading) {
      let imagePath = attrItems.image;
      if (fileList[0]) {
        const formData = new FormData();
        formData.append("file", fileList[0]);
        const response = await Req.post(`${process.env.REACT_APP_API_URL_1}/api/upload`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          }
        });
        if (response.data && response.data.url) {
          imagePath = response.data.url;
        }
      }

      const data = {
        mat: tempMatterId,
        name: attrItems.name,
        minX: attrItems.x.min,
        maxX: attrItems.x.max,
        minY: attrItems.y.min,
        maxY: attrItems.y.max,
        image: imagePath,
        baseElevation: attrItems.baseElevation,
        floor: attrItems.floor,
        axis: attrItems.axis,
        rotation: attrItems.rotation,
        events: values.events,
        templates: values.templates,
        filename: fileName,
      };
      if (editId) {
        dispatch(editMatterport(editId, data, navigate));
      } else {
        dispatch(addMatterport(data, navigate));
      }
    }
  };

  useEffect(() => {
    if (editId) dispatch(getMatterport(editId));
  }, []);

  const [diffMatterId, setDiffMatterId] = useState(false);
  const setMatterPortId = () => {
    setDiffMatterId(true);
    setSdk(null);
    const sdk = makeSdk("sdk-iframe");
    makeScene(sdk);
    sdk.init(process.env.REACT_APP_MAT_APP_KEY, setSdk);

    setAttrItems({ ...attrItems, matterId: tempMatterId });
    initMatterport();
    setFirstPosition(false);
  };

  useEffect(() => {
    if (matterId) {
      setLockScreen(false);
      setTempMatterId(matterId);
    }
    return () => { };
  }, [matterId]);

  useEffect(() => {
    if (!loading) {
      const sdk = makeSdk("sdk-iframe");
      makeScene(sdk);
      sdk.init(process.env.REACT_APP_MAT_APP_KEY, setSdk);
    }
    if (matterportData) {
      setAttrItems({
        x: { min: parseFloat(matterportData?.minX.toFixed(5)) || 0, max: parseFloat(matterportData?.maxX.toFixed(5)) || 1 },
        y: { min: parseFloat(matterportData?.minY.toFixed(5)) || 0, max: parseFloat(matterportData?.maxY.toFixed(5)) || 1 },
        matterId: matterportData?.mat || "",
        name: matterportData?.name || "",
        admin: matterportData?.admin || "",
        image: matterportData?.image || "",
        regData: matterportData?.creation_date || 0,
        baseElevation: matterportData?.baseElevation || 0,
        floor: matterportData?.floor || 1,
        axis: matterportData?.axis || false,
        rotation: matterportData?.rotation || 0,
        fileName: matterportData?.filename || " ",
      });

      setFileName(matterportData?.filename || " ");

      initMatterport();
    }
  }, [loading]);

  useEffect(() => {
    if (slot) {
      slot.inputs.xMin = attrItems.x.min;
      slot.inputs.xMax = attrItems.x.max;
      slot.inputs.yMin = attrItems.y.min;
      slot.inputs.yMax = attrItems.y.max;
      slot.inputs.axis = attrItems.axis;
      slot.inputs.elevation = attrItems.baseElevation;
      slot.inputs.rotation = attrItems.rotation;
      slot.inputs.startPosition = startPosition;
    }

    if (centerSlot) {
      const centerX = mid(attrItems.x.min, attrItems.x.max);
      const centerY = mid(attrItems.y.min, attrItems.y.max);

      centerSlot.inputs.x = centerX;
      centerSlot.inputs.y = centerY;
      centerSlot.inputs.elevation = attrItems.baseElevation;
    }
  }, [attrItems, startPosition]);

  useEffect(() => {
    if (slot) {
      slot.inputs.comparedBackImage = comparedBackImage;
      slot.inputs.overlay = overLay;
    }
    if (centerSlot) {
      centerSlot.inputs.overlay = overLay;
    }
  }, [comparedBackImage, overLay]);

  useEffect(() => {
    if (imageFile) {
      confirmBackgroundImage(imageFile, true);
    }
  }, [imageFile]);

  const renderPage = async (data) => {
    const imagesList = [];
    const canvas = document.createElement("canvas");
    canvas.setAttribute("className", "canv");
    const pdf = await pdfjsLib.getDocument({ data }).promise;
    for (let i = 1; i <= pdf.numPages; i++) {
      const page = await pdf.getPage(i);
      const viewport = page.getViewport({ scale: 1.5 });
      canvas.height = viewport.height;
      canvas.width = viewport.width;
      const render_context = {
        canvasContext: canvas.getContext("2d"),
        viewport: viewport,
      };
      await page.render(render_context).promise;
      const img = canvas.toDataURL("image/png");
      imagesList.push(img);
    }
    if (imagesList.length > 0) {
      setImageFile(imagesList[0]);
    }
  };
  
  const UrlUploader = (url) => {
    fetch(url).then((response) => {
      response.blob().then((blob) => {
        const reader = new FileReader();
        reader.onload = (e) => {
          const data = atob(e.target.result.replace(/.*base64,/, ""));
          renderPage(data);
        };
        reader.readAsDataURL(blob);
      });
    });
  };

  useEffect(() => {
    setImageFile(false);
    if (fileList.length > 0) {
      setFileName(fileList[0].name);
      if (fileList[0].type == "application/pdf") {
        UrlUploader(URL.createObjectURL(fileList[0]));
        return;
      }
      confirmBackgroundImage(fileList[0]);
    } else {
      setFileName(matterportData?.filename || " ");
      if (attrItems.image) {
        const url = `${process.env.REACT_APP_API_URL_1}/${attrItems.image}`;
        setCrossImage(url);
      } else {
        setComparedBackImage(null);
      }
    }
  }, [fileList]);

  useEffect(() => {
    initMatterport();
  }, [sdk]);

  const setCrossImage = (url) => {
    const isPdf = url.toLocaleLowerCase().endsWith(".pdf");
    fetch(url)
      .then((response) => {
        if (response.ok) {
          return response.blob(); // Convert to blob
        }
        throw new Error("Network response was not ok.");
      })
      .then((blob) => {
        const url = URL.createObjectURL(blob); // Create object URL from blob
        if (isPdf) {
          UrlUploader(url);
          return;
        }
        confirmBackgroundImage(url, true);
      })
      .catch((error) => {
        console.error("There has been a problem with your fetch operation:", error);
      });
  };

  const initMatterport = async () => {
    if (!sdk) return;

    const data = matterportData ? matterportData
      : {
        minX: attrItems.x.min,
        maxX: attrItems.x.max,
        minY: attrItems.y.max,
        maxY: attrItems.y.max,
        axis: attrItems.axis,
        baseElevation: attrItems.baseElevation,
        floor: attrItems.floor,
        rotation: attrItems.rotation,
      };

    const sceneLoader = new SceneLoader(sdk);
    sidToScene.set("AAWs9eZ9ip6_AA", INIT_SCENE_DATA);
    const loadCallback = (node) => {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (
          component.componentType === "mp.dimension" &&
          component.inputs
        ) {
          component.inputs.xMin = data.minX;
          component.inputs.xMax = data.maxX;
          component.inputs.yMin = data.minY;
          component.inputs.yMax = data.maxY;
          component.inputs.axis = data.axis;
          component.inputs.elevation = data.baseElevation;
          component.inputs.rotation = data.rotation;

          setSlot(component);
          if (attrItems.image) {
            const url = `${process.env.REACT_APP_API_URL_1}/${attrItems.image}`;
            setCrossImage(url);
          }
        }

        if (component.componentType === "mp.point" && component.inputs) {
          component.inputs.x = mid(data.minX, data.maxX);
          component.inputs.y = mid(data.minY, data.maxY);
          component.inputs.elevation = data.baseElevation;

          setCenterSlot(component);
        }
      }
    };

    const startPos = await sdk.Camera.getPose();
    setStartPosition(startPos);
    setLockScreen(false);
    
    // Subscribe to camera pose changes
    sdk.Camera.pose.subscribe(function(pose) {
      setPoseCache(pose);
      if (!firstPosition) {
        setFirstPosition(true);
      }
    });

    // Subscribe to floor changes in Matterport SDK
    try {
      if (sdk.Floor && sdk.Floor.current) {
        if (editId && attrItems.floor) {
          switchMatterportFloor(attrItems.floor);
        }
        
        sdk.Floor.current.subscribe(function(currentFloor) {
          handleFloorChange(currentFloor);
        });
      }
    } catch (error) {
      console.warn("Floor events not available in this Matterport SDK version:", error);
    }

    await sceneLoader.load("AAWs9eZ9ip6_AA", loadCallback);
  };

  // Handle floor change events from Matterport SDK.
  // Only update the active floor number — baseElevation is a configured value
  // loaded from the server and must not be overwritten by viewer navigation.
  const handleFloorChange = (floorData) => {
    if (floorData) {
      setAttrItems((prev) => ({
        ...prev,
        floor: floorData.sequence + 1,
      }));
    }
  };

  // Switch floor in Matterport SDK based on form input
  const switchMatterportFloor = async (targetFloorNumber) => {
    if (!sdk || !sdk.Floor) return;

    try {
      await sdk.Floor.moveTo(targetFloorNumber - 1);
    } catch (error) {
      console.warn("Could not switch Matterport floor:", error);
    }
  };
  
  const toggleScreen = () => {
    setLockScreen(!lockScreen);
  };

  const confirmBackgroundImage = async (file, isUrl = false) => {
    let url = file;
    if (!isUrl) {
      url = await loadAsyncFile(file);
    }
    const image = new Image();
    image.onload = () => {
      const width = attrItems.x.max - attrItems.x.min;
      const height = attrItems.y.max - attrItems.y.min;
      
      const centerX = mid(attrItems.x.min, attrItems.x.max);
      const centerY = mid(attrItems.y.min, attrItems.y.max);

      const { xMin, xMax, yMin, yMax } = getDimension(width, height, [
        centerX,
        centerY,
      ]);
      setAttrItems({ ...attrItems, x: { min: xMin, max: xMax, }, y: { min: yMin, max: yMax, }, });
      setComparedBackImage({ image: image });
    };
    image.src = url;
  };

  const setMinX = (value) => {
    setAttrItems({ ...attrItems, x: { ...attrItems.x, min: value } });
  };

  const setMaxX = (value) => {
    setAttrItems({ ...attrItems, x: { ...attrItems.x, max: value } });
  };

  const setMinY = (value) => {
    setAttrItems({ ...attrItems, y: { ...attrItems.y, min: value } });
  };

  const setMaxY = (value) => {
    setAttrItems({ ...attrItems, y: { ...attrItems.y, max: value } });
  };

  const setRotation = (value) => {
    setAttrItems({ ...attrItems, rotation: value });
  };

  const { width, height, center } = useMemo(() => {
    if (!attrItems) {
      return {
        width: 0,
        height: 0,
        center: [0, 0],
      };
    }

    const width = attrItems.x.max - attrItems.x.min;
    const height = attrItems.y.max - attrItems.y.min;
    const centerX = mid(attrItems.x.min, attrItems.x.max);
    const centerY = mid(attrItems.y.min, attrItems.y.max);

    return {
      width: parseFloat(width.toFixed(5)),
      height: parseFloat(height.toFixed(5)),
      center: [centerX, centerY],
    };
  }, [attrItems]);

  useEffect(() => {
    if ((!editId || diffMatterId) && firstPosition && poseCache && center) {
      setDiffMatterId(false);
      const { xMin, xMax, yMin, yMax } = getDimension(width, height, [poseCache.position.x, poseCache.position.z]);
      setAttrItems({ ...attrItems, x: { min: xMin, max: xMax }, y: { min: yMin, max: yMax } });
    }
  }, [firstPosition]);

  const resizeStatus = {
    leftTop: 0, 
    leftBottom: 1, 
    rightTop: 2, 
    rightBottom: 3
  };

  const [resizeReady, setResizeReady] = useState(null);
  const [isResize, setIsResize] = useState(false);

  const rotatePoint = (cx, cy, x, y, angle) => {
    const radians = (Math.PI / 180) * angle;
    const cos = Math.cos(radians);
    const sin = Math.sin(radians);
    const nx = (cos * (x - cx)) + (sin * (y - cy)) + cx;
    const ny = (cos * (y - cy)) - (sin * (x - cx)) + cy;

    return {
      x: nx, 
      y: ny
    };
  };

  const setResizable = (mp) => {
    if (!poseCache) return;
    const sdkEle = document.getElementById("sdk-iframe");
    const size = {
      w: sdkEle.clientWidth,
      h: sdkEle.clientHeight,
    };
    let ltc = sdk.Conversion.worldToScreen(slot.leftTopPivot.position, poseCache, size);
    let lbc = sdk.Conversion.worldToScreen(slot.leftBottomPivot.position, poseCache, size);
    let rtc = sdk.Conversion.worldToScreen(slot.rightTopPivot.position, poseCache, size);
    let rbc = sdk.Conversion.worldToScreen(slot.rightBottomPivot.position, poseCache, size);
    ltc.x = Math.abs(ltc.x);
    ltc.y = Math.abs(ltc.y);
    lbc.x = Math.abs(lbc.x);
    lbc.y = Math.abs(lbc.y);
    rtc.x = Math.abs(rtc.x);
    rtc.y = Math.abs(rtc.y);
    rbc.x = Math.abs(rbc.x);
    rbc.y = Math.abs(rbc.y);
    
    // Rectangle properties
    const rectX = ltc.x;
    const rectY = ltc.y;
    const width = rtc.x - ltc.x;
    const height = lbc.y - ltc.y;
    const angle = attrItems.rotation;
    // Center of the rectangle
    const centerX = rectX + width / 2;
    const centerY = rectY + height / 2;
    
    ltc = rotatePoint(centerX, centerY, rectX, rectY, angle);
    lbc = rotatePoint(centerX, centerY, rectX, rectY + height, angle);
    rtc = rotatePoint(centerX, centerY, rectX + width, rectY, angle);
    rbc = rotatePoint(centerX, centerY, rectX + width, rectY + height, angle);
    
    const p = 15;
    let isR = false;
    let isS = null;
    document.body.style.cursor = "default";
    if (mp.x > ltc.x-p && mp.x < ltc.x+p && mp.y > ltc.y-p && mp.y < ltc.y+p) {
      isS = resizeStatus["leftTop"];
      isR = true;
      document.body.style.cursor = "nw-resize";
    } else if (mp.x > lbc.x-p && mp.x < lbc.x+p && mp.y > lbc.y-p && mp.y < lbc.y+p) {
      isS = resizeStatus["leftBottom"];
      isR = true;
      document.body.style.cursor = "ne-resize";
    } else if (mp.x > rtc.x-p && mp.x < rtc.x+p && mp.y > rtc.y-p && mp.y < rtc.y+p) {
      isS = resizeStatus["rightTop"];
      isR = true;
      document.body.style.cursor = "ne-resize";
    } else if (mp.x > rbc.x-p && mp.x < rbc.x+p && mp.y > rbc.y-p && mp.y < rbc.y+p) {
      isS = resizeStatus["rightBottom"];
      isR = true;
      document.body.style.cursor = "nw-resize";
    }
    setIsResize(isR);
    setResizeReady(isS);
  };

  const mouseDownLockScreen = async (e) => {
    const sdkEle = document.getElementById("sdk-iframe").getBoundingClientRect();
    const mousePos = {
      x: e.clientX - sdkEle.x,
      y: e.clientY - sdkEle.y,
    };
    const initPos = await sdk.Renderer.getWorldPositionData(mousePos, 0);
    setIsDrag(true);
    setResizable(mousePos);
    setPreviousX(initPos.position.x);
    setPreviousY(initPos.position.z);
  };

  const mouseMoveLockScreen = async (e) => {
    if (!sdk) return;

    const sdkEle = document.getElementById("sdk-iframe").getBoundingClientRect();
    const mousePos = {
      x: e.clientX - sdkEle.x,
      y: e.clientY - sdkEle.y,
    };
    
    if (!isDrag && sdk) {
      setResizable(mousePos);
      return;
    }

    const initPos = await sdk.Renderer.getWorldPositionData(mousePos, 0);
    const offsetX = initPos.position.x - previousX;
    const offsetZ = initPos.position.z - previousZ;

    const width = attrItems.x.max - attrItems.x.min;
    const height = attrItems.y.max - attrItems.y.min;
    const centerX = mid(attrItems.x.min, attrItems.x.max);
    const centerY = mid(attrItems.y.min, attrItems.y.max);

    if (isResize) {
      const { xMin, xMax, yMin, yMax } = getDimension(width, height, [centerX, centerY]);
      let autoOffsetZ = offsetZ;
      if (resizeReady == resizeStatus["leftTop"]) {
        const modWidth = xMax - xMin - offsetX;
        let autoHeight = yMax - yMin - offsetZ;
        if (comparedBackImage && !unlockAspect) {
          const ratio = comparedBackImage.image.height / comparedBackImage.image.width;
          autoHeight = modWidth * ratio;
          autoOffsetZ = yMax - yMin - autoHeight;
        }
        setAttrItems({
          ...attrItems,
          x: {
            min: xMin + parseFloat(offsetX.toFixed(5)),
            max: xMax,
          },
          y: {
            min: yMin + parseFloat(autoOffsetZ.toFixed(5)),
            max: yMax,
          },
        });
      } else if (resizeReady == resizeStatus["leftBottom"]) {
        const modWidth = xMax - xMin - offsetX;
        let autoHeight = yMax - yMin + offsetZ;
        if (comparedBackImage && !unlockAspect) {
          const ratio = comparedBackImage.image.height / comparedBackImage.image.width;
          autoHeight = modWidth * ratio;
          autoOffsetZ = -yMax + yMin + autoHeight;
        }
        setAttrItems({
          ...attrItems,
          x: {
            min: xMin + parseFloat(offsetX.toFixed(5)),
            max: xMax,
          },
          y: {
            min: yMin,
            max: yMax + parseFloat(autoOffsetZ.toFixed(5)),
          },
        });
      } else if (resizeReady == resizeStatus["rightTop"]) {
        const modWidth = xMax - xMin + offsetX;
        let autoHeight = yMax - yMin - offsetZ;
        if (comparedBackImage && !unlockAspect) {
          const ratio = comparedBackImage.image.height / comparedBackImage.image.width;
          autoHeight = modWidth * ratio;
          autoOffsetZ = yMax - yMin - autoHeight;
        }
        setAttrItems({
          ...attrItems,
          x: {
            min: xMin,
            max: xMax + parseFloat(offsetX.toFixed(5)),
          },
          y: {
            min: yMin + parseFloat(autoOffsetZ.toFixed(5)),
            max: yMax,
          },
        });
      } else if (resizeReady == resizeStatus["rightBottom"]) {
        const modWidth = xMax - xMin + offsetX;
        let autoHeight = yMax - yMin + offsetZ;
        if (comparedBackImage && !unlockAspect) {
          const ratio = comparedBackImage.image.height / comparedBackImage.image.width;
          autoHeight = modWidth * ratio;
          autoOffsetZ = -yMax + yMin + autoHeight;
        }
        setAttrItems({
          ...attrItems,
          x: {
            min: xMin,
            max: xMax + parseFloat(offsetX.toFixed(5)),
          },
          y: {
            min: yMin,
            max: yMax + parseFloat(autoOffsetZ.toFixed(5)),
          },
        });
      }      
    } else {
      const { xMin, xMax, yMin, yMax } = getDimension(width, height, [centerX + offsetX, centerY + offsetZ]);
      setAttrItems({
        ...attrItems,
        x: {
          min: xMin,
          max: xMax,
        },
        y: {
          min: yMin,
          max: yMax,
        },
      });
    }
    setPreviousX(initPos.position.x);
    setPreviousY(initPos.position.z);
  };

  const mouseLeaveLockScreen = () => {
    setIsDrag(false);
    setIsResize(false);
    document.body.style.cursor = "default";
  };

  const mouseOutLockScreen = () => {
    setIsDrag(false);
    setIsResize(false);
    document.body.style.cursor = "default";
  };

  const setWidth = (width) => {
    let autoHeight = height;

    if (comparedBackImage) {
      const ratio = comparedBackImage.image.height / comparedBackImage.image.width;
      autoHeight = width * ratio;
    }

    const { xMin, xMax, yMin, yMax } = getDimension(width, autoHeight, center);
    setAttrItems({ ...attrItems, x: { min: xMin, max: xMax }, y: { min: yMin, max: yMax } });
  };

  const setHeight = (height) => {
    let autoWidth = width;

    if (comparedBackImage) {
      const ratio = comparedBackImage.image.height / comparedBackImage.image.width;
      autoWidth = height * ratio;
    }

    const { xMin, xMax, yMin, yMax } = getDimension(autoWidth, height, center);
    setAttrItems({ ...attrItems, x: { min: xMin, max: xMax }, y: { min: yMin, max: yMax } });
  };

  const setCenterX = (centerX) => {
    const { xMin, xMax, yMin, yMax } = getDimension(width, height, [centerX, center[1]]);
    setAttrItems({ ...attrItems, x: { min: xMin, max: xMax }, y: { min: yMin, max: yMax } });
  };

  const setCenterY = (centerY) => {
    const { xMin, xMax, yMin, yMax } = getDimension(width, height, [center[0], centerY]);
    setAttrItems({ ...attrItems, x: { min: xMin, max: xMax }, y: { min: yMin, max: yMax } });
  };

  const { events, templates } = useSelector((state) => state.realistic);

  const [eventOptions, setEventOptions] = useState([]);
  const [templateOptions, setTemplateOptions] = useState([]);

  const [selectedEventOptions, setSelectedEventOptions] = useState([]);
  const [selectedTemplateOptions, setSelectedTemplateOptions] = useState([]);

  useEffect(() => {
    if (matterportData && matterportData.events) {
      setEventOptions(matterportData.events.map((item) => {
        return {
          id: item.id,
          eventName: item.name
        };
      }));
    }
    if (matterportData && matterportData.templates) {
      setTemplateOptions(matterportData.templates.map((item) => {
        return {
          id: item.id,
          templateName: item.name
        };
      }));
    }
  }, [matterportData]);

  useEffect(() => {
    if (events != null) {
      const combinedArray = events.concat(selectedEventOptions);
      const uniqueArray = Object.values(
        combinedArray.reduce((acc, obj) => {
          acc[obj.id] = obj;
          return acc;
        }, {})
      );
      setEventOptions(uniqueArray);
    }
  }, [events]);

  useEffect(() => {
    if (templates != null) {
      const combinedArray = templates.concat(selectedTemplateOptions);
      const uniqueArray = Object.values(
        combinedArray.reduce((acc, obj) => {
          acc[obj.id] = obj;
          return acc;
        }, {})
      );
      setTemplateOptions(uniqueArray);
    }
  }, [templates]);

  const eventIds = [];
  matterportData?.events?.forEach((item) => {
    eventIds.push(item.id);
  });
  const templateIds = [];
  matterportData?.templates?.forEach((item) => {
    templateIds.push(item.id);
  });

  const memoInitialValues = useMemo(() => ({
    events: editId ? eventIds : [],
    templates: editId ? templateIds : []
  }), [matterportData]);

  const [initialValues, setInitValues] = useState({ ...memoInitialValues });

  useEffect(() => {
    setInitValues({ ...memoInitialValues });
  }, [memoInitialValues]);

  function filerEvent(filter) {
    dispatch(getAllEvent({ search: filter }));
  }

  function filterTemplate(filter) {
    dispatch(getAllTemplate({ search: filter }));
  }

  return (
    <>
      <Formik enableReinitialize={true} initialValues={initialValues} onSubmit={onSubmit}>
        {({ values, resetForm, setFieldValue }) => (
          <Form>
            <Mui.Grid container spacing={4}>
              <Mui.Grid item xs={12} xl={12}>
                <StyledCard sx={{ p: "20px", height: "750px", mb: "70px" }}>
                  {!attrItems.matterId && "Matterport"}
                  <Suspense fallback={null}>
                    <Mui.Box sx={{ position: "relative", width: "100%", height: "100%" }}>
                      {attrItems.matterId && <Frame src={attrItems.matterId} />}
                      {lockScreen && (
                        <Mui.Box
                          sx={{ position: "absolute", top: 0, width: "100%", height: "100%" }}
                          onMouseDown={mouseDownLockScreen}
                          onMouseMove={mouseMoveLockScreen}
                          onMouseUp={mouseLeaveLockScreen}
                          onMouseOut={mouseOutLockScreen}
                        />
                      )}
                      {attrItems.matterId && <ScreenLock lockScreen={lockScreen} toggleScreen={toggleScreen} />}
                    </Mui.Box>
                  </Suspense>
                  <Mui.Grid container spacing={4}>
                    <Mui.Grid item xs={8} xl={8}>
                      {/* {attrItems.matterId && <ViewSwitch sdk={sdk} startPosition={startPosition} />} */}
                    </Mui.Grid>
                    <Mui.Grid item xs={4} xl={4}>
                      {attrItems.matterId && <Mui.Box
                        width="100%"
                        display="flex"
                        flexDirection={{ xs: "column", sm: "row" }}
                        justifyContent={{ sm: "flex-end" }}
                        gap={2}
                        mt={5}
                      >
                        <StyledCheckbox onChange={() => setUnlockAspect(!unlockAspect)} checked={unlockAspect} label={"Unlock Image Aspect Ratio"} />
                        <StyledCheckbox onChange={() => setOverLay(!overLay)} checked={overLay} label={"Show Overlay"} />
                      </Mui.Box>}
                    </Mui.Grid>
                  </Mui.Grid>
                </StyledCard>
              </Mui.Grid>

              <Mui.Grid item xs={12} xl={12}>
                <StyledCard sx={{ p: "1.5rem" }}>
                  <Mui.Grid container spacing={2}>
                    <Mui.Grid item xs={12} md={12}>
                      <FileUpload width="100%" fileTypes={{ "image/*": [], "application/pdf": [] }} multiple={false} callback={setFileList} name="compare image" />
                    </Mui.Grid>

                    <Mui.Grid item xs={12} md={12}>
                      <InputField name="name" type="text" value={attrItems.name} onChange={(e) => setAttrItems({ ...attrItems, name: e.target.value })} label="Name*" />
                    </Mui.Grid>

                    <Mui.Grid item xs={12} md={12}>
                      <Mui.Box
                        width="100%"
                        display="flex"
                        flexDirection={{ xs: "column", sm: "row" }}
                        justifyContent={{ sm: "flex-end" }}
                        gap={2}
                      >
                        <InputField name="mat" type="text" label="Matterport ID*" value={tempMatterId} onChange={(e) => setTempMatterId(e.target.value)} />
                        <StyledButton onClick={setMatterPortId} isloading={matterId === tempMatterId} type="button" sx={{ mt: "8px", mb: "4px" }}>
                          Display
                        </StyledButton>
                      </Mui.Box>
                    </Mui.Grid>

                    <Mui.Grid item xs={12} md={12}>
                      <MultiSelectField options={eventOptions} callback={ setSelectedEventOptions } filterEvent={filerEvent} name="events" type="select" label="Events" labelField="eventName" />
                    </Mui.Grid>
                    <Mui.Grid item xs={12} md={12}>
                      <MultiSelectField options={templateOptions} callback={ setSelectedTemplateOptions } filterEvent={filterTemplate} name="templates" type="select" label="Templates" labelField="templateName" />
                    </Mui.Grid>

                    <Mui.Grid item xs={12} md={12}>
                      <InputField name="filename" type="text" value={fileName} label="ImageName" disabled />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="width" type="number" value={width} label="Width*" onChange={(e) => setWidth(e.target.value ? parseFloat(e.target.value) : 1)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="height" type="number" value={height} label="Height*" onChange={(e) => setHeight(e.target.value ? parseFloat(e.target.value) : 1)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="centerX" type="number" value={center[0]} label="CenterX*" onChange={(e) => setCenterX(e.target.value ? parseFloat(e.target.value) : 0)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="centerY" type="number" value={center[1]} label="CenterY*" onChange={(e) => setCenterY(e.target.value ? parseFloat(e.target.value) : 0)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="minX" type="number" value={attrItems.x.min} label="MinX*" onChange={(e) => setMinX(e.target.value ? parseFloat(e.target.value) : 0)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="maxX" type="number" value={attrItems.x.max} label="MaxX*" onChange={(e) => setMaxX(e.target.value ? parseFloat(e.target.value) : 1)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="minY" type="number" value={attrItems.y.min} label="MinY*" onChange={(e) => setMinY(e.target.value ? parseFloat(e.target.value) : 0)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="maxY" type="number" value={attrItems.y.max} label="MaxY*" onChange={(e) => setMaxY(e.target.value ? parseFloat(e.target.value) : 1)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={12} md={12}>
                      <InputField name="rotation" type="number" value={attrItems.rotation} label="Rotate*" onChange={(e) => setRotation(e.target.value ? parseFloat(e.target.value) : 0)} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="floor" type="number" value={attrItems.floor} label="Floor Number" disabled onChange={(e) => setAttrItems({ ...attrItems, floor: e.target.value ? parseInt(e.target.value) : 0 })} />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="baseElevation" type="number" value={attrItems.baseElevation} label="Base Elevation" onChange={(e) => setAttrItems({ ...attrItems, baseElevation: e.target.value ? parseFloat(e.target.value) : 0 })} />
                    </Mui.Grid>

                  </Mui.Grid>

                  <Mui.Box
                    width="100%"
                    display="flex"
                    flexDirection={{ xs: "column", sm: "row" }}
                    justifyContent={{ sm: "flex-end" }}
                    gap={2}
                    mt={5}
                  >
                    <StyledButton type="submit" isloading={loading}>
                      {editId ? "Update Matterport" : "Create Matterport"}
                    </StyledButton>

                    <StyledButton
                      type="button"
                      variant="outlined"
                      color={theme.palette.grey.main}
                      sx={{ color: theme.palette.text.primary }}
                      component={Link}
                      to={`${process.env.PUBLIC_URL}/matterport`}
                    >
                      Cancel
                    </StyledButton>
                  </Mui.Box>
                </StyledCard>
              </Mui.Grid>
            </Mui.Grid>
          </Form>
        )}
      </Formik>
    </>
  );
};

export default MatterportForm;
