import _ from "lodash";

import {
  all,
  fork,
  call,
  put,
  takeEvery,
  takeLatest,
} from "redux-saga/effects";

import {
  getMatterport,
  getMatterportAll,
  saveMatterport,
  updateMatterport,
  removeMatterport,
} from "src/controllers/matterport";

import {
  MATTERPORT_ADMIN_ACTIONS,
  MATTERPORT_FRONT_ACTIONS,
} from "../../actionTypes";

function* fetchMatterport(action) {
  const { id } = action;
  try {
    const matterport = yield call(getMatterport, ...[id]);
    yield put({
      type: MATTERPORT_FRONT_ACTIONS.MATTERPORT_FETCH_SUCCESSED,
      matterport: matterport,
    });
  } catch (e) {
    yield put({
      type: MATTERPORT_FRONT_ACTIONS.MATTERPORT_FETCH_FAILED,
      message: e.message,
    });
  }
}

function* fetchAllMatterport(action) {
  try {
    const matterports = yield call(getMatterportAll);
    yield put({
      type: MATTERPORT_FRONT_ACTIONS.MATTERPORT_ALL_FETCH_SUCCESSED,
      matterports: _.keyBy(matterports.data, "id"),
    });
  } catch (e) {}
}

function* sendMatterport(action) {
  const { matterport } = action;

  try {
    const res = yield call(saveMatterport, ...[matterport]);
    yield put({
      type: MATTERPORT_ADMIN_ACTIONS.MATTERPORT_SAVE_REQUEST_SUCCESSED,
      matter: res.data,
    });
  } catch (e: any) {
    yield put({
      type: MATTERPORT_ADMIN_ACTIONS.MATTERPORT_SAVE_REQUEST_FAILED,
      message: e.message,
    });
  }
}

function* sendUpdateMatterportData(action) {
  const { id, matterport } = action;

  try {
    const res = yield call(updateMatterport, ...[id, matterport]);
    yield put({
      type: MATTERPORT_ADMIN_ACTIONS.MATTERPORT_UPDATE_REQUEST_SUCCESSED,
      matter: res.data,
    });
  } catch (e: any) {
    yield put({
      type: MATTERPORT_ADMIN_ACTIONS.MATTERPORT_UPDATE_REQUEST_FAILED,
      message: e.message,
    });
  }
}

function* sendRequestRemoveMatterport(action) {
  const { id } = action;

  try {
    const res = yield call(removeMatterport, ...[id]);
    yield put({
      type: MATTERPORT_ADMIN_ACTIONS.MATTERPORT_REMOVE_REQUEST_SUCCESSED,
      matter: res,
    });
  } catch (e) {
    yield put({
      type: types.MATTERPORT_REMOVE_REQUEST_FAILED,
      message: e.message,
    });
  }
}

export function* fetchMatterportById() {
  yield takeEvery(
    MATTERPORT_FRONT_ACTIONS.MATTERPORT_FETCH_REQUESTED,
    fetchMatterport
  );
}

export function* fetchMatterportAll() {
  yield takeEvery(
    MATTERPORT_FRONT_ACTIONS.MATTERPORT_ALL_FETCH_REQUESTED,
    fetchAllMatterport
  );
}

export function* sendNewMatterport() {
  yield takeEvery(
    MATTERPORT_ADMIN_ACTIONS.MATTERPORT_SAVE_REQUEST,
    sendMatterport
  );
}

export function* sendUpdateMatterport() {
  yield takeEvery(
    MATTERPORT_ADMIN_ACTIONS.MATTERPORT_UPDATE_REQUEST,
    sendUpdateMatterportData
  );
}

export function* removeExistMatterport() {
  yield takeEvery(
    MATTERPORT_ADMIN_ACTIONS.MATTERPORT_REMOVE_REQUEST,
    sendRequestRemoveMatterport
  );
}

export default function* rootSaga() {
  yield all([
    fork(fetchMatterportById),
    fork(fetchMatterportAll),
    fork(sendNewMatterport),
    fork(sendUpdateMatterport),
    fork(removeExistMatterport),
  ]);
}
