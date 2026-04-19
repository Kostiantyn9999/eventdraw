import React from "react";
import { useParams } from "react-router-dom";
import { BreadcrumbContainer } from "ui";
import MatterportForm from "./Form";

const EditMatterport = () => {
  const params = useParams();

  return (
    <>
      <BreadcrumbContainer
        title={params.id ? "Update matterport" : "Create a new matterport"}
        paths={[
          {
            title: "Matterport",
            page: `/matterport`,
          },
          {
            title: params.id ? "Edit" : "Add",
          },
        ]}
      />

      <MatterportForm editId={params.id} />
    </>
  );
};

export default EditMatterport;
