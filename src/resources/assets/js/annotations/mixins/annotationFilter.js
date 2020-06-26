import {Typeahead} from '../import';

export default {
    template: `<typeahead
            :items="items"
            :placeholder="placeholder"
            :value="selectedItemName"
            @select="select"
        ></typeahead>`,
    components: {
        typeahead: Typeahead,
    },
    data() {
        return {
            name: '',
            placeholder: '',
            selectedItem: null,
        };
    },
    computed: {
        items() {
            return [];
        },
        selectedItemName() {
            return this.selectedItem ? this.selectedItem.name : '';
        },
    },
    methods: {
        select(item) {
            this.selectedItem = item;
        },
        filter(annotations) {
            return annotations;
        },
        reset() {
            this.selectedItem = null;
        },
    },
    watch: {
        selectedItem(item) {
            if (item) {
                this.$emit('select', this);
            } else {
                this.$emit('unselect');
            }
        },
    },
    created() {
        this.$mount();
    },
};
