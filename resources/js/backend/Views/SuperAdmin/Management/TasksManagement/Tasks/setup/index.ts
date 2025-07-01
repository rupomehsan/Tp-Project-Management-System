import app_config from "../../../../../../Config/app_config";
import setup_type from "./setup_type";

const prefix: string = "Tasks";

const setup: setup_type = {
  prefix,
  permission: ["admin", "super_admin"],

  api_host: app_config.api_host,
  api_version: app_config.api_version,
  api_end_point: "task",

  // module_name: "user",
  store_prefix: "tasks",
  route_prefix: "Tasks",
  route_path: "tasks",

  select_fields: [
    "id",
    "project_id",
    "title",
    "description",
    "system_loss",
    "assigned_to",
    "start_date",
    "end_date",
    "task_status",
    "rating",
    "priority",
    "slug",
    "created_at",
  ],

  sort_by_cols: [
    "id",
    "project_id",
    "title",
    "description",
    "system_loss",
    "assigned_to",
    "start_date",
    "end_date",
    "task_status",
    "priority",
    "slug",
    "created_at",
  ],

  layout_title: prefix + " Management",
  page_title: `${prefix} Management`,

  all_page_title: "All " + prefix,
  details_page_title: "Details " + prefix,
  create_page_title: "Create " + prefix,
  edit_page_title: "Edit " + prefix,
};

export default setup;
