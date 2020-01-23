import Vue from 'vue'
import Router from 'vue-router'
//Dashboard Page Component
import Home from '../components/Dashboard/DashboardComponent.vue'
import UserComponent from "../components/Dashboard/UserComponent";
import ProfileComponent from "../components/Dashboard/ProfileComponent";
import CategoryComponent from "../components/Dashboard/CategoryComponent";

import ProjectComponent from "../components/Dashboard/ProjectComponent";
import ProjectCreateComponent from "../components/Dashboard/ProjectCreateComponent";

Vue.use(Router)

const router = new Router({
    routes: [
        {
            path: "/",
            name: "Dashboard",
            component: Home,
            meta: {
                role: ['admin', 'user'],
                icon: 'mdi-view-dashboard',
            }
            , hidden: false
        },

        {
            path: "/users",
            name: "Users",
            component: UserComponent,
            meta: {
                role: ['admin'],
                icon: 'mdi-account',
            }
            , hidden: false
        },
        {
            path: "/profile",
            name: "Profile",
            component: ProfileComponent,
            meta: {
                role: ['admin', 'user'],
                icon: 'mdi-account',
            }
            , hidden: true
        },
        {
            path: "/category",
            name: "Category",
            component: CategoryComponent,
            meta: {
                role: ['admin'],
                icon: 'mdi-menu',
            }
            , hidden: false
        },

        {
            path: "/projects",
            name: "Projects List",
            component: ProjectComponent,
            meta: {
                role: ['admin'],
                icon: 'mdi-firework',
            }
            , hidden: false
        },

        {
            path: "/projects/:id",
            name: "Projects",
            component: ProjectCreateComponent,
            props: true,
            meta: {
                role: ['admin'],
                icon: 'mdi-account',
            }
            , hidden: true
        },


    ]
})

export default router
