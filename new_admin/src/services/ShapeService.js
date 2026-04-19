import Req from "interceptors/TokenInterceptor";

const ShapeService = (function () {
  function _getAllShape() {
    return Req.get(`${process.env.REACT_APP_API_URL_1}/api/shapes`);
  }
  function _getAllCategory() {
    return Req.get("/api/category");
  }
  function _addShape(data) {
    return Req.post(`${process.env.REACT_APP_API_URL_1}/api/shapes`, data, {
      headers: {
        "Content-Type": "multipart/form-data",
      }
    });
  }
  function _getShape(id) {
    return Req.get(`${process.env.REACT_APP_API_URL_1}/api/shapes/${id}`);
  }
  function _editShape(data, id) {
    return Req.post(`${process.env.REACT_APP_API_URL_1}/api/shapes/${id}`, data, {
      headers: {
        "Content-Type": "multipart/form-data",
      }
    });
  }
  function _deleteShape(id) {
    return Req.delete(`${process.env.REACT_APP_API_URL_1}/api/shapes/${id}`);
  }
  return {
    getAllShape: _getAllShape,
    getAllCategory: _getAllCategory,
    addShape: _addShape,
    getShape: _getShape,
    editShape: _editShape,
    deleteShape: _deleteShape,
  };
})();
export default ShapeService;
