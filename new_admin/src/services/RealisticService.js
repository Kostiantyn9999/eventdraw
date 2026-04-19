import Req from "interceptors/TokenInterceptor";

const RealisticService = (function () {
  function _getAllEvent(data) {
    return Req.post(`${process.env.REACT_APP_API_URL_2}/get-all-events?search=${data.search}&t=${Date.now().toString(36)}`);
  }
  function _getAllTemplate(data) {
    return Req.post(`${process.env.REACT_APP_API_URL_2}/get-all-templates?search=${data.search}&t=${Date.now().toString(36)}`);
  }
  function _getAllRealistic() {
    return Req.get(`${process.env.REACT_APP_API_URL_2}/realistics-list?t=${Date.now().toString(36)}`);
  }
  function _addRealistic(data) {
    return Req.post(`${process.env.REACT_APP_API_URL_2}/realistics-save?t=${Date.now().toString(36)}`, data, {
      headers: {
        "Content-Type": "multipart/form-data",
      }
    });
  }
  function _getRealistic(id) {
    return Req.get(`${process.env.REACT_APP_API_URL_2}/realistics-get?id=${id}&t=${Date.now().toString(36)}`);
  }
  function _editRealistic(data, id) {
    return Req.post(`${process.env.REACT_APP_API_URL_2}/realistics-update?t=${Date.now().toString(36)}`, { ...data, id: id }, {
      headers: {
        "Content-Type": "multipart/form-data",
      }
    });
  }
  function _deleteRealistic(id) {
    return Req.get(`${process.env.REACT_APP_API_URL_2}/realistics-delete?id=${id}&t=${Date.now().toString(36)}`);
  }
  return {
    getAllEvent: _getAllEvent,
    getAllTemplate: _getAllTemplate,
    getAllRealistic: _getAllRealistic,
    addRealistic: _addRealistic,
    getRealistic: _getRealistic,
    editRealistic: _editRealistic,
    deleteRealistic: _deleteRealistic,
  };
})();
export default RealisticService;
