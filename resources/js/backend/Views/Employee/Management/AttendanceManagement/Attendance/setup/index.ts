import app_config from "../../../../../../Config/app_config";
import setup_type from "./setup_type";

const prefix: string = "Attendance";

const setup: setup_type = {
  prefix,
  permission: ["admin", "super_admin"],

  api_host: app_config.api_host,
  api_version: app_config.api_version,
  api_end_point: "attendance",

  // module_name: "user",
  store_prefix: "attendance",
  route_prefix: "Attendance",
  route_path: "attendance",

  select_fields: [
    "id",
    "user_id",
    "date",
    "check_in",
    "check_out",
    "attendance_status",
    "slug",
    "created_at",
    "notes",
    "is_late",
  ],

  sort_by_cols: ["id", "date"],

  layout_title: prefix + " Management",
  page_title: `${prefix} Management`,

  all_page_title: "All " + prefix,
  details_page_title: "Details " + prefix,
  create_page_title: "Create " + prefix,
  edit_page_title: "Edit " + prefix,
};

export default setup;
