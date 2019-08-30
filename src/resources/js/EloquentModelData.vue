<template>
    <tr :id="row.id"
        :class="computed_class"
        class="flex-row flex-nowrap text-left eloquent-table-row">


        <td @click.exact="select"
            style="cursor: pointer; max-width:25px"
            @click.ctrl.exact="row.selected = !row.selected">
            <i class="material-icons">more_vert</i>
        </td>

        <td class="col" v-for="(value, key) in row" v-if="showColumn(key)">
            <div v-if="typeof value === 'object'">
                <button @click="viewObjectData(value)" class="btn btn-dark text-nowrap">
                    View Data
                </button>
            </div>
            <div v-else>{{value}}</div>
        </td>

        <eloquent-model-controls :show="selected" :el="$el" :selected-model="data" @action="handleAction"/>

    </tr>
</template>

<script>
    import EloquentObjectViewModal from "./EloquentObjectViewModal";

    export default {
        name: "EloquentModelData",
        components: {
            'eloquent-model-controls':require('./EloquentModelControls').default
        },
        props: {
            meta: {},
            row: {},
            selected: {
                default: false
            }
        },
        data() {
            return {
                data: {},
                model_data: {}
            }
        },
        created() {
            this.data = this.row;
            this.model_data = this.meta;

        },
        computed: {
            computed_class() {
                let str = "";
                str += this.row.selected ? ' bulk_selected' : '';
                str += this.selected ? ' selected' : '';
                return str;
            }
        },
        methods: {
            handleAction(action) {
                this.$emit('action', {
                    action, model:this.row
                })
            },
            select(event) {
                this.$emit('select', this.$el, this.row)
            },

            showColumn(key) {
                return _.includes(this.model_data.columns, key)

            },
            viewObjectData(value) {
                this.$modal.show(EloquentObjectViewModal, {
                    data: value,
                }, {
                    scrollable: true,
                    height: 'auto',
                });
            },
        }
    }
</script>

<style lang="scss">
    .eloquent-table-row {
        td {

            padding-top: 0;

            -webkit-transition: padding-top 500ms;
            -moz-transition: padding-top 500ms;
            -ms-transition: padding-top 500ms;
            -o-transition: padding-top 500ms;
            transition: padding-top 500ms;
        }

    }

    .selected {


    }
</style>
