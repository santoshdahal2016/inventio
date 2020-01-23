<template>
    <v-container>
        <v-card class="elevation-12">

            <v-card-title>
                <div>
                    <h3 class="headline">{{title}}</h3>

                </div>
            </v-card-title>
            <v-card-text>

                <v-form ref="project" v-model="valid">
                    <v-container grid-list-md text-md-center>
                        <v-layout row wrap align-center>
                            <input type="hidden" v-model="project.id"/>

                            <v-flex md12 sm12>
                                <v-text-field
                                    name="project"
                                    label="Project *"
                                    type="text"
                                    v-model="project.title"
                                    :rules="requiredRules"
                                    v-validate
                                    data-vv-name="title"
                                    :error-messages="errors.first('title')"
                                ></v-text-field>
                            </v-flex>
                            <v-flex md12 sm12>

                                <vue-editor
                                    :customModules="customModulesForEditor"
                                    :editorOptions="editorSettings"
                                    v-model="project.abstract"></vue-editor>

                            </v-flex>


                            <v-flex md6 sm12>
                                <v-select
                                    name="category"
                                    :items="getCategories"
                                    label="Category *"
                                    v-model="project.category_id"
                                    item-text="title"
                                    item-value="id"
                                    :rules="requiredRules"
                                ></v-select>
                            </v-flex>


                            <v-flex md6 sm12>
                                <v-label
                                >Feature Image:
                                </v-label>
                                <img style="max-width: 100%;height: auto" :src="project.featured[0].url" height="200px"
                                     v-if="project.featured">

                                <input
                                    name="featured"
                                    label="Featured Image "
                                    type="file"
                                    @change="handleFileUpload"
                                />
                            </v-flex>

                        </v-layout>
                    </v-container>

                </v-form>

            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn color="primary" :disabled="!valid || loading" @click="saveProject">Save
                    <v-icon v-if="loading">fas fa-circle-notch fa-spin</v-icon>
                </v-btn>
                <v-btn color="warning" @click="$router.go(-1)">Cancel

                </v-btn>

            </v-card-actions>

        </v-card>
    </v-container>

</template>

<script>
    import {mapGetters} from 'vuex';
    import {VueEditor} from 'vue2-editor'

    import {ImageDrop} from "quill-image-drop-module";
    import ImageResize from "quill-image-resize-module";

    export default {
        name: "project",
        props: ['msg', 'id'],
        components: {
            'vue-editor': VueEditor
        },
        inject: ['$validator'],
        data() {
            return {
                customModulesForEditor: [
                    {alias: "imageDrop", module: ImageDrop},
                    {alias: "imageResize", module: ImageResize}
                ],
                editorSettings: {
                    modules: {
                        imageResize: {}
                    }
                },

                alert: false,
                valid: true,
                requiredRules: [
                    v => !!v || 'Required field'
                ],
                project: {},
                formData: null,

                path: JSON.parse(JSON.stringify(this.$route.path))

            }
        },
        created: function () {
            this.$store.dispatch('fetchCategories', {
                page: 1,
                itemsPerPage: 10,
                search: undefined,
                sort: 'desc'
            },)

            this.fetchProject()

        },
        mounted() {

        },
        methods: {
            fetchProject() {
                if (this.id) {
                    if (this.id === 'create') {
                        this.$store.dispatch('clearProject');
                        this.project = {};

                    } else {
                        this.$store.dispatch('fetchProject', this.id)
                            .then(response => {
                                this.project = response.data.data;
                            })
                    }
                }
            },
            handleFileUpload(event) {
                this.featured = event.target.files[0]
            },
            saveProject() {
                if (this.$refs.project.validate()) {
                    this.$store.commit('setLoading', true);
                    var formData = new FormData();
                    formData.append('id', this.project.id);
                    formData.append('title', this.project.title);
                    formData.append('abstract', this.project.abstract);
                    formData.append('category_id', this.project.category_id);




                    if (this.featured) {
                        formData.append('featured', this.featured);
                    }
                    if (this.project.id) {
                        formData.append('_method', "put");

                    }
                    this.$store.dispatch('saveProject', formData).then(response => {
                        if (response.status === 201) {
                            this.$store.dispatch('showSuccessSnackbar', 'Project was created successfully');
                        } else {
                            this.$store.dispatch('showSuccessSnackbar', 'Project was edited successfully');
                        }
                        this.$router.push({name: 'Projects List'});

                    })
                        .catch(error => {
                            this.$store.dispatch('showErrorSnackbar', error);


                        })

                }
            }
        },
        computed: {
            ...mapGetters([
                'loading', 'getProject', 'getCategories',

            ]),
            getFullPath() {
                return this.$route.path
            },
            title() {
                if (this.project.id) {
                    return "Edit " + this.project.title
                } else
                    return "Create Project"

            },
            loading() {
                return this.$store.getters.loading;
            }
        },
        watch: {
            '$route.params.id'(newId, oldId) {
                this.fetchProject()
            }
        }

    }
</script>

<style scoped>

</style>
