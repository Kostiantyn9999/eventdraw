import React, { useEffect, useState, useMemo } from "react";
import * as Mui from "@mui/material";
import { Link, useNavigate } from "react-router-dom";
import { Formik, Form } from "formik";
import * as Yup from "yup";
import { useDispatch, useSelector } from "react-redux";
import { getRealistic, getAllEvent, getAllTemplate, addRealistic, editRealistic } from "reduxs/actions";
import { StyledCard, StyledButton, FileUpload } from "ui";
import { InputField, TextareaField, MultiSelectField } from "ui/form/field";
import Radio from "@mui/material/Radio";
import RadioGroup from "@mui/material/RadioGroup";
import FormControlLabel from "@mui/material/FormControlLabel";
import { toaster } from "helpers";
import Req from "interceptors/TokenInterceptor";
import { loadAsyncFile } from "helpers/util";
import { Viewer } from "../components/Viewer";

const RealisticForm = (props) => {
  const { editId } = props;

  const theme = Mui.useTheme();
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const [modelFileList, setModelFileList] = useState([]);
  const [screenFileList, setScreenFileList] = useState([]);
  const [dimension, setDimension] = useState({});
  const [switchValue, setSwitchValue] = useState("point");
  const [image, setImage] = useState(null);
  const [canvasRatio, setCanvasRatio] = useState(1);

  const { loading, realisticData, events, templates } = useSelector((state) => state.realistic);

  const [eventOptions, setEventOptions] = useState([]);
  const [templateOptions, setTemplateOptions] = useState([]);

  const [selectedEventOptions, setSelectedEventOptions] = useState([]);
  const [selectedTemplateOptions, setSelectedTemplateOptions] = useState([]);

  useEffect(() => {
    if (realisticData && realisticData.events) {
      setEventOptions(realisticData.events.map((item) => {
        return {
          id: item.id,
          eventName: item.name
        };
      }));
    } else {
      setEventOptions([]);
    }
    if (realisticData && realisticData.templates) {
      setTemplateOptions(realisticData.templates.map((item) => {
        return {
          id: item.id,
          templateName: item.name
        };
      }));
    } else {
      setTemplateOptions([]);
    }
  }, [realisticData]);

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
  realisticData?.events.forEach((item) => {
    eventIds.push(item.id);
  });
  const templateIds = [];
  realisticData?.templates.forEach((item) => {
    templateIds.push(item.id);
  });

  const memoInitialValues = useMemo(() => ({
    name: editId ? realisticData?.name || "" : "",
    description: editId ? realisticData?.description || "" : "",
    startx: editId ? realisticData?.startx ? String(realisticData?.startx) : 0 : 0,
    starty: editId ? realisticData?.starty ? String(realisticData?.starty) : 0 : 0,
    widthx: editId ? realisticData?.widthx ? String(realisticData?.widthx) : 0 : 0,
    widthy: editId ? realisticData?.widthy ? String(realisticData?.widthy) : 0 : 0,
    posx: editId ? realisticData?.posx ? String(realisticData?.posx) : 0 : 0,
    posy: editId ? realisticData?.posy ? String(realisticData?.posy) : 0 : 0,
    posh: editId ? realisticData?.posh ? String(realisticData?.posh) : 0.5 : 0.5,
    screenshot: editId ? realisticData?.screenshot || "" : "",
    model: editId ? realisticData?.model || "" : "",
    events: editId ? eventIds : [],
    templates: editId ? templateIds : []
  }), [realisticData]);

  const [initialValues, setInitValues] = useState({ ...memoInitialValues });

  const schema = Yup.object().shape({
    name: Yup.string().required("Name is required"),
    description: Yup.string().required("Description is required"),
    posh: Yup.number("Must be a number type").required("Camera Height is required"),
  });

  const onSubmit = async (values) => {
    if (!loading) {
      if (!editId && modelFileList.length == 0) {
        toaster("warn", "Please select a model file");
        return;
      }
      if (!editId && screenFileList.length == 0) {
        toaster("warn", "Please select a screenshot file");
        return;
      }
      let modelFilePath = "";
      let screenFilePath = "";
      if (modelFileList.length > 0) {
        const formData = new FormData();
        formData.append("file", modelFileList[0]);
        const response = await Req.post(`${process.env.REACT_APP_API_URL_1}/api/upload`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          }
        });
        if (response.data && response.data.url) {
          modelFilePath = response.data.url;
        }
      }
      if (screenFileList.length > 0) {
        const formData = new FormData();
        formData.append("file", screenFileList[0]);
        const response = await Req.post(`${process.env.REACT_APP_API_URL_1}/api/upload`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          }
        });
        if (response.data && response.data.url) {
          screenFilePath = response.data.url;
        }
      }
      const data = { ...values, model: modelFileList.length > 0 ? modelFilePath : realisticData.model, screenshot: screenFileList.length > 0 ? screenFilePath : realisticData.screenshot };
      if (editId) {
        dispatch(editRealistic(editId, data, navigate));
      } else {
        dispatch(addRealistic(data, navigate));
      }
    }
  };

  useEffect(() => {
    setInitValues({ ...memoInitialValues });
  }, [memoInitialValues]);

  useEffect(() => {
    const ele = document.getElementById("viewer");
    ele.innerHTML = "";
    if (!loading && editId && realisticData) {
      if (realisticData.model) {
        const viewer = new Viewer(ele);
        viewer.load(`${process.env.REACT_APP_API_URL_1}/${realisticData.model}`).catch((e) => {
          console.log(e);
        }).then((gltf) => {
          console.log(gltf);
        });
      }
      if (realisticData.screenshot) {
        const image = new Image();
        image.onload = () => {
          setImage(image);
          if (!dimension.canvas) return;
          dimension.canvas.width = image.width;
          dimension.canvas.height = image.height;
          const rect = dimension.canvas.getBoundingClientRect();
          setDimension({
            ...dimension,
            startX: realisticData.startx * rect.width,
            startY: realisticData.starty * rect.height,
            widthX: realisticData.widthx * rect.width + realisticData.startx * rect.width,
            widthY: realisticData.widthy * rect.height + realisticData.starty * rect.height,
            pointX: realisticData.posx * rect.width,
            pointY: realisticData.posx * rect.height,
          });
        };
        image.src = `${process.env.REACT_APP_API_URL_1}/${realisticData.screenshot}`;
      }
      return;
    }
    setImage(null);
  }, [realisticData]);

  useEffect(() => {
    const ele = document.getElementById("viewer");
    ele.innerHTML = "";
    if (modelFileList.length > 0 || (realisticData && realisticData.model)) {
      const url = modelFileList.length > 0 ? URL.createObjectURL(modelFileList[0]) : `${process.env.REACT_APP_API_URL_1}/${realisticData.model}`;
      const viewer = new Viewer(ele);
      viewer.load(url).catch((e) => {
        console.log(e);
      }).then((gltf) => {
        console.log(gltf);
      });
    }
  }, [modelFileList]);

  const drawScreenToCanvas = async (file) => {
    const url = await loadAsyncFile(file);
    const image = new Image();
    image.onload = () => {
      setImage(image);
    };
    image.src = url;
  };

  useEffect(() => {
    if (!image) return;
    const rect = dimension.canvas.getBoundingClientRect();
    dimension.canvas.width = image.width;
    dimension.canvas.height = image.height;
    setCanvasRatio(image.width / rect.width);
    dimension.context.strokeStyle = dimension.color;
    dimension.context.lineWidth = 4 * (image.width / rect.width);
  }, [image]);

  useEffect(() => {
    if (screenFileList.length > 0 && dimension.context) {
      drawScreenToCanvas(screenFileList[0]);
    } else {
      if (editId && realisticData && realisticData.screenshot) {
        const image = new Image();
        image.onload = () => {
          setImage(image);
        };
        image.src = `${process.env.REACT_APP_API_URL_1}/${realisticData.screenshot}`;
      } else {
        setImage(null);
      }
    }
  }, [screenFileList, dimension.context]);

  useEffect(() => {
    const canvas = document.getElementById("dimension-viewer");
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
    const ctx = canvas.getContext("2d");
    ctx.strokeStyle = dimension.color;
    ctx.lineWidth = 4 * canvasRatio;

    setDimension({
      color: "#E36049",
      canvas: canvas,
      context: ctx,
      isDown: false,
      radius: 8,
      startX: initialValues.startx * rect.width,
      startY: initialValues.starty * rect.height,
      widthX: initialValues.widthx * rect.width + initialValues.startx * rect.width,
      widthY: initialValues.widthy * rect.height + initialValues.starty * rect.height,
      pointX: editId ? initialValues.posx * rect.width : canvas.width / 2,
      pointY: editId ? initialValues.posx * rect.height : canvas.height / 2,
    });

    dispatch(getRealistic(editId));
  }, []);

  function handleMouseDown(e) {
    e.preventDefault();
    e.stopPropagation();

    if (switchValue !== "point") {
      setDimension({
        ...dimension,
        startX: e.nativeEvent.offsetX,
        startY: e.nativeEvent.offsetY,
        isDown: true,
      });
    } else {
      setDimension({
        ...dimension,
        pointX: e.nativeEvent.offsetX,
        pointY: e.nativeEvent.offsetY,
      });
      const rect = dimension.canvas.getBoundingClientRect();
      setInitValues({
        ...initialValues,
        posx: (e.nativeEvent.offsetX / rect.width).toFixed(2),
        posy: (e.nativeEvent.offsetY / rect.height).toFixed(2),
      });
    }
  }

  function handleMouseUp(e) {
    e.preventDefault();
    e.stopPropagation();
    if (switchValue === "point") {
      return;
    }

    setDimension({
      ...dimension,
      isDown: false,
    });
  }

  function handleMouseOut(e) {
    e.preventDefault();
    e.stopPropagation();
    if (switchValue === "point") {
      return;
    }

    setDimension({
      ...dimension,
      isDown: false,
    });
  }

  function handleMouseMove(e) {
    e.preventDefault();
    e.stopPropagation();

    if (!dimension.isDown || switchValue === "point") {
      return;
    }

    setDimension({
      ...dimension,
      widthX: e.nativeEvent.offsetX,
      widthY: e.nativeEvent.offsetY
    });
  }

  useEffect(() => {
    if (dimension.context) {
      const rect = dimension.canvas.getBoundingClientRect();
      dimension.context.strokeStyle = dimension.color;
      drawDimension();
      setInitValues({
        ...initialValues,
        widthx: ((dimension.widthX - dimension.startX) / rect.width).toFixed(2),
        widthy: ((dimension.widthY - dimension.startY) / rect.height).toFixed(2),
        startx: (dimension.startX / rect.width).toFixed(2),
        starty: (dimension.startY / rect.height).toFixed(2),
        posx: (dimension.pointX / rect.width).toFixed(2),
        posy: (dimension.pointY / rect.height).toFixed(2),
      });
    }
  }, [
    image,
    dimension.widthX,
    dimension.widthY,
    dimension.pointX,
    dimension.pointY,
    canvasRatio
  ]);

  function drawDimension() {
    dimension.context.clearRect(0, 0, dimension.canvas.width, dimension.canvas.height);

    const width = dimension.widthX - dimension.startX;
    const height = dimension.widthY - dimension.startY;

    if (image) {
      dimension.context.drawImage(image, 0, 0);
    }

    dimension.context.strokeRect(dimension.startX * canvasRatio, dimension.startY * canvasRatio, width * canvasRatio, height * canvasRatio);

    dimension.context.beginPath();
    dimension.context.arc(dimension.pointX * canvasRatio, dimension.pointY * canvasRatio, dimension.radius * canvasRatio, 0, Math.PI * 2);
    dimension.context.closePath();
    dimension.context.fillStyle = dimension.color;
    dimension.context.fill();
  }

  function filerEvent(filter) {
    dispatch(getAllEvent({ search: filter }));
  }

  function filterTemplate(filter) {
    dispatch(getAllTemplate({ search: filter }));
  }

  return (
    <Formik enableReinitialize={true} initialValues={initialValues} validationSchema={schema} onSubmit={onSubmit}>
      {({ values, resetForm, setFieldValue }) => (
        <Form>
          <Mui.Grid container spacing={4}>
            <Mui.Grid item xs={12} xl={8}>
              <StyledCard sx={{ p: "1.5rem", height: "500px" }}>
                <Mui.Typography sx={{ fontWeight: "600", fontSize: 18 }} display="block">
                  3D Model Viewer
                </Mui.Typography>
                <Mui.Box>
                  <Mui.Box id="viewer" style={{ width: "100%", height: "420px" }}></Mui.Box>
                </Mui.Box>
              </StyledCard>

              <StyledCard sx={{ p: "1.5rem", mt: "20px" }}>
                <Mui.Box
                  width="100%"
                  display="flex"
                  justifyContent={{ sm: "space-between" }}
                  alignItems={{ sm: "center" }}
                >
                  <Mui.Box sx={{ fontWeight: "600", fontSize: 18 }}>
                    Dimension Viewer
                  </Mui.Box>
                  <RadioGroup
                    row
                    aria-labelledby="demo-row-radio-buttons-group-label"
                    name="row-radio-buttons-group"
                    value={switchValue}
                    onChange={(e) => {
                      setSwitchValue(e.target.value);
                    }}
                  >
                    <FormControlLabel value="dimension" control={<Radio />} label="Dimension" />
                    <FormControlLabel value="point" control={<Radio />} label="Initial Position" />
                  </RadioGroup>
                </Mui.Box>
                <canvas
                  id="dimension-viewer"
                  style={{ border: "1px solid grey", width: "100%" }}
                  onMouseDown={handleMouseDown}
                  onMouseMove={handleMouseMove}
                  onMouseUp={handleMouseUp}
                  onMouseOut={handleMouseOut}
                />
              </StyledCard>
            </Mui.Grid>

            <Mui.Grid item xs={12} xl={4}>
              <StyledCard sx={{ p: "1.5rem" }}>
                <Mui.Grid container spacing={2}>
                  <Mui.Grid item xs={12} md={12}>
                    <FileUpload width="100%" fileSize={100 * 1024 * 1024} fileTypes={{ "multipart/form-data": [".glb", ".gltf"] }} multiple={false} callback={setModelFileList} name="model" />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <FileUpload width="100%" fileTypes={{ "image/*": [] }} multiple={false} callback={setScreenFileList} name="screenshot" />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <InputField name="name" type="text" label="Name*" />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <TextareaField name="description" rows="3" type="textarea" label="Description*" />
                  </Mui.Grid>

                  {/* <Mui.Grid item xs={12} md={12}>
                    <Mui.Box
                      width="100%"
                      display="flex"
                      flexDirection={{ xs: "column", sm: "row" }}
                      justifyContent={{ sm: "flex-end" }}
                      gap={2}
                    >
                      <InputField name="search" type="text" label="Search Event and Template" />
                      <StyledButton type="button" sx={{ mt: "8px", mb: "4px" }}>
                        Search
                      </StyledButton>
                    </Mui.Box>
                  </Mui.Grid> */}
                  <Mui.Grid item xs={12} md={12}>
                    <MultiSelectField options={eventOptions} callback={ setSelectedEventOptions } filterEvent={filerEvent} name="events" type="select" label="Events" labelField="eventName" />
                  </Mui.Grid>
                  <Mui.Grid item xs={12} md={12}>
                    <MultiSelectField options={templateOptions} callback={ setSelectedTemplateOptions } filterEvent={filterTemplate} name="templates" type="select" label="Templates" labelField="templateName" />
                  </Mui.Grid>

                  <Mui.Grid item xs={6} md={6}>
                    <InputField name="startx" type="text" label="Start-X" disabled />
                  </Mui.Grid>

                  <Mui.Grid item xs={6} md={6}>
                    <InputField name="starty" type="text" label="Start-Y" disabled />
                  </Mui.Grid>

                  <Mui.Grid item xs={6} md={6}>
                    <InputField name="widthx" type="text" label="Width-X" disabled />
                  </Mui.Grid>

                  <Mui.Grid item xs={6} md={6}>
                    <InputField name="widthy" type="text" label="Width-Y" disabled />
                  </Mui.Grid>

                  <Mui.Grid item xs={6} md={6}>
                    <InputField name="posx" type="text" label="Position-X" disabled />
                  </Mui.Grid>

                  <Mui.Grid item xs={6} md={6}>
                    <InputField name="posy" type="text" label="Position-Y" disabled />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <InputField name="posh" type="text" label="Default Camera Height*" />
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
                    {editId ? "Update Realistic" : "Create Realistic"}
                  </StyledButton>

                  <StyledButton
                    type="button"
                    variant="outlined"
                    color={theme.palette.grey.main}
                    sx={{ color: theme.palette.text.primary }}
                    component={Link}
                    to={`${process.env.PUBLIC_URL}/realistic`}
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
  );
};

export default RealisticForm;
