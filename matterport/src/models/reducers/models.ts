import { MODEL_ACTION } from "../actionTypes";

const initialState = null;

export default function models(state = initialState, action) {
  switch (action.type) {
    case MODEL_ACTION.RECEIVE_MODELS:
      return action.models;
    default:
      return state;
  }
}
