<template>
    <div :style="computed_style" class="row" :class="show ? 'show':''" id="eloquent-model-controls">

        <button class="btn-danger p-0 material-icons" @click="emitAction">delete</button>
        <button class="btn-primary p-0 material-icons" @click="emitAction">edit</button>
    </div>
</template>

<script>
    export default {
        name: "EloquentModelControls",
        props: {
            selectedModel: {},
            show: {},
            el: {}
        },
        data() {
            return {
                scroll_top: 0,
                effective_scroll_top: null
            }
        },
        methods: {
            emitAction(event) {
                this.$emit('action', event.target.innerText)
            }
        },
        created() {
            window.document.body.onscroll = () => {
                console.log('scrolled');
                this.scroll_top = window.scrollY;
            };
            this.effective_scroll_top = this.scroll_top;
        },
        watch: {
            scroll_top: {
                handler(oldv, newv) {
                    if (!this.show) this.effective_scroll_top = newv;
                }
            },
            selectedModel: {
                handler(newv, oldv) {
                    if (this.show) {
                        this.effective_scroll_top = this.scroll_top;
                    }
                }
            }
        },

        computed: {

            computed_style() {
                let str = "";
                str += `left:${this.left}px;`;
                str += `top:${this.top}px;`;
                str += `width:${this.width - 1}px`;

                return str;
            },
            top() {
                return this.style_data.top + this.effective_scroll_top;
            },
            left() {
                return this.style_data.left;
            },
            style_data() {
                try {
                    return this.element.getBoundingClientRect();
                } catch (e) {
                    return {};
                }
            },
            element() {
                return this.el || {};
            }
        }
    }
</script>

<style lang="scss">
    $panel-height: 40px;

    #eloquent-model-controls {
        background-color: cadetblue;
        color: white;
        position: absolute;
        max-width: 100%;
        margin-left: 1px;
        padding-left: 5px;

        height: 0;
        overflow: hidden;

        box-shadow: inset 0px -6px 11px -9px black;

        -webkit-transition: height 500ms;
        -moz-transition: height 500ms;
        -ms-transition: height 500ms;
        -o-transition: height 500ms;
        transition: height 500ms;

    }

    #eloquent-model-controls.show {
        height: $panel-height;
    }


    .selected.eloquent-table-row {
        td {
            padding-top: $panel-height;
        }
    }

</style>
