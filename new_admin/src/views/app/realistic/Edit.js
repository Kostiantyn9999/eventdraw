import React from "react";
import { useParams } from "react-router-dom";
import { BreadcrumbContainer } from "ui";
import RealisticForm from "./Form";

const EditRealistic = () => {
  const params = useParams();

  return (
    <>
      <BreadcrumbContainer
        title={params.id ? "Update realistic" : "Create a new realistic"}
        paths={[
          {
            title: "Realistic",
            page: `/realistic`,
          },
          {
            title: params.id ? "Edit" : "Add",
          },
        ]}
      />

      <RealisticForm editId={params.id} />
    </>
  );
};

export default EditRealistic;
