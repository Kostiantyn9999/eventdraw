import React, { useEffect, useState } from "react";
import * as Mui from "@mui/material";
import { Link, useNavigate } from "react-router-dom";
import { Formik, Form } from "formik";
import * as Yup from "yup";
import { useDispatch, useSelector } from "react-redux";
import { getShape, addShape, editShape } from "reduxs/actions";
import { FileUpload, StyledCard, StyledButton } from "ui";
import { InputField, SelectField } from "ui/form/field";
import { toaster } from "helpers";
import { categoryList } from "configs/constants";
import { Viewer } from "../components/Viewer";

const ShapeForm = (props) => {
  const { editId } = props;

  const theme = Mui.useTheme();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [fileList, setFileList] = useState([]);

  const { loading, shapeData } = useSelector((state) => state.shape);

  const initialValues = {
    shapeType: editId ? shapeData?.shapeType || "" : "",
    elevate: editId ? shapeData?.elevate !== "" ? shapeData?.elevate + "" : "" : "",
    height: editId ? shapeData?.height !== "" ? shapeData?.height + "" : "" : "",
    description: editId ? shapeData?.description || "" : "",
    category: editId ? shapeData?.category || "" : "",
    shapetypes: editId ? shapeData?.shapetypes || "test" : "test",
    model: editId ? shapeData?.model || "" : "",
  };

  const schema = Yup.object().shape({
    shapeType: Yup.string().required("Shape type is required"),
    description: Yup.string().required("Description is required"),
    category: Yup.string().required("Category is required"),
    elevate: Yup.number("Must be a number type").required("Elevate is required"),
    height: Yup.number("Must be a number type").required("Height is required"),
  });

  const onSubmit = (values) => {
    if (!loading) {
      if (!editId && fileList.length == 0) {
        toaster("warn", "Please select a file");
        return;
      }
      const formData = new FormData();
      if (fileList.length > 0) {
        formData.append("model", fileList[0]);
      }
      formData.append("shapeType", values.shapeType);
      formData.append("height", values.height);
      formData.append("elevate", values.elevate);
      formData.append("description", values.description);
      formData.append("category", values.category);
      formData.append("shapetypes", values.shapetypes);
      if (editId) {
        dispatch(editShape(editId, formData, navigate));
      } else {
        dispatch(addShape(formData, navigate));
      }
    }
  };

  useEffect(() => {
    if (editId) dispatch(getShape(editId));
  }, []);

  useEffect(() => {
    const ele = document.getElementById("viewer");
    ele.innerHTML = "";
    if (!loading && shapeData && shapeData.model) {
      const viewer = new Viewer(ele);
      viewer.load(`${process.env.REACT_APP_API_URL_1}/${shapeData.model}`).catch((e) => {
        console.log(e);
      }).then((gltf) => {
        console.log(gltf);
      });
    }
  }, [shapeData]);

  useEffect(() => {
    const ele = document.getElementById("viewer");
    ele.innerHTML = "";
    if (fileList.length > 0 || (shapeData && shapeData.model)) {
      const url = fileList.length > 0 ? URL.createObjectURL(fileList[0]) : `${process.env.REACT_APP_API_URL_1}/${shapeData.model}`;

      const viewer = new Viewer(ele);
      viewer.load(url).catch((e) => {
        console.log(e);
      }).then((gltf) => {
        console.log(gltf);
      });
    }
  }, [fileList]);

  return (
    <Formik enableReinitialize={true} initialValues={initialValues} validationSchema={schema} onSubmit={onSubmit}>
      {({ values, resetForm, setFieldValue }) => (
        <Form>
          <Mui.Grid container spacing={4}>
            <Mui.Grid item xs={12} xl={8}>
              <StyledCard sx={{ p: "1.5rem", height: "650px" }}>
                <Mui.Box id="viewer" style={{ width: "100%", height: "100%" }}></Mui.Box>
              </StyledCard>
            </Mui.Grid>

            <Mui.Grid item xs={12} xl={4}>
              <StyledCard sx={{ p: "1.5rem" }}>
                <Mui.Grid container spacing={2}>
                  <Mui.Grid item xs={12} md={12}>
                    <FileUpload width="100%" fileSize={100 * 1024 * 1024} fileTypes={{ "multipart/form-data": [".glb", ".gltf"] }} multiple={false} callback={setFileList} />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <InputField name="shapeType" type="text" label="Shape Type*" />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <InputField name="description" type="text" label="Description*" />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <SelectField options={categoryList} name="category" type="select" label="Category*" />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <InputField name="elevate" type="text" label="Elevate*" />
                  </Mui.Grid>

                  <Mui.Grid item xs={12} md={12}>
                    <InputField name="height" type="text" label="Height*" />
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
                    {editId ? "Update Shape" : "Create Shape"}
                  </StyledButton>

                  <StyledButton
                    type="button"
                    variant="outlined"
                    color={theme.palette.grey.main}
                    sx={{ color: theme.palette.text.primary }}
                    component={Link}
                    to={`${process.env.PUBLIC_URL}/shape`}
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

export default ShapeForm;
