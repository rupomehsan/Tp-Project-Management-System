import app_config from "../../../../../../Config/app_config";
import setup_type from "./setup_type";

const prefix: string = "Projects";

const setup: setup_type = {
  prefix,
  permission: ["admin", "super_admin"],

  api_host: app_config.api_host,
  api_version: app_config.api_version,
  api_end_point: "project",

  // module_name: "user",
  store_prefix: "project",
  route_prefix: "Project",
  route_path: "project",

  select_fields: [
    "id",
    "project_group_id",
    "name",
    "description",
    "start_date",
    "end_date",
    "project_status",
    "project_link",
    "project_agrement_file",
    "project_document",
    "project_document_files",
    "project_progress",
    "slug",
    "created_at",
    "delivery_status",
  ],

  sort_by_cols: [
    "id",
    "project_group_id",
    "name",
    "description",
    "start_date",
    "end_date",
    "project_status",
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
