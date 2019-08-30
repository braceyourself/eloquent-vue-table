<template>
    <pre v-html="formatted_data"/>
</template>

<script>
    export default {
        name: "EloquentObjectViewModal",
        props: {
            data: {},
        },
        data() {
            return {
                height: 'auto',
            }
        },
        computed: {
            formatted_data() {
                return this.prettyPrint(this.data)
            }
        },
        methods: {
            prettyPrint(obj) {
                var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
                return JSON.stringify(obj, null, 3)
                    .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
                    .replace(/</g, '&lt;').replace(/>/g, '&gt;')
                    .replace(jsonLine, this.replacer);
            },
            replacer(match, pIndent, pKey, pVal, pEnd) {
                var key = '<span style="color:brown">';
                var val = '<span style="color:navy">';
                var str = '<span style="color:olive">';
                var r = pIndent || '';
                if (pKey)
                    r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
                if (pVal)
                    r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
                return r + (pEnd || '');
            },

        },
    }
</script>

<style scoped lang="scss">
    pre {
        background-color: ghostwhite;
        border: 1px solid silver;
        padding: 10px 20px;
        margin: 20px;
    }

</style>
