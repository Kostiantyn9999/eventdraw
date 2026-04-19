import { all, call, fork, put, takeEvery } from "redux-saga/effects";
import { GET_ALL_MATTERPORT, GET_MATTERPORT_LIST, ADD_MATTERPORT, GET_MATTERPORT, EDIT_MATTERPORT, DELETE_MATTERPORT } from "../actions";
import {
  getAllMatterportSuccess,
  getAllMatterportError,
  getMatterportList,
  getMatterportListSuccess,
  getMatterportListError,
  addMatterportSuccess,
  addMatterportError,
  getMatterportSuccess,
  getMatterportError,
  editMatterportSuccess,
  editMatterportError,
  deleteMatterportSuccess,
  deleteMatterportError,
} from "./action";
import { toaster, parseMessage, handleResponseErrorMessage } from "helpers";
import MatterportService from "services/MatterportService";

export function* watchgetAllMatterport() {
  yield takeEvery(GET_ALL_MATTERPORT, getAllMatterport);
}

const getAllMatterportAsync = async () => {
  return MatterportService.getAllMatterport();
};

function* getAllMatterport() {
  try {
    const response = yield call(getAllMatterportAsync);
    if (response.data) {
      yield put(getAllMatterportSuccess(response.data));
    } else {
      toaster("", response.data.message);
      yield put(getAllMatterportError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getAllMatterportError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetMatterportList() {
  yield takeEvery(GET_MATTERPORT_LIST, getMatterportListAc);
}

const getMatterportListAsync = async () => {
  return MatterportService.getAllMatterport();
};

function* getMatterportListAc({ payload }) {
  try {
    const response = yield call(getMatterportListAsync);

    const newData = [];
    response.data.forEach((item) => {
      newData.push({ ...item, minX: item.minX.toFixed(2), maxX: item.maxX.toFixed(2), minY: item.minY.toFixed(2), maxY: item.maxY.toFixed(2) });
    });
    if (response.data) {
      yield put(getMatterportListSuccess(newData));
    } else {
      toaster("", response.data.message);
      yield put(getMatterportListError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getMatterportListError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchAddMatterport() {
  yield takeEvery(ADD_MATTERPORT, addMatterport);
}

const addMatterportAsync = async (data) => {
  return MatterportService.addMatterport(data);
};

function* addMatterport({ payload }) {
  const { navigate } = payload;
  try {
    const response = yield call(addMatterportAsync, payload.matterportData);
    if (response.data.state !== "failed") {
      toaster("success", response.data.message);
      yield put(addMatterportSuccess(true));
      navigate(`${process.env.PUBLIC_URL}/matterport`);
    } else {
      toaster("error", response.data.message);
      yield put(addMatterportError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(addMatterportError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetMatterport() {
  yield takeEvery(GET_MATTERPORT, getMatterport);
}

const getMatterportAsync = async (id) => {
  return MatterportService.getMatterport(id);
};

function* getMatterport({ payload }) {
  try {
    const response = yield call(getMatterportAsync, payload.matterportId);
    if (response.data) {
      yield put(getMatterportSuccess(response.data));
    } else {
      toaster("", response.data.message);
      yield put(getMatterportError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(getMatterportError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchEditMatterport() {
  yield takeEvery(EDIT_MATTERPORT, editMatterport);
}

const editMatterportAsync = async (data, id) => {
  return MatterportService.editMatterport(data, id);
};

function* editMatterport({ payload }) {
  const { navigate } = payload;
  try {
    const response = yield call(editMatterportAsync, payload.matterportData, payload.matterportId);
    if (response.data) {
      toaster("success", response.data.message);
      yield put(editMatterportSuccess(true));
      navigate(`${process.env.PUBLIC_URL}/matterport`);
    } else {
      toaster("", response.data.message);
      yield put(editMatterportError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(editMatterportError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchDeleteMatterport() {
  yield takeEvery(DELETE_MATTERPORT, deleteMatterport);
}

const deleteMatterportAsync = async (id) => {
  return MatterportService.deleteMatterport(id);
};

function* deleteMatterport({ payload }) {
  try {
    const response = yield call(deleteMatterportAsync, payload.matterportId);
    if (response.data) {
      toaster("success", "Successfully Deleted!");
      yield put(deleteMatterportSuccess(true));
      yield put(getMatterportList());
    } else {
      toaster("", response.data.message);
      yield put(deleteMatterportError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(deleteMatterportError(errMessage));
    toaster("error", errMessage);
  }
}

export default function* rootSaga() {
  yield all([
    fork(watchgetAllMatterport),
    fork(watchGetMatterportList),
    fork(watchAddMatterport),
    fork(watchGetMatterport),
    fork(watchEditMatterport),
    fork(watchDeleteMatterport),
  ]);
}
