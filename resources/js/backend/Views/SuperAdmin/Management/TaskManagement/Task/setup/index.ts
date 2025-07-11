import app_config from "../../../../../../Config/app_config";
import setup_type from "./setup_type";

const prefix: string = "Task Management";

const setup: setup_type = {
  prefix,
  permission: ["admin", "super_admin"],

  api_host: app_config.api_host,
  api_version: app_config.api_version,
  api_end_point: "tasks",

  store_prefix: "tasks",
  route_prefix: "Task",
  route_path: "tasks",

  select_fields: [
    "id",
    "project_id",
    "title",
    "description",
    "system_loss",
    "assigned_to",
    "start_date",
    "due_date",
    "priority",
    "task_status",
    "slug",
    "created_at",
  ],

  sort_by_cols: [
    "id",
    "title",
    "description",
    "system_loss",
    "assigned_to",
    "due_date",
    "priority",
    'task_status'
  ],

  layout_title: prefix + " Management",
  page_title: `${prefix} Management`,

  all_page_title: "All " + prefix,
  details_page_title: "Details " + prefix,
  create_page_title: "Create " + prefix,
  edit_page_title: "Edit " + prefix,
};

export default setup;
