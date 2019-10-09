import SelectPure from "select-pure";
var autocomplete = new SelectPure(".autocomplete-select", {
    options: [
      {
        label: "Barbina",
        value: "ba",
      },
      {
        label: "Bigoli",
        value: "bg",
      },
      {
        label: "Bucatini",
        value: "bu",
      },
      {
        label: "Busiate",
        value: "bus",
      },
      {
        label: "Capellini",
        value: "cp",
      },
      {
        label: "Fedelini",
        value: "fe",
      },
      {
        label: "Maccheroni",
        value: "ma",
      },
      {
        label: "Spaghetti",
        value: "sp",
      },
    ],
    value: ["sp"],
    multiple: true,
    autocomplete: true,
    icon: "fa fa-times",
    onChange: value => { console.log(value); },
  });