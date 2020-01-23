<template>
    <v-container>
        <v-card class="elevation-12">
            <v-card-title>
                <div>
                    <h3 class="headline">{{title}}</h3>

                </div>
            </v-card-title>
            <v-card-text>

                <v-form ref="category" v-model="valid">
                    <v-container grid-list-md text-md-center>
                        <v-layout row wrap align-center>
                            <input type="hidden" v-model="category.id"/>
                            <v-flex md12 sm12>
                                <v-text-field
                                    prepend-icon="person"
                                    name="category"
                                    label="Category *"
                                    type="text"
                                    v-model="category.title"
                                    :rules="requiredRules"
                                    data-vv-name="title"
                                    :error-messages="errors.first('title')"
                                ></v-text-field>
                            </v-flex>

                        </v-layout>
                    </v-container>

                </v-form>

            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn color="primary" :disabled="!valid || loading" @click="saveCategory">Save
                    <v-icon v-if="loading">fas fa-circle-notch fa-spin</v-icon>
                </v-btn>
                <v-btn @click="$router.go(-1)">Cancel

                </v-btn>

            </v-card-actions>

        </v-card>
    </v-container>

</template>

<script>
    import { mapGetters} from 'vuex';

    export default {
        name: "category",
        props: ['msg','id'],
        components: {
        },
        inject: ['$validator'],
        data() {
            return {
                alert: false,
                valid: true,
                requiredRules: [
                    v => !!v || 'Required field'
                ],
                category:{},
                formData:null
            }
        },
        created: function(){
            this.fetchCategory()

        },
        mounted(){

        },
        methods:{
            fetchCategory(){
                if(this.id){
                    if(this.id ==='create'){
                        this.$store.dispatch('clearCategory');

                    }
                    else {
                        this.$store.dispatch('fetchCategory',this.id)
                            .then(response => {
                                this.category = response.data.data;
                            })
                    }
                }
            },
            saveCategory(){
                if(this.$refs.category.validate()){
                    this.$store.dispatch('saveCategory',this.category).then(response => {
                        if(response.status === 201){
                            this.$store.dispatch('showSuccessSnackbar','Category was created successfully');
                        }else{
                            this.$store.dispatch('showSuccessSnackbar','Category was edited successfully');
                        }
                        this.$router.push({name:'categories'});

                    })
                        .catch(error => {
                            this.$store.dispatch('showErrorSnackbar',error);


                        })
                }
            }
        },
        computed: {
            ...mapGetters([
                'loading','getCategory'
            ]),
            title(){
                if(this.category.id){
                    return "Edit "+ this.category.title
                }
                else
                    return "Create Category"

            },
            loading(){
                return this.$store.getters.loading;
            }
        }
    }
</script>

<style scoped>

</style>
