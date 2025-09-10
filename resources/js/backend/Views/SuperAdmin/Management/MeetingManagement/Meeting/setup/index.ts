import app_config from "../../../../../../Config/app_config";
import setup_type from "./setup_type";

const prefix: string = "Meeting";

const setup: setup_type = {
  prefix,
  permission: ["admin", "super_admin"],

  api_host: app_config.api_host,
  api_version: app_config.api_version,
  api_end_point: "meeting",

  // module_name: "user",
  store_prefix: "meeting",
  route_prefix: "Meeting",
  route_path: "meeting",

  select_fields: [
    "id",
    "group_id",
    "title",
    "date",
    "meeting_type",
    "meeting_link",
    "description",
    "slug",
    "created_at",
  ],

  sort_by_cols: [
    "id",
    "group_id",
    "title",
    "date",
    "meating_type",
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
