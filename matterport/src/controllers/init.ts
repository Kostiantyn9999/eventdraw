import { fetchModels } from "./models"
import { fetchPlans } from "./plans"

async function init () {
    fetchModels();
    fetchPlans();
}

export function appInitialized () {
    return async function (dispatch, getState) {
        await init()
    }
}