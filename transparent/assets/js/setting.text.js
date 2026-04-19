var SettingText = function (parameter) {
  const main = this;

  this.textItems = [];

  main.init = function () {
    $("#text-show").change(function (e) {
      const checked = $(this).prop("checked");

      main.textItems.forEach((element) => {
        // console.log(element);
        element.visible = checked;
      });
    });
  };

  main.addText = function (item) {
    this.textItems.push(item);
  };

  main.init();
};
