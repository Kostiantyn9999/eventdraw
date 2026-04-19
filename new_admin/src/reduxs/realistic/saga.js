import { all, call, fork, put, takeEvery } from "redux-saga/effects";
import { GET_ALL_REALISTIC, GET_ALL_EVENT, GET_ALL_TEMPLATE, GET_REALISTIC_LIST, ADD_REALISTIC, GET_REALISTIC, EDIT_REALISTIC, DELETE_REALISTIC } from "../actions";
import {
  getAllRealisticSuccess,
  getAllRealisticError,
  getAllEventSuccess,
  getAllEventError,
  getAllTemplateSuccess,
  getAllTemplateError,
  getAllRealistic,
  getRealisticListSuccess,
  getRealisticListError,
  addRealisticSuccess,
  addRealisticError,
  getRealisticSuccess,
  getRealisticError,
  editRealisticSuccess,
  editRealisticError,
  deleteRealisticSuccess,
  deleteRealisticError,
} from "./action";
import { toaster, parseMessage, handleResponseErrorMessage } from "helpers";
import RealisticService from "services/RealisticService";

export function* watchgetAllRealistic() {
  yield takeEvery(GET_ALL_REALISTIC, getAllRealisticA);
}

const getAllRealisticAsync = async () => {
  return RealisticService.getAllRealistic();
};

function* getAllRealisticA() {
  try {
    const response = yield call(getAllRealisticAsync);
    if (response.data) {
      const list = [];
      response.data.forEach((item) => {
        /* eslint-disable-next-line */
        list.push({ ...item, screenshot: <img width={100} src={`${process.env.REACT_APP_API_URL_1}/${item.screenshot}`} /> });
      });
      yield put(getAllRealisticSuccess(list));
    } else {
      toaster("", response.data.message);
      yield put(getAllRealisticError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getAllRealisticError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchgetAllEvent() {
  yield takeEvery(GET_ALL_EVENT, getAllEvent);
}

const getAllEventAsync = async (data) => {
  return RealisticService.getAllEvent(data);
};

function* getAllEvent({ payload }) {
  try {
    const { data } = payload;
    const response = yield call(getAllEventAsync, data);
    if (response.data) {
      yield put(getAllEventSuccess(response.data));
    } else {
      toaster("", response.data.message);
      yield put(getAllEventError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getAllEventError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchgetAllTemplate() {
  yield takeEvery(GET_ALL_TEMPLATE, getAllTemplate);
}

const getAllTemplateAsync = async (data) => {
  return RealisticService.getAllTemplate(data);
};

function* getAllTemplate({ payload }) {
  try {
    const { data } = payload;
    const response = yield call(getAllTemplateAsync, data);
    if (response.data) {
      yield put(getAllTemplateSuccess(response.data));
    } else {
      toaster("", response.data.message);
      yield put(getAllTemplateError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getAllTemplateError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetRealisticList() {
  yield takeEvery(GET_REALISTIC_LIST, getRealisticListAc);
}

const getRealisticListAsync = async () => {
  return RealisticService.getAllRealistic();
};

function* getRealisticListAc({ payload }) {
  try {
    const response = yield call(getRealisticListAsync);

    if (response.data.success) {
      yield put(getRealisticListSuccess(response.data.data));
    } else {
      toaster("", response.data.message);
      yield put(getRealisticListError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getRealisticListError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchAddRealistic() {
  yield takeEvery(ADD_REALISTIC, addRealistic);
}

const addRealisticAsync = async (data) => {
  return RealisticService.addRealistic(data);
};

function* addRealistic({ payload }) {
  const { navigate } = payload;
  try {
    const response = yield call(addRealisticAsync, payload.realisticData);
    if (response.data) {
      toaster("success", response.data.message);
      yield put(addRealisticSuccess(true));
      navigate(`${process.env.PUBLIC_URL}/realistic`);
    } else {
      toaster("", response.data.message);
      yield put(addRealisticError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(addRealisticError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetRealistic() {
  yield takeEvery(GET_REALISTIC, getRealistic);
}

const getRealisticAsync = async (id) => {
  return RealisticService.getRealistic(id);
};

function* getRealistic({ payload }) {
  try {
    if (!payload.realisticId) {
      yield put(getRealisticSuccess(null));
      return;
    }
    const response = yield call(getRealisticAsync, payload.realisticId);
    if (response.data) {
      yield put(getRealisticSuccess(response.data));
    } else {
      toaster("", response.data.message);
      yield put(getRealisticError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(getRealisticError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchEditRealistic() {
  yield takeEvery(EDIT_REALISTIC, editRealistic);
}

const editRealisticAsync = async (data, id) => {
  return RealisticService.editRealistic(data, id);
};

function* editRealistic({ payload }) {
  const { navigate } = payload;
  try {
    const response = yield call(editRealisticAsync, payload.realisticData, payload.realisticId);
    if (response.data) {
      toaster("success", response.data.message);
      yield put(editRealisticSuccess(true));
      navigate(`${process.env.PUBLIC_URL}/realistic`);
    } else {
      toaster("", response.data.message);
      yield put(editRealisticError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(editRealisticError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchDeleteRealistic() {
  yield takeEvery(DELETE_REALISTIC, deleteRealistic);
}

const deleteRealisticAsync = async (id) => {
  return RealisticService.deleteRealistic(id);
};

function* deleteRealistic({ payload }) {
  try {
    const response = yield call(deleteRealisticAsync, payload.realisticId);
    if (response.data) {
      toaster("success", "Successfully Deleted!");
      yield put(deleteRealisticSuccess(true));
      yield put(getAllRealistic());
    } else {
      toaster("", response.data.message);
      yield put(deleteRealisticError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(deleteRealisticError(errMessage));
    toaster("error", errMessage);
  }
}

export default function* rootSaga() {
  yield all([
    fork(watchgetAllEvent),
    fork(watchgetAllTemplate),
    fork(watchgetAllRealistic),
    fork(watchGetRealisticList),
    fork(watchAddRealistic),
    fork(watchGetRealistic),
    fork(watchEditRealistic),
    fork(watchDeleteRealistic),
  ]);
}
