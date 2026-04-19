import React, { createContext, useContext, useReducer } from "react";
import Toast from "./ToastWithContext";

export const ToastContext = createContext();

export const ADD = "ADD";
export const REMOVE = "REMOVE";
export const REMOVE_ALL = "REMOVE_ALL";

const initialState = [];

export const toastReducer = (state, action) => {
  switch (action.type) {
    case ADD:
      return [
        ...state,
        {
          id: +new Date(),
          content: action.payload.content,
          type: action.payload.type,
        },
      ];
    case REMOVE:
      return state.filter((t) => t.id !== action.payload.id);
    case REMOVE_ALL:
      return initialState;
    default:
      return state;
  }
};

export const ToastProvider = (props) => {
  const [toast, toastDispatch] = useReducer(toastReducer, initialState);
  const toastData = { toast, toastDispatch };

  return (
    <ToastContext.Provider value={toastData}>
      {props.children}
      {<Toast toast={toast} />}
    </ToastContext.Provider>
  );
};

export const useToastContext = () => {
  return useContext(ToastContext);
};
