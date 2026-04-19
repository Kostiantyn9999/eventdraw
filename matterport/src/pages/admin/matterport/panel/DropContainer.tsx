import React, { useEffect, useState } from "react";
import { faSearch } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { Box, Button, Text, TextInput } from "grommet";

import { getEvents, getTemplates } from "src/controllers/auth";
import { IEvent, ITemplate } from "types/floor";

type Props = {
  setEvent: (v: IEvent | ITemplate, type: "event" | "template") => void;
};

const DropContainer = ({ setEvent }: Props) => {
  const [keyword, setKeyword] = useState<string>("Diag");
  const [searchType, setSearchType] = useState<"event" | "template">("event");
  const [filterKeyword, setFilterKeyword] = useState<string>("");
  const [events, setEvents] = useState<Array<IEvent | ITemplate>>([]);

  const getEvent = async () => {
    if (keyword.length > 0) {
      if (searchType === "event") {
        const res = await getEvents(keyword);
        setEvents(res);
      }

      if (searchType === "template") {
        const res = await getTemplates(keyword);
        setEvents(res);
      }
    }
  };

  useEffect(() => {
    setEvents([]);
  }, [searchType]);

  return (
    <Box width="medium">
      <Box flex direction="row">
        <TextInput
          placeholder="Input keyword"
          value={keyword}
          onChange={(e) => {
            setKeyword(e.target.value);
          }}
        />
        <Button
          onClick={getEvent}
          icon={
            <FontAwesomeIcon
              icon={faSearch as any}
              size={"1x"}
              color="#0000000"
            />
          }
        />
      </Box>
      <Box flex direction="row">
        <Box
          style={{
            padding: "2px 5px",
            background: `${searchType === "event" ? "black" : "white"}`,
            color: `${searchType === "event" ? "white" : "black"}`,
          }}
          onClick={() => {
            setSearchType("event");
          }}
        >
          <Text size="1x">Event</Text>
        </Box>
        <Box
          style={{
            padding: "2px 5px",
            background: `${searchType === "template" ? "black" : "white"}`,
            color: `${searchType === "template" ? "white" : "black"}`,
          }}
          onClick={() => {
            setSearchType("template");
          }}
        >
          <Text size="1x">Template</Text>
        </Box>
      </Box>
      <Box>
        <TextInput
          value={filterKeyword}
          onChange={(e) => {
            setFilterKeyword(e.target.value);
          }}
        />
      </Box>
      <Box overflow="auto" style={{ maxHeight: "400px" }}>
        {events
          .filter((e) => {
            // if (searchType === "event") {
            //   return (e as IEvent).eventName.includes(filterKeyword);
            // }

            // if (searchType === "template") {
            //   return (e as ITemplate).templateName.includes(filterKeyword);
            // }
            const str =
              (e as IEvent).eventName || (e as ITemplate).templateName;

            return str.includes(filterKeyword);
            // return false;
          })
          .map((e, i) => (
            <Box
              key={i}
              flex={false}
              border={{ side: "bottom", color: "#00000033" }}
              onClick={() => {
                setEvent(e, searchType);
              }}
            >
              <Text size="small">
                {searchType === "event"
                  ? (e as IEvent).eventName
                  : (e as ITemplate).templateName}
              </Text>
            </Box>
          ))}
      </Box>
    </Box>
  );
};

export default DropContainer;
