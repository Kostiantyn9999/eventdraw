import React, { useMemo, useState } from "react";
import { Box, Button, DropButton, Text, TextInput } from "grommet";
import { IEvent, ITemplate } from "types/floor";
import { IEditableMatterport } from "..";
import DropContainer from "./DropContainer";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTimes } from "@fortawesome/free-solid-svg-icons";

type Props = {
  item: IEditableMatterport;
  setItem: (v: IEditableMatterport) => void;
};

const LayoutAssignItem = ({
  type,
  value,
  setValue,
  removeItem,
}: {
  type: "event" | "template";
  value: IEvent | ITemplate;
  setValue: (
    preType: "event" | "template",
    prevId: string | null,
    v: IEvent | ITemplate,
    type: "event" | "template"
  ) => void;
  removeItem: (delId: string | null) => void;
}) => {
  const [selectOpened, setSelectOpened] = useState(false);

  const displayName = useMemo(() => {
    if (value) {
      if (type === "event") return (value as IEvent).name;
      if (type === "template") return (value as ITemplate).templateName;
    }

    return "No Assigned";
  }, [value, type]);

  const setEvent = (
    newValue: IEvent | ITemplate,
    newType: "event" | "template"
  ) => {
    setValue(type, value ? value.id : null, newValue, newType);
    setSelectOpened(false);
  };

  return (
    <Box
      flex
      direction="row"
      justify="between"
      style={{ alignItems: "center" }}
    >
      <Box background="#aaaaaa" color="#ffffff" pad="small">
        <Text size="2x">{type.toUpperCase().at(0)}</Text>
      </Box>
      <DropButton
        label={displayName}
        open={selectOpened}
        dropAlign={{ top: "bottom", right: "right" }}
        dropContent={<DropContainer setEvent={setEvent} />}
        onOpen={() => setSelectOpened(true)}
        onClose={() => setSelectOpened(false)}
      />
      <Button
        icon={<FontAwesomeIcon icon={faTimes as any} size="1x" />}
        onClick={() => {
          removeItem(value ? value.id : null);
        }}
      />
    </Box>
  );
};

const LayoutAssign = ({ item, setItem }: Props) => {
  const [filterText, setFilterText] = useState("");

  const addEvent = () => {
    const items = item.events;

    setItem({ ...item, events: [...items, null] });
  };

  const removeItem = (delId: string | null) => {
    const newItems = item.events.filter((m) =>
      m ? m.id !== delId : m !== delId
    );

    setItem({ ...item, events: newItems });
  };

  const setEvent = (
    preType: "event" | "template",
    prevId: string | null,
    v: IEvent | ITemplate,
    type: "event" | "template"
  ) => {
    // setItem({ ...item, event: v });
    // setSelectOpened(false);
    if (type === "event") {
      if (preType === type) {
        if (prevId) {
          const newItems = item.events
            // .filter((ev) => ev.id !== v.id)
            .map((ev) => {
              if (ev.id === prevId) {
                return {
                  id: (v as IEvent).id,
                  name: (v as IEvent).eventName,
                  eventName: (v as IEvent).eventName,
                };
              } else {
                return ev;
              }
            });

          setItem({ ...item, events: newItems });
        } else {
          setItem({
            ...item,
            events: [
              ...item.events.filter((ev) => ev !== null),
              {
                id: (v as IEvent).id,
                name: (v as IEvent).eventName,
                eventName: (v as IEvent).eventName,
              },
            ],
          });
        }
      } else {
        const newItems = item.templates.filter(
          (ev) => ev !== null && ev.id !== prevId
        );
        setItem({
          ...item,
          templates: newItems,
          events: [
            ...item.events.filter((ev) => ev !== null),
            {
              id: (v as IEvent).id,
              name: (v as IEvent).eventName,
              eventName: (v as IEvent).eventName,
            },
          ],
        });
      }
    } else if (type === "template") {
      if (preType === type) {
        if (prevId) {
          const newItems = item.templates.map((ev) => {
            if (ev.id === prevId) {
              return v as ITemplate;
            } else {
              return ev;
            }
          });

          setItem({ ...item, templates: newItems });
        } else {
          setItem({
            ...item,
            templates: [
              ...item.templates.filter((ev) => ev !== null),
              v as ITemplate,
            ],
          });
        }
      } else {
        const newItems = item.events.filter(
          (ev) => ev !== null && ev.id !== prevId
        );
        setItem({
          ...item,
          events: newItems,
          templates: [
            ...item.templates.filter((ev) => ev !== null),
            v as ITemplate,
          ],
        });
      }
    }
  };

  return (
    <Box border="bottom" flex={{ shrink: 0 }}>
      <Box pad="small" background="pale_grey">
        <Text color="light_navy_bright" weight={600}>
          Layouts on Eventdraw
        </Text>
      </Box>
      <Box pad="small" gap="xsmall" flex={{ shrink: 0 }}>
        <Box flex direction="row" style={{ alignItems: "center" }} gap="2px">
          <TextInput
            placeholder="Search"
            value={filterText}
            onChange={(e) => {
              setFilterText(e.target.value);
            }}
          />
          <Button label="Add" primary onClick={addEvent} />
        </Box>
        {item.events
          .filter((d) => (d ? d.name.includes(filterText) : true))
          .map((itemEvent, index) => (
            <LayoutAssignItem
              key={index}
              type="event"
              value={itemEvent}
              setValue={setEvent}
              removeItem={removeItem}
            />
          ))}
        {item.templates
          .filter((d) => (d ? d.templateName.includes(filterText) : true))
          .map((itemEvent, index) => (
            <LayoutAssignItem
              key={index}
              type="template"
              value={itemEvent}
              setValue={setEvent}
              removeItem={removeItem}
            />
          ))}
        {/* <TextInput value="01 - The Mint - Boardroom" size="small" /> */}
        {/* <DropButton value={value?.name} size="small" /> */}
        {/* <DropButton
          label={value ? value.eventName : "No Assigned"}
          open={selectOpened}
          dropAlign={{ top: "bottom", right: "right" }}
          dropContent={<DropContainer setEvent={setEvent} />}
          onOpen={() => setSelectOpened(true)}
          onClose={() => setSelectOpened(false)}
        />
        <DropButton
          label={value ? value.eventName : "No Assigned"}
          open={selectOpened}
          dropAlign={{ top: "bottom", right: "right" }}
          dropContent={<DropContainer setEvent={setEvent} />}
          onOpen={() => setSelectOpened(true)}
          onClose={() => setSelectOpened(false)}
        /> */}
        {/* <Select
          options={events}
          getOptionLabel={(d) => d.eventName}
          getOptionValue={(d) => d.id}
          value={value}
          onChange={(v) => setItem({ ...item, event: v })}
        /> */}
      </Box>
    </Box>
  );
};

export default LayoutAssign;
