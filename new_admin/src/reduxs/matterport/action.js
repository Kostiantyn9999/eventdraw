export const GET_ALL_MATTERPORT = "GET_ALL_MATTERPORT";
export const GET_ALL_MATTERPORT_SUCCESS = "GET_ALL_MATTERPORT_SUCCESS";
export const GET_ALL_MATTERPORT_ERROR = "GET_ALL_MATTERPORT_ERROR";
export const GET_MATTERPORT_LIST = "GET_MATTERPORT_LIST";
export const GET_MATTERPORT_LIST_SUCCESS = "GET_MATTERPORT_LIST_SUCCESS";
export const GET_MATTERPORT_LIST_ERROR = "GET_MATTERPORT_LIST_ERROR";
export const ADD_MATTERPORT = "ADD_MATTERPORT";
export const ADD_MATTERPORT_SUCCESS = "ADD_MATTERPORT_SUCCESS";
export const ADD_MATTERPORT_ERROR = "ADD_MATTERPORT_ERROR";
export const GET_MATTERPORT = "GET_MATTERPORT";
export const GET_MATTERPORT_SUCCESS = "GET_MATTERPORT_SUCCESS";
export const GET_MATTERPORT_ERROR = "GET_MATTERPORT_ERROR";
export const EDIT_MATTERPORT = "EDIT_MATTERPORT";
export const EDIT_MATTERPORT_SUCCESS = "EDIT_MATTERPORT_SUCCESS";
export const EDIT_MATTERPORT_ERROR = "EDIT_MATTERPORT_ERROR";
export const DELETE_MATTERPORT = "DELETE_MATTERPORT";
export const DELETE_MATTERPORT_SUCCESS = "DELETE_MATTERPORT_SUCCESS";
export const DELETE_MATTERPORT_ERROR = "DELETE_MATTERPORT_ERROR";
export const RESET_MATTERPORT = "RESET_MATTERPORT";

export const getAllMatterport = () => ({
  type: GET_ALL_MATTERPORT,
  payload: {},
});

export const getAllMatterportSuccess = (matterports) => ({
  type: GET_ALL_MATTERPORT_SUCCESS,
  payload: matterports,
});

export const getAllMatterportError = (error) => ({
  type: GET_ALL_MATTERPORT_ERROR,
  payload: error,
});

export const getMatterportList = () => ({
  type: GET_MATTERPORT_LIST,
  payload: {},
});

export const getMatterportListSuccess = (matterportList) => ({
  type: GET_MATTERPORT_LIST_SUCCESS,
  payload: { matterportList },
});

export const getMatterportListError = (error) => ({
  type: GET_MATTERPORT_LIST_ERROR,
  payload: error,
});

export const addMatterport = (matterportData, navigate) => ({
  type: ADD_MATTERPORT,
  payload: { matterportData, navigate },
});

export const addMatterportSuccess = (success) => ({
  type: ADD_MATTERPORT_SUCCESS,
  payload: success,
});

export const addMatterportError = (error) => ({
  type: ADD_MATTERPORT_ERROR,
  payload: error,
});

export const getMatterport = (matterportId) => ({
  type: GET_MATTERPORT,
  payload: { matterportId },
});

export const getMatterportSuccess = (matterportData) => ({
  type: GET_MATTERPORT_SUCCESS,
  payload: matterportData,
});

export const getMatterportError = (error) => ({
  type: GET_MATTERPORT_ERROR,
  payload: error,
});

export const editMatterport = (matterportId, matterportData, navigate) => ({
  type: EDIT_MATTERPORT,
  payload: { matterportId, matterportData, navigate },
});

export const editMatterportSuccess = (success) => ({
  type: EDIT_MATTERPORT_SUCCESS,
  payload: success,
});

export const editMatterportError = (error) => ({
  type: EDIT_MATTERPORT_ERROR,
  payload: error,
});

export const deleteMatterport = (matterportId) => ({
  type: DELETE_MATTERPORT,
  payload: { matterportId },
});

export const deleteMatterportSuccess = (success) => ({
  type: DELETE_MATTERPORT_SUCCESS,
  payload: success,
});

export const deleteMatterportError = (error) => ({
  type: DELETE_MATTERPORT_ERROR,
  payload: error,
});

export const resetMatterport = () => ({
  type: RESET_MATTERPORT,
  payload: {},
});
