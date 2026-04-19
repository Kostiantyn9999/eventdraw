import { createSelector } from "reselect";
import _ from "lodash";

const getFloor = (state) => _.get(state, "floor");
const getModels = (state) => _.get(state, "models");

export const getAvailableModels = createSelector(
  [getFloor, getModels],
  (floor, models) => {

    if (_.isNil(floor) || _.isNil(models)) return null;
    
    const availableModel = _.filter(models, (model) =>
      _.some(floor.shapes, (shape) => {
        return model.shapeType.trim() == shape.name.trim() || model.shapeType == "chair";
      })
    );

    return availableModel;
  }
);
