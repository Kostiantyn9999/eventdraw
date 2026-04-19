import { useMutation, useQuery, useQueryClient } from "react-query";
import axios from "axios";

import { API } from "./API";
import { IEvent, IMatterportDimension } from "types/floor";
import { IMatterport } from "types/matterport";

import store from "src/models/store";
import { addToast } from "src/models/actions/toastAction";
import { useHistory } from "react-router-dom";

export const useAllMatterports = () => {
  return useQuery(["matterports"], async () => getMatterportAll());
};

export const useMatterport = (id?: string) => {
  return useQuery(["matterport", id], async () =>
    id ? fetchMatterport(id) : null
  );
};

export const useCreateMatterport = () => {
  const queryClient = useQueryClient();
  const history = useHistory();

  return useMutation((data: any) => saveMatterport(data), {
    onSuccess: ({ data }: { data: { message: string } }) => {
      store.dispatch(
        addToast({
          content: {
            header: "Notification",
            message: data.message,
          },
          type: "",
        })
      );
      queryClient.invalidateQueries(["matterports"]);
      history.push("/matterports");
    },
  });
};

export const useUpdateMatterport = () => {
  const queryClient = useQueryClient();

  return useMutation(
    (data: { id: number; data: any }) => updateMatterport(data.id, data.data),
    {
      onSuccess: ({ data }: { data: { message: string } }) => {
        // console.log(d);
        store.dispatch(
          addToast({
            content: {
              header: "Notification",
              message: data.message,
            },
            type: "",
          })
        );
        queryClient.invalidateQueries(["matterports"]);
      },
    }
  );
};

export const useRemoveMatterport = () => {
  const queryClient = useQueryClient();

  return useMutation((id: number) => removeMatterport(id), {
    onSuccess: ({ data }: { data: { message: string } }) => {
      store.dispatch(
        addToast({
          content: {
            header: "Notification",
            message: data.message,
          },
          type: "",
        })
      );
      queryClient.invalidateQueries(["matterports"]);
    },
  });
};

export const getMatterport = async (sceneId: string) =>
  API.get<Array<IMatterport>>(`/api/matterports/${sceneId}`);

export const getMatterportAll = async () =>
  // API.get<Array<IMatterport>>("/api/matterports");
  axios.get<Array<IMatterport>>(
    `https://test.eventdraw.com.au/frontend/web/site/matterport-list?t=${Date.now().toString(
      36
    )}`
  );
// fetch("https://test.eventdraw.com.au/frontend/web/site/matterport-list")

export const saveMatterport = async (matterport: any) => {
  const data = new FormData();
  data.append("mat", matterport.mat);
  data.append("name", matterport.name);
  data.append("minX", matterport.minX);
  data.append("maxX", matterport.maxX);
  data.append("minY", matterport.minY);
  data.append("maxY", matterport.maxY);
  data.append("axis", matterport.axis);
  data.append("baseElevation", matterport.baseElevation);
  data.append("event", matterport.event);
  data.append("reg_man", matterport.reg_man);
  data.append("rotation", matterport.rotation);

  return axios.post(
    "https://test.eventdraw.com.au/frontend/web/site/matterport-save",
    data,
    {
      headers: {
        "Content-Type": "multipart/form-data;",
      },
    }
  );
};

export const fetchMatterport = async (
  id: number | string
): Promise<IMatterport> =>
  axios.get(
    `https://test.eventdraw.com.au/frontend/web/site/matterport-get?id=${id}&t=${Date.now().toString(
      36
    )}`
  );

// API.get(
//   `https://test.eventdraw.com.au/frontend/web/site/matterport-get?id=${id}`
// );

// ): Promise<IMatterport> => API.get(`/api/matterports/${id}`);

export const updateMatterport = async (id: number, matterport: any) => {
  const data = new FormData();
  data.append("id", id.toString());
  data.append("mat", matterport.mat);
  data.append("name", matterport.name);
  data.append("minX", matterport.minX);
  data.append("maxX", matterport.maxX);
  data.append("minY", matterport.minY);
  data.append("maxY", matterport.maxY);
  data.append("axis", matterport.axis);
  data.append("baseElevation", matterport.baseElevation);
  data.append("rotation", matterport.rotation);

  // data.append("event", matterport.event);
  // data.append("events", JSON.stringify([11104, 9326]))
  // data.append("events", [11104, 9326])
  // console.log(matterport);
  matterport.events.forEach((d: IEvent, index: number) => {
    data.append(`events[${index}]`, d.id);
  });

  matterport.templates.forEach((d: IEvent, index: number) => {
    data.append(`templates[${index}]`, d.id);
  });

  return axios.post(
    `https://test.eventdraw.com.au/frontend/web/site/matterport-update`,
    data,
    {
      headers: {
        "Content-Type": "multipart/form-data;",
      },
    }
  );
};

export const removeMatterport = async (id: number) =>
  // API.delete(`/api/matterports/${id}`);
  axios.get(
    `https://test.eventdraw.com.au/frontend/web/site/matterport-delete?id=${id}`
  );

// Client Part
export const getDimension = async (mat: string, id?: string) =>
  axios.get<IMatterportDimension>(
    `https://test.eventdraw.com.au/frontend/web/site/matterport-get-dimension?mat=${mat}&id=${id}&t=${Date.now().toString(
      36
    )}`
  );

export const saveMatterportFile = async (file: File) => {
  const data = new FormData();
  data.append("file", file);

  return API.post(
    `https://3d.eventdraw.com.au/eventdraw_api/public/api/upload`,
    data,
    {
      headers: {
        "Content-Type": "multipart/form-data;",
      },
    }
  );
};
