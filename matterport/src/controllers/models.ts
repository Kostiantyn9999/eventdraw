import { client } from "./api/client";

import store from "models/store";
import { receiveModels } from "models/actions/modelAction";

import enviroment from "src/constants/enviroment";
import { IModel } from "types/model";

const HOST = enviroment.HOST;

export const getModels = async (): Promise<IModel[]> =>
  client.get(`${HOST}/api/shapes/newversion/?&t=${Date.now().toString(36)}`);

export const fetchModels = async () => {
  const response = await client.get(
    `${HOST}/api/shapes/newversion/?&t=${Date.now().toString(36)}`
  );
  if (response == undefined) console.log("error");
  store.dispatch(receiveModels(response));
};
