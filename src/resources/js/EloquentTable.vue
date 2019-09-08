<template>
    <div>
        <loading :condition="loading">

            <div>Model: {{model}}</div>
            <div>Table: {{table_name}}</div>
            <div>Total: {{model_data.total_count}}</div>


            <button v-if="model_data.create" class="btn btn-primary material-icons">add</button>
            <button v-if="any_selected && model_data.bulk_delete" class="btn btn-danger material-icons">bulk delete
            </button>
            <hr>

            <div id="eloquent-table-container">


                <eloquent-table-controls :meta="meta" @navigate="navigate"/>


                <table id="eloquent-table">

                    <tr class="flex-row flex-nowrap">
                        <th style="max-width:25px"></th>

                        <th v-for="(v,k) in model_data.columns"
                            :id="v +'-column'"
                            class="col">
                            {{v}}
                        </th>
                    </tr>

                    <eloquent-model-data v-for="(row, key) in data.data"
                                         :key="key"
                                         :row="row"
                                         :selected="selected_model && selected_model.id === row.id"
                                         @select="modelSelected"
                                         @action="performAction"
                                         :meta="model_data"/>


                </table>
            </div>

        </loading>

    </div>
</template>

<script>
    import uuid from 'uuid';
    import Client from './client';
    import $ from 'jquery';


    const PAGES = [
        'next', 'prev', 'first', 'last'
    ];

    export default {
        name: "EloquentTable",
        mixins: [Client],
        components: {
            'eloquent-model-data': require('./EloquentModelData').default,
            'eloquent-table-controls': require('./EloquentTableControls').default,
            'loading': require('./Loading').default
        },
        data() {
            return {
                model_data: {
                    total_count: 0
                },
                data: {},
                view_object: null,
                selected_model: null,

            }
        },
        watch: {
            data:{
                handler() {
                    this.$emit('input',this.data)
                }
            }
        },
        created() {
            this.heartbeat();
            this.$store.commit('interval', setInterval(this.heartbeat, this.heartbeat_timeout));
        },

        methods: {
            performAction(payload) {
                switch (payload.action) {
                    case 'edit':
                        break;
                    case 'delete':

                        this.client.delete(payload.model.id);
                        break;
                }
            },
            navigate(direction) {
                if (this.data[direction] === null || this.data[direction] === undefined) return;

                console.log(`showing ${direction} page`);
                console.log(this.data[direction]);


                let from_page = this.data;

                this.data = from_page[direction];
                this.data.last = from_page.last;
                this.data.first = from_page.first;

                switch (direction) {
                    case 'next':
                        this.data.prev = from_page;
                        this.data.next = (from_page.last && from_page.last.current_page == this.data.current_page + 1) ? from_page.last : null;
                        this.data.prev.next = null;
                        break;
                    case 'prev':
                        this.data.next = from_page;
                        this.data.prev = (from_page.first && from_page.first.current_page == this.data.current_page - 1) ? from_page.first : null;
                        this.data.next.prev = null;
                        break;
                    case 'first':
                    case 'last':
                        this.data.next = (from_page.current_page === this.data.current_page + 1 || from_page.prev && from_page.prev.current_page === this.data.current_page + 1) ? from_page.prev : null;
                        this.data.prev = (from_page.current_page === this.data.current_page - 1 || from_page.next && from_page.next.current_page === this.data.current_page - 1) ? from_page.next : null;
                }

                this.$router.replace({
                    query: {
                        page:this.data.current_page
                    }
                });


                this.loadMetaPages();
            },


            loadModelData() {

                this.client.get('meta').then(res => {

                    console.log('reload data?',res.data.total_count !== this.data.total);

                    if (res.data.total_count !== this.data.total) {
                        this.model_data = res.data;

                        this.loadData();
                    }

                });

            },
            loadData() {
                this.client.get('?'+$.param({
                    page:this.data.current_page
                })).then(res => {
                    console.log('reloading data');

                    _.forEach(res.data.data, i => {
                        i.selected = false;
                        if (!i.id) i.id = uuid.v1()
                    });

                    this.data = res.data;

                    this.loadMetaPages()

                });
            },
            loadMetaPages() {
                _.forEach(PAGES, page => {
                    let page_url = _.split(this.data[`${page}_page_url`], `api/braceyourself/${this.resource_slug}`)[1];

                    console.log(`Loading ${page} page via ${page_url}`, this.data);
                    // if(['first','last'].includes(page) && this.)
                    if (page_url && (this.data[page] === null || this.data[page] === undefined)) {
                        this.client.get(page_url).then(res => {
                            console.log(`loaded ${page} page via ${page_url}`);
                            this.data[page] = res.data
                        });
                    }

                });
            },

            heartbeat() {
                this.loadModelData()
            },

            modelSelected(element, model) {
                this.selected_model = this.selected_model && this.selected_model.id === model.id ? null : model;
                if (this.selected_model) {

                    this.selected_model.element = element;
                }
            },
        },
        computed: {
            meta() {
                let data = {
                    total_count: this.model_data.total_count
                };

                _.forEach(this.data, (v, k) => {
                    if (k !== 'data') {
                        data[k] = v;
                    }
                });

                return data;
            },
            loading() {
                return this.data.data === undefined
            },


            resource_slug() {
                return this.model_data.resource_slug;
            },
            table_name() {
                return this.model_data.table;
            },
            any_selected() {
                return this.selected.length > 0;
            },
            selected() {
                return _(this.data.data).filter(v => {
                    return v.selected();
                })
            },
        },
        props: {
            model: {
                type: String
            },
            heartbeat_timeout: {
                default: 5000,
                type: Number
            }
        },
    }
</script>

<style scoped lang="scss">

    #eloquent-table {
        display: block;
        overflow-x: scroll;
        box-shadow: 7px 6px 7px 4px #0000001c;
        border-radius: 2px;
        border: solid 1px blanchedalmond;
        padding: 16px;
        width: 100%;
    }

    .bulk_selected {
        background-color: cadetblue;
        color: white;

    }

    .eloquent-table-row {
        z-index: 1;

        &:hover {
            @extend .bulk_selected;
        }
    }

</style>
