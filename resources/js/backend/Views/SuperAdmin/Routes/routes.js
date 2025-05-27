//app layout
import Layout from "../Layouts/Layout.vue";
//Dashboard
import Dashboard from "../Management/Dashboard/Dashboard.vue";
//SettingsRoutes
import SettingsRoutes from "../Management/Settings/setup/routes.js";
//routes

import UserRoutes from '../Management/UserManagement/User/setup/routes.js';
// import TaskManagementRoutes from '../Management/TaskManagement/Task/setup/routes.js';
import TaskRoutes from '../Management/TaskManagement/Task/setup/routes.js';
import TaskCommentRoutes from '../Management/TaskManagement/TaskComment/setup/routes.js';
import TaskFileRoutes from '../Management/TaskManagement/TaskFile/setup/routes.js';




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
        // TaskManagementRoutes,
        TaskRoutes,
        TaskCommentRoutes,
        TaskFileRoutes,
        //settings
        SettingsRoutes,
    ],
};

export default routes;

