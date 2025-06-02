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


import BlogCategroy from '../Management/BasicTestModule/BlogCategory/setup/routes.js';




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

        BlogCategroy,
        //settings
        SettingsRoutes,
    ],
};

export default routes;

