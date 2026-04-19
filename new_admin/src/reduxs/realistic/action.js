export const GET_ALL_REALISTIC = "GET_ALL_REALISTIC";
export const GET_ALL_REALISTIC_SUCCESS = "GET_ALL_REALISTIC_SUCCESS";
export const GET_ALL_REALISTIC_ERROR = "GET_ALL_REALISTIC_ERROR";
export const GET_ALL_EVENT = "GET_ALL_EVENT";
export const GET_ALL_EVENT_SUCCESS = "GET_ALL_EVENT_SUCCESS";
export const GET_ALL_EVENT_ERROR = "GET_ALL_EVENT_ERROR";
export const GET_ALL_TEMPLATE = "GET_ALL_TEMPLATE";
export const GET_ALL_TEMPLATE_SUCCESS = "GET_ALL_TEMPLATE_SUCCESS";
export const GET_ALL_TEMPLATE_ERROR = "GET_ALL_TEMPLATE_ERROR";
export const GET_REALISTIC_LIST = "GET_REALISTIC_LIST";
export const GET_REALISTIC_LIST_SUCCESS = "GET_REALISTIC_LIST_SUCCESS";
export const GET_REALISTIC_LIST_ERROR = "GET_REALISTIC_LIST_ERROR";
export const ADD_REALISTIC = "ADD_REALISTIC";
export const ADD_REALISTIC_SUCCESS = "ADD_REALISTIC_SUCCESS";
export const ADD_REALISTIC_ERROR = "ADD_REALISTIC_ERROR";
export const GET_REALISTIC = "GET_REALISTIC";
export const GET_REALISTIC_SUCCESS = "GET_REALISTIC_SUCCESS";
export const GET_REALISTIC_ERROR = "GET_REALISTIC_ERROR";
export const EDIT_REALISTIC = "EDIT_REALISTIC";
export const EDIT_REALISTIC_SUCCESS = "EDIT_REALISTIC_SUCCESS";
export const EDIT_REALISTIC_ERROR = "EDIT_REALISTIC_ERROR";
export const DELETE_REALISTIC = "DELETE_REALISTIC";
export const DELETE_REALISTIC_SUCCESS = "DELETE_REALISTIC_SUCCESS";
export const DELETE_REALISTIC_ERROR = "DELETE_REALISTIC_ERROR";
export const RESET_REALISTIC = "RESET_REALISTIC";

export const getAllRealistic = () => ({
  type: GET_ALL_REALISTIC,
  payload: {},
});

export const getAllRealisticSuccess = (realistics) => ({
  type: GET_ALL_REALISTIC_SUCCESS,
  payload: { realistics },
});

export const getAllRealisticError = (error) => ({
  type: GET_ALL_REALISTIC_ERROR,
  payload: error,
});

export const getAllEvent = (data) => ({
  type: GET_ALL_EVENT,
  payload: { data },
});

export const getAllEventSuccess = (events) => ({
  type: GET_ALL_EVENT_SUCCESS,
  payload: { events },
});

export const getAllEventError = (error) => ({
  type: GET_ALL_EVENT_ERROR,
  payload: error,
});

export const getAllTemplate = (data) => ({
  type: GET_ALL_TEMPLATE,
  payload: { data },
});

export const getAllTemplateSuccess = (templates) => ({
  type: GET_ALL_TEMPLATE_SUCCESS,
  payload: { templates },
});

export const getAllTemplateError = (error) => ({
  type: GET_ALL_TEMPLATE_ERROR,
  payload: error,
});

export const getRealisticList = () => ({
  type: GET_REALISTIC_LIST,
  payload: {},
});

export const getRealisticListSuccess = (realisticList) => ({
  type: GET_REALISTIC_LIST_SUCCESS,
  payload: { realisticList },
});

export const getRealisticListError = (error) => ({
  type: GET_REALISTIC_LIST_ERROR,
  payload: error,
});

export const addRealistic = (realisticData, navigate) => ({
  type: ADD_REALISTIC,
  payload: { realisticData, navigate },
});

export const addRealisticSuccess = (success) => ({
  type: ADD_REALISTIC_SUCCESS,
  payload: success,
});

export const addRealisticError = (error) => ({
  type: ADD_REALISTIC_ERROR,
  payload: error,
});

export const getRealistic = (realisticId) => ({
  type: GET_REALISTIC,
  payload: { realisticId },
});

export const getRealisticSuccess = (realisticData) => ({
  type: GET_REALISTIC_SUCCESS,
  payload: realisticData,
});

export const getRealisticError = (error) => ({
  type: GET_REALISTIC_ERROR,
  payload: error,
});

export const editRealistic = (realisticId, realisticData, navigate) => ({
  type: EDIT_REALISTIC,
  payload: { realisticId, realisticData, navigate },
});

export const editRealisticSuccess = (success) => ({
  type: EDIT_REALISTIC_SUCCESS,
  payload: success,
});

export const editRealisticError = (error) => ({
  type: EDIT_REALISTIC_ERROR,
  payload: error,
});

export const deleteRealistic = (realisticId) => ({
  type: DELETE_REALISTIC,
  payload: { realisticId },
});

export const deleteRealisticSuccess = (success) => ({
  type: DELETE_REALISTIC_SUCCESS,
  payload: success,
});

export const deleteRealisticError = (error) => ({
  type: DELETE_REALISTIC_ERROR,
  payload: error,
});

export const resetRealistic = () => ({
  type: RESET_REALISTIC,
  payload: {},
});
