import { IModel } from "types/model";
import { MODEL_ACTION } from "../actionTypes";

export function receiveModels(models: IModel[]) {
  return {
    type: MODEL_ACTION.RECEIVE_MODELS,
    models,
  };
}
