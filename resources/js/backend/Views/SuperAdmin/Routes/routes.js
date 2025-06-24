//app layout
import Layout from "../Layouts/Layout.vue";
//Dashboard
import Dashboard from "../Management/Dashboard/Dashboard.vue";
//SettingsRoutes
import SettingsRoutes from "../Management/Settings/setup/routes.js";
//routes

import UserRoutes from '../Management/UserManagement/User/setup/routes.js';
// Project Management
import ProjectGroup from '../Management/ProjectManagement/ProjectGroup/setup/routes.js';
import Project from '../Management/ProjectManagement/Project/setup/routes.js';
// Todo Management
import TodoCategory from '../Management/TodoManagement/Category/setup/routes.js';
import AddTodo from '../Management/TodoManagement/AddTodo/setup/routes.js';

// Task Management
import Tasks from '../Management/TasksManagement/Tasks/setup/routes.js';
// Team Management
import Team from '../Management/TeamManagement/Team/setup/routes.js';

// Attendance Management
import Attendance from '../Management/AttendanceManagement/Attendance/setup/routes.js';

// Meeting Management
import MeetingGroup from '../Management/MeetingManagement/MeetingGroup/setup/routes.js';
import Meeting from '../Management/MeetingManagement/Meeting/setup/routes.js';
import MeetingAgenda from '../Management/MeetingManagement/MeetingAgenda/setup/routes.js';




const routes = {
    path: '',
    component: Layout,
    children: [
        {
            path: 'dashboard',
            component: Dashboard,
            name: 'adminDashboard',
        },
        //management routes
        UserRoutes,
        // project Management
        ProjectGroup,
        Project,
        // Todo Management
        TodoCategory,
        AddTodo,
        // Tasks Management
        Tasks,
        // Team Management
        Team,
        // Attendance Management
        Attendance,

        // Meeting Management
        MeetingGroup,
        Meeting,
        MeetingAgenda,

        //settings
        SettingsRoutes,
    ],
};

export default routes;

