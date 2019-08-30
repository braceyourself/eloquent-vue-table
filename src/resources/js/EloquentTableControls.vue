<template>
    <div class="row justify-content-between pb-2">

        <span class="justify-content-start col-3 row">

            <button :disabled="disable_first_button" class="btn-primary" @click="navigate('first')">
                <i class="material-icons">
                    {{meta.first === null ? 'loading':'skip_previous'}}
                </i>
            </button>

            <button :disabled="disable_prev_button"
                    @click="navigate('prev')"
                    class="btn-primary">

                {{meta.prev === null && meta.prev_page_url !== null ? 'Loading...':'Previous Page'}}
            </button>

        </span>

        <div class="col text-center">
            <span v-if="meta.from !== meta.to">{{meta.from}} to </span>
            {{meta.to}} of {{meta.total_count}} total
        </div>

        <span class="col-2 justify-content-end row">
            <button :disabled="disable_next_button"
                    @click="navigate('next')"
                    class="btn-primary">
                {{meta.next === null && meta.next_page_url !== null ? 'Loading...':'Next Page'}}
            </button>
            <button :disabled="disable_last_button" class="btn-primary" @click="navigate('last')">
                <i class="material-icons">skip_next</i>
            </button>

        </span>
    </div>
</template>

<script>
    export default {
        name: "EloquentTableControls",
        props: ['meta'],
        methods: {
            navigate(dir) {
                this.$emit('navigate', dir)
            }
        },
        computed: {
            current_page() {
                return this.meta.current_page;
            },
            last_page() {
                return this.meta.last_page;
            },
            disable_next_button() {
                return this.meta.next_page_url === null;
            },

            disable_prev_button() {
                return this.meta.prev_page_url === null;
            },
            disable_first_button() {
                return this.current_page === 1;
            },
            disable_last_button() {
                return this.current_page === this.last_page;
            }
        },

    }
</script>

<style scoped lang="scss">
    .material-icons {
        font-size: small;
        position: relative;
        top: 3px;
    }


</style>
