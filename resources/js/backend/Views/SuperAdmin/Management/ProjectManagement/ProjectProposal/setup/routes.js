import ProjectProposal from "../pages/ProjectProposal.vue";
import Layout from "../pages/Layout.vue";
const routes = {
  path: "project-proposal",
  component: Layout,
  children: [
    {
      path: "project-proposal",
      name: "ProposalProject",
      component: ProjectProposal,
    },
  ],
};

export default routes;
