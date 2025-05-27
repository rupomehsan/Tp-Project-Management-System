import app_config from "../../../../../../Config/app_config";
import setup_type from "./setup_type";

const prefix: string = "Task Management";

const setup: setup_type = {
  prefix,
  permission: ["admin", "super_admin"],

  api_host: app_config.api_host,
  api_version: app_config.api_version,
  api_end_point: "tasks-files",

  store_prefix: "tasksfiles",
  route_prefix: "TaskFile",
  route_path: "tasksfiles",

  select_fields: [
    "id",
    "task_id",
    "file_name",
    "file_path",
    "uploaded_at",
    "slug",
    "created_at",
  ],

  sort_by_cols: [
    "id",
    "task_id",
  ],

  layout_title: prefix + " Management",
  page_title: `${prefix} Management`,

  all_page_title: "All " + prefix,
  details_page_title: "Details " + prefix,
  create_page_title: "Create " + prefix,
  edit_page_title: "Edit " + prefix,
};

export default setup;
