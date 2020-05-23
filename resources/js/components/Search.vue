<script>
    import algoliasearch from 'algoliasearch/lite';
    export default {
        name: "Search",
        data() {
            return {
                searchClient: algoliasearch(
                    '8HWX65C86E',
                    '62fdbc5d05fe68ba9212305d0e7e8beb'
                ),
            };
        },
    }
</script>
