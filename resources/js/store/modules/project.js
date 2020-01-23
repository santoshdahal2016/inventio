import axios from 'axios';

export default {
    state: {
        projects: [],
        project: {},
    },
    mutations:{
        setProject(state,payload){
            state.project = payload
        },
        setProjects(state,payload){
            state.projects = payload;
        },
        setProjectId(state,payload){
            state.project.id = payload
        },

    },
    actions: {
        fetchProject({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {
                commit('setLoading',true);
                axios.get('/projects/'+payload)
                    .then(response =>{
                        commit('setLoading',false)
                        commit('setProject',response.data)
                        resolve(response)
                    })
                    .catch(error => {
                        commit('setLoading',false);
                        reject(error)
                    })

            }))

        },
        fetchProjects({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {
                commit('setLoading',true);
                axios.get('/projects',{params:payload})
                    .then(response =>{
                        commit('setLoading',false)
                        commit('setProjects',response.data.data)
                        resolve(response)

                    })
                    .catch(error => {
                        commit('setLoading',false);
                        reject(error)
                    })

            }))

        },
        saveProject({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {

                commit('setLoading',true);
                const app = this;
                if(!isNaN(payload.get('id'))){
                    axios.post('/projects/'+payload.get('id'),payload)
                        .then(response => {
                            commit('setLoading',false);

                            resolve(response);
                        })
                        .catch(error => {
                            commit('setLoading',false);
                            reject(error);
                        });
                }
                else{
                    axios.post('/projects',payload)
                        .then(response => {
                            commit('setLoading',false);
                            resolve(response);
                        })
                        .catch(error => {
                            commit('setLoading',false);
                            reject(error);
                        });
                }

            }))


        },

        deleteProject({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {
                commit('setLoading',true);
                axios.delete('/projects/'+payload)
                    .then(response =>{
                        commit('setLoading',false)
                        resolve(response)
                    })
                    .catch(error => {
                        commit('setLoading',false);
                        reject(error)
                    })

            }))

        },

        setProject({commit,state},payload){
            commit('setProject',payload);
        },
        clearProject({commit}){
            commit('setProject',{})
        },

    },
    getters: {
        getProject:state => {
            return state.project;
        },
        getProjects:state => {
            return state.projects;
        }
    }
}
