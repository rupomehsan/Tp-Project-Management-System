import app_config from "../../../../../../Config/app_config";
import setup_type from "./setup_type";

const prefix: string = "ProjectModule";

const setup: setup_type = {
  prefix,
  permission: ["admin", "super_admin"],

  api_host: app_config.api_host,
  api_version: app_config.api_version,
  api_end_point: "project-modules",

  module_name: "project-module",
  store_prefix: "project_module",
  route_prefix: "ProjectModule",
  route_path: "project-module",

  select_fields: [
    "id",
    "project_id",
    "module_name",
    "start_date",
    "end_date",
    "is_done",
    "order",
    "status",
    "slug",
    "created_at",
    "deleted_at",
  ],

  sort_by_cols: [
    "id",
    "project_id",
    "module_name",
    "start_date",
    "end_date",
    "is_done",
    "order",
    "status",
    "created_at",
  ],
  table_header_data: [
    "id",
    "project",
    "module_name",
    "start_date",
    "end_date",
    "is_done",
    "order",
    "status",
    "created_at",
  ],
  table_row_data: [
    "id",
    "project_id",
    "module_name",
    "start_date",
    "end_date",
    "is_done",
    "order",
    "status",
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
