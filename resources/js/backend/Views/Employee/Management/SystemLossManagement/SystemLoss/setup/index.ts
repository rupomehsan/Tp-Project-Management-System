import app_config from "../../../../../../Config/app_config";
import setup_type from "./setup_type";

const prefix: string = "System Loss";

const setup: setup_type = {
  prefix,
  permission: ["admin", "super_admin"],

  api_host: app_config.api_host,
  api_version: app_config.api_version,
  api_end_point: "system-loss",

  // module_name: "user",
  store_prefix: "system_loss",
  route_prefix: "SystemLoss",
  route_path: "system-loss",

  select_fields: [
    "id",
    "project_id",
    "loss_category_id",
    "loss_date",
    "start_time",
    "end_time",
    "loss_type",
    "remarks",
    "slug",
    "created_at",
  ],

  sort_by_cols: ["id", "loss_date", "date", "loss_type", "created_at"],

  layout_title: prefix + " Management",
  page_title: `${prefix} Management`,

  all_page_title: "All " + prefix,
  details_page_title: "Details " + prefix,
  create_page_title: "Create " + prefix,
  edit_page_title: "Edit " + prefix,
};

export default setup;
