import React from "react";
import { useParams } from "react-router-dom";
import { BreadcrumbContainer } from "ui";
import ShapeForm from "./Form";

const EditShape = () => {
  const params = useParams();

  return (
    <>
      <BreadcrumbContainer
        title={params.id ? "Update shape" : "Create a new shape"}
        paths={[
          {
            title: "Shape",
            page: `/shape`,
          },
          {
            title: params.id ? "Edit" : "Add",
          },
        ]}
      />

      <ShapeForm editId={params.id} />
    </>
  );
};

export default EditShape;
