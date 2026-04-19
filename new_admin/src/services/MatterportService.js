import Req from "interceptors/TokenInterceptor";

const MatterportService = (function () {
  function _getAllMatterport() {
    return Req.get(`${process.env.REACT_APP_API_URL_2}/matterport-list?t=${Date.now().toString(36)}`);
  }
  function _addMatterport(data) {
    return Req.post(`${process.env.REACT_APP_API_URL_2}/matterport-save?t=${Date.now().toString(36)}`, data, {
      headers: {
        "Content-Type": "multipart/form-data",
      }
    });
  }
  function _getMatterport(id) {
    return Req.get(`${process.env.REACT_APP_API_URL_2}/matterport-get?id=${id}&t=${Date.now().toString(36)}`);
  }
  function _editMatterport(data, id) {
    return Req.post(`${process.env.REACT_APP_API_URL_2}/matterport-update?t=${Date.now().toString(36)}`, { ...data, id: id }, {
      headers: {
        "Content-Type": "multipart/form-data",
      }
    });
  }
  function _deleteMatterport(id) {
    return Req.get(`${process.env.REACT_APP_API_URL_2}/matterport-delete?id=${id}&t=${Date.now().toString(36)}`);
  }
  return {
    getAllMatterport: _getAllMatterport,
    addMatterport: _addMatterport,
    getMatterport: _getMatterport,
    editMatterport: _editMatterport,
    deleteMatterport: _deleteMatterport,
  };
})();
export default MatterportService;
