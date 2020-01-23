<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div class="container">


        <v-row>
            <v-col
                cols="6"
            >

                <ejs-treeview id='treeview' :nodeTemplate='Template' :nodeEdited='nodeEdited' :nodeDragStop="preventParent" :allowEditing='true' :dataSourceChanged="dataChange" :nodeDropped="onMoveCallback"
                              :fields="treeFields" :allowDragAndDrop='true'></ejs-treeview>

            </v-col>
            <!--Edit Dialog-->

            <v-col

                cols="6"
            >

                <v-card>
                    <v-card-title>
                        <span class="headline"> {{textMap[dialogStatus]}}</span>
                    </v-card-title>

                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-form ref="form" v-model="valid">

                                    <v-col cols="24" sm="24" md="24">
                                        <v-text-field v-model="defaultItem.title"
                                                      :rules="requiredRules"
                                                      :error-messages="errors.first('title')"
                                                      label="Title"></v-text-field>
                                    </v-col>
                                </v-form>

                            </v-row>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <div class="flex-grow-1"></div>
                        <v-btn color="blue darken-1" text @click="save()">
                            <span v-if="dialogStatus==='create'">Save</span>
                            <span v-if="dialogStatus==='update'">Edit</span>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <!--Table View Started-->
        <material-card
            color="green"
            title="Category"
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


                <template v-slot:item.action="{ item }">
                    <v-icon small class="mr-2" @click="editItem(item)" color="teal darken-2">
                        edit
                    </v-icon>
                    <v-icon small @click="deleteItem(item)" color="red darken-2">
                        delete
                    </v-icon>
                </template>

                <template slot="no-data">
                    <v-alert :value="true" color="error" icon="warning">
                        Sorry, No Category Yet.
                    </v-alert>
                </template>

            </v-data-table>
        </material-card>

    </div>
</template>

<script>

    import {mapGetters} from 'vuex';
    import draggable from 'vuedraggable'

    export default {
        /*injecting the vee-validate*/
        inject: ['$validator'],
        components: {
            draggable,
        },
        data() {
            var demoVue = Vue.component("demo", {
                template: '<li class="list-group-item"  > <v-icon>mdi-pin</v-icon> {{data.title}} <div style="float:right"> <v-avatar color="teal" size="20"> <span class="white--text ">{{data.position}}</span> </v-avatar> </div> </li>',
                data() {
                    return {
                        data: {}
                    };
                }
            });
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

                /*Create and Edit Related Variable*/
                dialogStatus: 'create',
                textMap: {
                    update: 'Edit',
                    create: 'Create'
                },
                valid: true,
                requiredRules: [
                    v => !!v || 'Required field'
                ],
                edit: false,
                defaultItem: {
                    title: '',
                    position: ''
                },

                Template: function (e) {
                    return {
                        template: demoVue
                    };
                },

                /*For Hot Reloading Data*/
                path: JSON.parse(JSON.stringify(this.$route.path))
            }
        }, computed: {
            ...mapGetters([]),
            getFullPath() {
                return this.$route.path
            },
            treeFields() {
                return {dataSource: this.data, id: 'id', text: 'title', selected: 'isSelected'}
            },
        },
        watch: {
            options: {
                handler() {
                    this.fetch();
                },
                deep: true,
            },
            getFullPath() {
                if (this.getFullPath === this.path) {
                    this.fetch()
                }
            }

        },

        methods: {
            dataChange(args){
                // console.log(args);
                // console.log(args['draggedNodeData']);
                // console.log(args['draggedNodeData']);

            },
            preventParent(args){
                // console.log(args['dropLevel']);

                if (args['dropLevel'] > 1) {
                    args.cancel = true;
                }
            },

            nodeEdited: function (args) {
                var payload = this.data.find(x => x.id == args['nodeData']['id']);
                payload.title = args['newText'];
                this.$store.dispatch('saveCategory', payload).then(response => {
                    if (response.status === 201) {
                        this.$store.dispatch('showSuccessSnackbar', 'Category was created successfully');
                    } else {
                        this.$store.dispatch('showSuccessSnackbar', 'Category was edited successfully');
                    }
                    this.fetch();

                }).catch(error => {

                })
            },

            resetDefault() {
                this.defaultItem = {
                    id: undefined,
                    title: ' ',
                    position: undefined,
                    slug: undefined,
                }
                this.dialogStatus = "create";
                this.edit = false;

            },
            onMoveCallback(args) {
                // console.log(args);
                return new Promise((resolve, reject) => {
                    this.$store.dispatch('sortCategory', {'id1':args['draggedNodeData']['id'] , 'id2':args['droppedNodeData']['id']})
                        .then(response => {
                           this.fetch();
                        }).catch(error => {
                        // console.log(error.response.status);
                    });
                })

            },
            /*Fetch Categories*/
            fetch() {
                return new Promise((resolve, reject) => {
                    this.$store.dispatch('fetchCategories', this.options)
                        .then(response => {
                            this.data = response.data.data;
                            this.loading = false;
                            this.totalData = response.data.meta.total;
                            this.treeFields.dataSource = response.data.data;

                        }).catch(error => {
                        // console.log(error.response.status);
                    });
                })

            },

            /*Show Edit Dialog*/
            editItem(item) {
                this.editedIndex = this.data.indexOf(item)
                this.defaultItem = Object.assign({}, item)
                this.dialogStatus = "update";
                this.edit = true;

            },

            /*Save the Edited Category*/
            save() {
                if (this.$refs.form.validate()) {
                    return new Promise((resolve, reject) => {
                        this.$store.dispatch('saveCategory', this.defaultItem).then(response => {
                            if (response.status === 201) {
                                this.$store.dispatch('showSuccessSnackbar', 'Category was created successfully');
                            } else {
                                this.$store.dispatch('showSuccessSnackbar', 'Category was edited successfully');
                            }
                            this.fetch();
                            this.resetDefault();


                        }).catch(error => {
                            // this.$store.dispatch('showErrorSnackbar', error.response.data);

                        })
                    })
                }

            },


            /*Delete Category*/
            deleteItem(item) {
                const index = this.data.indexOf(item)
                this.$root.$confirm('Delete ' + item.name, 'Are you sure?', {color: 'red'})
                    .then((confirm) => {
                        if (confirm) {
                            //if true, call delete end point
                            this.$store.dispatch('deleteCategory', item.id)
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


<style>

    .e-list-text {
        width: 100%;
    }

    .flip-list-move {
        transition: transform 0.5s;
    }

    .no-move {
        transition: transform 0s;
    }

    .ghost {
        opacity: 0.5;
        background: #c8ebfb;
    }

    .list-group {
        min-height: 20px;
    }

    .list-group-item {
        cursor: move;
    }

    .list-group-item i {
        cursor: pointer;
    }
</style>
