import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import { getAllRealistic, deleteRealistic, resetRealistic } from "reduxs/actions";
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

const RealisticList = (props) => {
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
  const { realistics, delLoading, loading, success } = useSelector((state) => state.realistic);

  const sortFunc = (a, b, sort, col) => {
    const c = [].includes(col) ? parseFloat(a[col]) : a[col];
    const d = [].includes(col) ? parseFloat(b[col]) : b[col];
    if (c > d) {
      return sort === "desc" ? -1 : 1;
    } else if (c < d) {
      return sort === "desc" ? 1 : -1;
    } else {
      return 0;
    }
  };

  const onChange = (search, sort, page, perPage, col) => {
    const filteredList = realistics.sort((a, b) => sortFunc(a, b, sort, col)).filter((item) => {
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
    navigate(`${process.env.PUBLIC_URL}/realistic/edit/${id}`);
  };

  const handleOnDelete = () => {
    if (!delLoading && deleteId) dispatch(deleteRealistic(deleteId));
  };

  const columns = React.useMemo(() => [
    {
      Header: "Name",
      accessor: "name",
    },
    {
      Header: "Description",
      accessor: "description",
    },
    {
      Header: "Screenshot",
      accessor: "screenshot",
      disableSortBy: true,
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
    dispatch(getAllRealistic());
  }, []);

  useEffect(() => {
    if (!realistics) return;
    const to = metaData.to < realistics.length ? metaData.to : realistics.length;
    setMetaData({ ...metaData, total: realistics.length, totalPages: Math.ceil(realistics.length / perPage), to: to });
    setData([...realistics.sort((a, b) => sortFunc(a, b, sortOrder, activeCol)).slice(0, perPage)]);
  }, [realistics]);

  return (
    <>
      <BreadcrumbContainer
        title="Realistic List"
        paths={[
          {
            title: "Realistic",
            path: `${process.env.PUBLIC_URL}/realistic`,
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
            title="Realistic"
            subTitle="List of all available realistic"
            buttonA="Add Realistic"
            allowButtonA={true}
            buttonAIcon={<AddIcon />}
            handleButtonA={() => navigate(`${process.env.PUBLIC_URL}/realistic/add`)}
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
        info="Are you sure to delete selected realistic?"
        loadingInfo="Realistic is deleting..."
        actionLabel="Delete"
        loading={delLoading}
        success={success}
        reset={() => dispatch(resetRealistic())}
      />
    </>
  );
};

export default RealisticList;
