import axios from 'axios';

export default {
    state: {
        snackbar: {
            show:false,
            text: '',
            color: ''
        },
        app: {
            title: 'Invento',
            description: 'We manage research project'
        }
    },
    mutations:{
        setSnackbar(state,payload){
            state.snackbar.show = payload.show;
            state.snackbar.text = payload.text;
            state.snackbar.color = payload.color;

        },
        setPageSize(state,payload){
            state.pageSize  = payload
        }



    },
    actions: {
        setSnackbar({commit,state},payload){
            commit('setSnackbar',payload)
        },
        showSuccessSnackbar({commit,state},payload){
            commit('setSnackbar',{ show: 'true', text: payload, color:'green'})

        },
        showErrorSnackbar({commit,state},payload){
            commit('setSnackbar',{ show: 'true', text: payload, color:'red'})


        }
    },
    getters: {
        getSnackbar:state => {
            return state.snackbar;
        },

        getApp:state => {
            return state.app;
        }

    }
}
