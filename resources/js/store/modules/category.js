import axios from 'axios';

export default {
    state: {
        categories: [],
        category: {},
    },
    mutations:{
        setCategory(state,payload){
            state.category = payload
        },
        setCategories(state,payload){
            state.categories = payload;
        },
        setCategoryId(state,payload){
            state.category.id = payload
        },

    },
    actions: {
        fetchCategory({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {
                commit('setLoading',true);
                axios.get('/categories/'+payload)
                    .then(response =>{
                        commit('setLoading',false)
                        commit('setCategory',response.data)
                        resolve(response)
                    })
                    .catch(error => {
                        commit('setLoading',false);
                        reject(error)
                    })

            }))

        },
        fetchCategories({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {
                commit('setLoading',true);
                axios.get('/categories',{params:payload})
                    .then(response =>{
                        commit('setLoading',false)
                        commit('setCategories',response.data.data)
                        resolve(response)

                    })
                    .catch(error => {
                        commit('setLoading',false);
                        reject(error)
                    })

            }))

        },
        saveCategory({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {

                commit('setLoading',true);
                const app = this;
                if(payload.id){
                    axios.put('/categories/'+payload.id,payload)
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
                    axios.post('/categories',payload)
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

        deleteCategory({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {
                commit('setLoading',true);
                axios.delete('/categories/'+payload)
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
        sortCategory({commit,state,rootState},payload){
            return new Promise(((resolve, reject) => {
                commit('setLoading',true);
                axios.post('/categories/sort/'+parseInt(payload['id1'])+'/'+parseInt(payload['id2']))
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
        setCategory({commit,state},payload){
            commit('setCategory',payload);
        },
        clearCategory({commit}){
            commit('setCategory',{})
        },

    },
    getters: {
        getCategory:state => {
            return state.category;
        },
        getCategories:state => {
            return state.categories;
        }
    }
}
