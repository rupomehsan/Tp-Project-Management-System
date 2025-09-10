import ProjectPlanning from "../pages/ProjectPlanning.vue";
import Layout from "../pages/Layout.vue";
const routes = {
  path: "project",
  component: Layout,
  children: [
    {
      path: "project-planning",
      name: "ProjectPlanning",
      component: ProjectPlanning,
    },
  ],
};

export default routes;
