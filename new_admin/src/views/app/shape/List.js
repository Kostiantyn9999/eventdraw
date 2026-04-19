import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import { getShapeList, deleteShape, resetShape } from "reduxs/actions";
import AddIcon from "@mui/icons-material/Add";
import {
  StyledCard,
  BreadcrumbContainer,
  TableInstance,
  Table,
  TablePagination,
  Toolbar,
  Action,
  AlertDialog,
} from "ui";

const ShapeList = (props) => {
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const [pageValue, setPageValue] = useState(1);
  const [perPage, setPerPage] = useState(10);
  const [sortOrder, setSortOrder] = useState("desc");
  const [activeCol, setActiveCol] = useState("id");
  const [typingTimeout, setTypingTimeout] = useState(0);
  const [search, setSearch] = useState("");
  const [openDeleteAlert, setOpenDeleteAlert] = useState(false);
  const [deleteId, setDeleteId] = useState(null);
  const [metaData, setMetaData] = useState({
    totalPages: 0,
    page: pageValue,
    total: 0,
    from: 1,
    to: perPage
  });
  const [data, setData] = useState([]);
  const { shapeList, delLoading, loading, success } = useSelector((state) => state.shape);

  const sortFunc = (a, b, sort, col) => {
    const c = ["elevate", "height"].includes(col) ? parseFloat(a[col]) : a[col];
    const d = ["elevate", "height"].includes(col) ? parseFloat(b[col]) : b[col];
    if (c > d) {
      return sort === "desc" ? -1 : 1;
    } else if (c < d) {
      return sort === "desc" ? 1 : -1;
    } else {
      return 0;
    }
  };

  const onChange = (search, sort, page, perPage, col) => {
    const filteredList = shapeList.sort((a, b) => sortFunc(a, b, sort, col)).filter((item) => {
      if (!search) return true;
      for (const key in item) {
        if (typeof item[key] === "string" && item[key].includes(search)) {
          return true;
        }
      }
      return false;
    });
    const total = filteredList.length;
    const totalPages = Math.ceil(total / perPage);
    const from = (page - 1) * perPage + 1;
    const to = (page - 1) * perPage + perPage < total ? (page - 1) * perPage + perPage : total;
    setMetaData({ total, page, totalPages, from, to });
    setData([...filteredList.slice((page - 1) * perPage, page * perPage)]);
  };

  const handleSort = (order, val) => {
    setSortOrder(order);
    setActiveCol(val);
    onChange(search, order, 1, perPage, val);
  };

  const handleChangePage = (val) => {
    setPageValue(val);
    onChange(search, sortOrder, val, perPage, activeCol);
  };

  const handleChangePerPage = (val) => {
    setPerPage(val);
    onChange(search, sortOrder, 1, val, activeCol);
  };

  const handleSearch = (event) => {
    event.preventDefault();
    const val = event.target.value;
    setSearch(event.target.value);
    if (typingTimeout) {
      clearTimeout(typingTimeout);
    }
    setTypingTimeout(
      setTimeout(function () {
        onChange(val, sortOrder, 1, perPage, activeCol);
      }, 1000)
    );
  };

  const handleEditClick = (e, id) => {
    e.preventDefault();
    navigate(`${process.env.PUBLIC_URL}/shape/edit/${id}`);
  };

  const handleOnDelete = () => {
    if (!delLoading && deleteId) dispatch(deleteShape(deleteId));
  };

  const columns = React.useMemo(() => [
    {
      Header: "Category",
      accessor: "category",
    },
    {
      Header: "Shape Type",
      accessor: "shapeType",
    },
    {
      Header: "Elevate",
      accessor: "elevate",
    },
    {
      Header: "Height",
      accessor: "height",
    },
    {
      Header: "Actions",
      accessor: "actions",
      disableSortBy: true,
      Cell: (props) => {
        const rowIdx = props.row.original.id;

        return (
          <Action
            id={props.row.id}
            handleOnDelete={() => {
              setDeleteId(rowIdx);
              setOpenDeleteAlert(true);
            }}
            handleOnEdit={(e) => handleEditClick(e, rowIdx)}
            {...props}
          />
        );
      },
    },
  ]);

  useEffect(() => {
    dispatch(getShapeList());
  }, []);

  useEffect(() => {
    if (!shapeList) return;
    const to = metaData.to < shapeList.length ? metaData.to : shapeList.length;
    setMetaData({ ...metaData, total: shapeList.length, totalPages: Math.ceil(shapeList.length / perPage), to: to });
    setData([...shapeList.sort((a, b) => sortFunc(a, b, sortOrder, activeCol)).slice(0, perPage)]);
  }, [shapeList]);

  return (
    <>
      <BreadcrumbContainer
        title="Shape List"
        paths={[
          {
            title: "Shape",
            path: `${process.env.PUBLIC_URL}/shape`,
          },
        ]}
      />

      <StyledCard>
        <TableInstance
          columns={columns}
          permission={{ edit: true, delete: true }}
          data={data || []}
        >
          <Toolbar
            title="Shape"
            subTitle="List of all available shape"
            buttonA="Add Shape"
            allowButtonA={true}
            buttonAIcon={<AddIcon />}
            handleButtonA={() => navigate(`${process.env.PUBLIC_URL}/shape/add`)}
            search={search}
            handleSearch={handleSearch}
          />

          <Table handleSort={handleSort} loading={loading} />

          <TablePagination
            meta={metaData}
            goToStart={() => handleChangePage(1)}
            goToPrev={() => handleChangePage(pageValue === 0 ? 1 : pageValue - 1)}
            goToNext={() => handleChangePage(pageValue !== metaData?.totalPages ? pageValue + 1 : 1)}
            goToLast={() => handleChangePage(metaData?.totalPages)}
            handleChangePerPage={(val) => handleChangePerPage(val)}
            perPage={perPage}
            handleChangePage={handleChangePage}
          />
        </TableInstance>
      </StyledCard>

      <AlertDialog
        open={openDeleteAlert}
        handleCancel={() => {
          setOpenDeleteAlert(false);
          setDeleteId(null);
        }}
        handleAction={handleOnDelete}
        title="Delete"
        info="Are you sure to delete selected shape?"
        loadingInfo="Shape is deleting..."
        actionLabel="Delete"
        loading={delLoading}
        success={success}
        reset={() => dispatch(resetShape())}
      />
    </>
  );
};

export default ShapeList;
