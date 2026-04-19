import React, { useCallback, useMemo, useState } from "react";
import { connect } from "react-redux";
import { useHistory } from "react-router-dom";

import {
  Box,
  Button,
  ColumnConfig,
  DataTable,
  Main,
  Text,
  TextInput,
} from "grommet";
import _ from "lodash";

import { RootState } from "models/store";
import routes from "src/navigation/routes";
import { IMatterport } from "types/matterport";
import {
  useAllMatterports,
  useRemoveMatterport,
} from "src/controllers/matterport";

type Props = ReturnType<typeof mapStateToProps>;

const PlanList = ({}: Props) => {
  const history = useHistory();
  const { data } = useAllMatterports();

  const removeMatterport = useRemoveMatterport();

  const [searchText, setSearchText] = useState<string>("");

  const plans = useMemo(() => {
    let i = 0;

    return _.map(data?.data ?? [], (plan, key) => {
      const width = _.get(plan, "maxX") - _.get(plan, "minX");
      const height = _.get(plan, "maxY") - _.get(plan, "minY");

      i++;
      return { ...plan, order: i, width, height };
    });
  }, [data]);

  const createModel = useCallback(
    (event) => {
      history.push(routes.REGISTERMATTERPORT);
    },
    [history]
  );

  const editPlan = (id: number) => {
    history.push(`${routes.REGISTERMATTERPORT}/${id}`);
  };

  const removePlan = (id: number) => {
    // store.dispatch(removeMatterport(id));
    removeMatterport.mutate(id);
  };

  const columns: ColumnConfig<IMatterport>[] = [
    {
      property: "order",
      primary: true,
      header: (
        <Text size="small" weight="bold">
          No
        </Text>
      ),
      render: (datum) => (
        <Box direction="row" gap="xsmall" overflow="auto">
          <Text size="small">{datum.order}</Text>
        </Box>
      ),
    },
    {
      property: "mat",
      primary: true,
      header: (
        <Text size="small" weight="bold">
          Matterport sid
        </Text>
      ),
      render: (datum) => (
        <Box direction="row" gap="xsmall" overflow="auto">
          <Text size="small">{datum.mat}</Text>
        </Box>
      ),
    },
    {
      property: "name",
      primary: true,
      header: (
        <Text size="small" weight="bold">
          Matterport Name
        </Text>
      ),
      render: (datum) => (
        <Box direction="row" gap="xsmall" overflow="auto">
          <Text size="small">{datum.name}</Text>
        </Box>
      ),
    },
    {
      property: "width",
      header: (
        <Text size="small" weight="bold">
          Width
        </Text>
      ),
      render: (datum) => (
        <Box direction="row" gap="xsmall" overflow="auto">
          <Text size="small">{datum.width}</Text>
        </Box>
      ),
    },
    {
      property: "height",
      header: (
        <Text size="small" weight="bold">
          Height
        </Text>
      ),
      render: (datum) => (
        <Box direction="row" gap="xsmall" overflow="auto">
          <Text size="small">{datum.height}</Text>
        </Box>
      ),
    },
    {
      property: "Base Elevation",
      header: (
        <Text size="small" weight="bold">
          Base Elevation
        </Text>
      ),
      render: (datum) => (
        <Box direction="row" gap="xsmall" overflow="auto">
          <Text size="small">{datum.baseElevation}</Text>
        </Box>
      ),
    },
    {
      property: "regDate",
      header: (
        <Text size="small" weight="bold">
          Register Date
        </Text>
      ),
      render: (datum) => (
        <Box direction="row" gap="xsmall" overflow="auto">
          <Text size="small">{datum.creation_date}</Text>
        </Box>
      ),
    },
    {
      property: "action",
      header: (
        <Text size="small" weight="bold">
          Control
        </Text>
      ),
      render: (datum) => (
        <Box direction="row" gap="xsmall" overflow="auto">
          <Button
            key="edit"
            label="Review"
            primary
            onClick={() => goReview(datum.mat)}
            style={{ background: "#df4f4f" }}
          />
          <Button
            key="edit"
            label="Edit"
            primary
            onClick={() => editPlan(datum.id)}
          />
          <Button
            key="remove"
            label="Remove"
            primary
            onClick={() => removePlan(datum.id)}
          />
        </Box>
      ),
    },
  ];

  const goReview = (sid: string) => {
    window.open(`https://my.matterport.com/show/?m=${sid}`, "_blank");
  };

  return (
    <Main direction="column" pad="0px">
      <Box
        border="bottom"
        pad="small"
        align="center"
        flex={{ shrink: 0 }}
        background="#2148C0"
      >
        <Text>Registered Plans</Text>
      </Box>
      <Box
        flex={{ shrink: 0 }}
        direction="row"
        style={{ background: "white", borderBottom: "2px solid grey" }}
      >
        <Box flex={{ grow: 1 }}></Box>
        <Box justify="center" pad={{ right: "5px" }}>
          <Text size="small">Search Matterport:</Text>
        </Box>
        <Box justify="center">
          <TextInput
            placeholder="Search..."
            value={searchText}
            onChange={(e) => setSearchText(e.target.value)}
          />
        </Box>
      </Box>
      <Box flex overflow="auto" background="white">
        <DataTable
          columns={columns}
          data={plans.filter((d) => (d.mat as string).includes(searchText))}
          border={{ body: "bottom" }}
          pin
          style={{ height: "auto" }}
        />
      </Box>
      <Box
        direction="row"
        justify="end"
        pad="small"
        flex={{ shrink: 0 }}
        background="#2148C0"
      >
        <Button
          label="Register New Matterport"
          primary
          // color={colors.AQUA_MARINE}
          onClick={createModel}
        />
      </Box>
    </Main>
  );
};

const mapStateToProps = (state: RootState) => ({
  // plans: state.plans,
});

export default connect(mapStateToProps, {})(PlanList);
