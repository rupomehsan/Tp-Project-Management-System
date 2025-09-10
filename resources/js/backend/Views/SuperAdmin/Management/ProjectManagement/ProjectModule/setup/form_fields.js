export default [
  {
    name: "project_id",
    label: "Enter your project id",
    type: "select",
    value: "",
    data_list: [],
  },

  {
    name: "module_name",
    label: "Enter your module name",
    type: "text",
    value: "",
  },

  {
    name: "start_date",
    label: "Enter your start date",
    type: "date",
    value: "",
  },

  {
    name: "end_date",
    label: "Enter your end date",
    type: "date",
    value: "",
  },
  {
    name: "is_done",
    label: "Is the module done?",
    type: "select",
    value: false,
    data_list: [
      { value: true, label: "Yes" },
      { value: false, label: "No" },
    ],
  },
  {
    name: "order",
    label: "Enter your order",
    type: "number",
    value: "",
  },
];
