import { IToast } from "types/toast";
import { TOAST_ACTIONS, MATTERPORT_ADMIN_ACTIONS } from "../actionTypes";

const initialState: IToast[] = [];

export default function toast(state: IToast[] = initialState, action = "") {
  switch (action.type) {
    case TOAST_ACTIONS.TOAST_ADD:
      return [
        ...state,
        {
          id: +new Date(),
          content: action.payload.content,
          type: action.payload.type,
        },
      ];
    case MATTERPORT_ADMIN_ACTIONS.MATTERPORT_SAVE_REQUEST_SUCCESSED:
    case MATTERPORT_ADMIN_ACTIONS.MATTERPORT_UPDATE_REQUEST_SUCCESSED:
      return [
        ...state,
        {
          id: +new Date(),
          content: {
            header: "Notification",
            message: action.matter.message,
          },
        },
      ];
    case TOAST_ACTIONS.TOAST_REMOVE:
      return state.filter((t) => t.id !== action.id);
    case TOAST_ACTIONS.TOAST_REMOVE_ALL:
      return initialState;
    default:
      return state;
  }
}
