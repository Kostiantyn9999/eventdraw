import SceneComponent, { ComponentInteractionType } from "./SceneComponent";

class Clickable extends SceneComponent {
  events = {
    [ComponentInteractionType.CLICK]: true,
    [ComponentInteractionType.HOVER]: false,
  };

  onInit() {
    console.log("Clickable");
  }

  onEvent(eventType: string) {
    if (eventType === ComponentInteractionType.CLICK) {
      this.notify(ComponentInteractionType.CLICK, { id: 22 });
    }
  }
}

export const clickableType = "mp.clickable";
export const makeClickable = () => {
  return new Clickable();
};
