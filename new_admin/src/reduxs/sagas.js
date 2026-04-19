import { all } from "redux-saga/effects";
import authSagas from "./auth/saga";
import shapeSagas from "./shape/saga";
import realisticSagas from "./realistic/saga";
import matterportSagas from "./matterport/saga";
import userSagas from "./user/saga";

export default function* rootSaga(getState) {
  yield all([
    authSagas(),
    shapeSagas(),
    realisticSagas(),
    matterportSagas(),
    userSagas(),
  ]);
}
