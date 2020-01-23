<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div class="container">


        <v-btn color="primary" to="/projects/create">Create a Projects</v-btn>

        <!--Table View Started-->
        <material-card
            color="green"
            title="Projects"
        >

            <v-data-table
                :items="data"
                :headers="headers"
                :loading="loading" loading-text="Loading... Please wait"
                :options.sync="options"
                :server-items-length="totalData">

                <template v-slot:top>
                    <v-text-field @change="fetch" v-model="options.search" prepend-icon="search" label="Search ......"
                                  class="mx-4">
                    </v-text-field>
                </template>

                <template v-slot:item.action="{ item }" >
                    <router-link :to="{name: 'Projects',params: {id:item.id}}">
                        <v-icon color="primary" small class="mr-2" @click="" >
                            edit
                        </v-icon>
                    </router-link>
                    <v-icon color="red" @click="deleteItem(item)" small >
                        delete
                    </v-icon>
                </template>

                <template slot="no-data">
                    <v-alert :value="true" color="error" icon="warning">
                        Sorry, No Projects Yet.
                    </v-alert>
                </template>

            </v-data-table>
        </material-card>
        <!--Edit Dialog-->
    </div>
</template>

<script>

    import {mapGetters} from 'vuex';

    export default {
        /*injecting the vee-validate*/
        inject: ['$validator'],

        data() {
            return {

                /*Data Table Related Variable*/
                loading: true,
                headers: [
                    {text: 'Title', align: 'left', sortable: false, value: 'title'},
                    {text: 'Actions', value: 'action', sortable: false},

                ],
                data: [],
                totalData: 0,

                /*Search and Query Related Variable*/
                options: {
                    page: 1,
                    itemsPerPage: 10,
                    search: undefined,
                    sort: 'desc'
                },


                path: JSON.parse(JSON.stringify(this.$route.path))
            }
        }, computed: {
            ...mapGetters([]),
            getFullPath () {
                return this.$route.path
            }
        },
        watch: {
            options: {
                handler() {
                    this.fetch();
                },
                deep: true,
            },
            getFullPath () {
                if(this.getFullPath === this.path){
                    this.fetch()
                }
            }

        },

        methods: {
            /*Fetch Projects*/
            fetch() {
                return new Promise((resolve, reject) => {
                    this.$store.dispatch('fetchProjects', this.options)
                        .then(response => {
                            this.data = response.data.data;
                            this.loading = false;
                            this.totalData = response.data.meta.total;
                        }).catch(error => {
                        // console.log(error.response.status);
                    });
                })

            },


            /*Delete Project*/
            deleteItem(item) {
                const index = this.data.indexOf(item)
                this.$root.$confirm('Delete ' + item.title, 'Are you sure?', {color: 'red'})
                    .then((confirm) => {
                        if (confirm) {
                            //if true, call delete end point
                            this.$store.dispatch('deleteProject', item.id)
                                .then(response => {
                                    this.$store.dispatch('showSuccessSnackbar', response.data.message);
                                    this.fetch();
                                })
                        } else {

                        }
                    })
            },

        },
    }
</script>
