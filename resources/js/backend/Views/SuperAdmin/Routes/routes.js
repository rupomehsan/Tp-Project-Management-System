//app layout
import Layout from "../Layouts/Layout.vue";
//Dashboard
import Dashboard from "../Management/Dashboard/Dashboard.vue";
//SettingsRoutes
import SettingsRoutes from "../Management/Settings/setup/routes.js";
//routes
import ProjectModuleRoutes from "../Management/ProjectManagement/ProjectModule/setup/routes.js";

import MessageRoutes from "../Management/Message/setup/routes.js";
import NotificationRoutes from "../Management/Notification/setup/routes.js";

import UserRoutes from "../Management/UserManagement/User/setup/routes.js";
// Project Management
import ProjectGroup from "../Management/ProjectManagement/ProjectGroup/setup/routes.js";
import Project from "../Management/ProjectManagement/Project/setup/routes.js";
import UpcommingProject from "../Management/ProjectManagement/UpcommingProject/setup/routes.js";
import ProjectProposal from "../Management/ProjectManagement/ProjectProposal/setup/routes.js";
import ProjectPlanning from "../Management/ProjectManagement/ProjectPlanning/setup/routes.js";

// Todo Management
import TodoCategory from "../Management/TodoManagement/Category/setup/routes.js";
import AddTodo from "../Management/TodoManagement/AddTodo/setup/routes.js";
import Credential from "../Management/TodoManagement/Credential/setup/routes.js";

// Task Management
import Tasks from "../Management/TasksManagement/Tasks/setup/routes.js";
import TaskGroup from "../Management/TasksManagement/TaskGroup/setup/routes.js";
// Team Management
import Team from "../Management/TeamManagement/Team/setup/routes.js";

// Attendance Management
import Attendance from "../Management/AttendanceManagement/Attendance/setup/routes.js";

// Meeting Management
import MeetingGroup from "../Management/MeetingManagement/MeetingGroup/setup/routes.js";
import Meeting from "../Management/MeetingManagement/Meeting/setup/routes.js";
import MeetingAgenda from "../Management/MeetingManagement/MeetingAgenda/setup/routes.js";

//System Loss Management
import SystemLossCategory from "../Management/SystemLossManagement/SystemLossCategory/setup/routes.js";
import SystemLoss from "../Management/SystemLossManagement/SystemLoss/setup/routes.js";

const routes = {
  path: "",
  component: Layout,
  children: [
    {
      path: "dashboard",
      component: Dashboard,
      name: "adminDashboard",
    },
    //management routes
    ProjectModuleRoutes,

    MessageRoutes,
    NotificationRoutes,

    UserRoutes,
    // project Management
    ProjectGroup,
    Project,
    UpcommingProject,
    ProjectProposal,
    ProjectPlanning,
    // Todo Management
    TodoCategory,
    AddTodo,
    Credential,
    // Tasks Management
    Tasks,
    TaskGroup,
    // Team Management
    Team,
    // Attendance Management
    Attendance,

    // Meeting Management
    MeetingGroup,
    Meeting,
    MeetingAgenda,
    // System Loss Management
    SystemLossCategory,
    SystemLoss,

    //settings
    SettingsRoutes,
  ],
};

export default routes;
