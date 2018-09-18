/**
 * One sub-list item of a list item of the annotations tab
 *
 * @type {Object}
 */
biigle.$component('annotations.components.annotationsTabSubItem', {
    props: {
        item: {
            type: Object,
            required: true,
        },
        userId: {
            type: Number,
            required: true,
        },
    },
    computed: {
        annotation: function () {
            return this.item.annotation;
        },
        label: function () {
            return this.item.annotationLabel;
        },
        isSelected: function () {
            return this.annotation.selected;
        },
        classObject: function () {
            return {
                selected: this.isSelected,
            };
        },
        shapeClass: function () {
            return 'icon-' + this.annotation.shape.toLowerCase();
        },
        username: function () {
            if (this.label.user) {
                return this.label.user.firstname + ' ' + this.label.user.lastname;
            }

            return '(user deleted)';
        },
        canBeDetached: function () {
            return this.label.user && this.label.user.id === this.userId;
        },
        events: function () {
            return biigle.$require('events');
        },
    },
    methods: {
        toggleSelect: function (e) {
            this.$emit('select', this.$el);

            if (this.isSelected) {
                this.events.$emit('annotations.deselect', this.annotation, e);
            } else {
                this.events.$emit('annotations.select', this.annotation, e);
            }
        },
        focus: function (e) {
            this.events.$emit('annotations.focus', this.annotation);
            this.$emit('select', this.$el);
            this.events.$emit('annotations.select', this.annotation, e);
        },
        detach: function () {
            if (this.annotation.labels.length > 1) {
                this.events.$emit('annotations.detachLabel', this.annotation, this.label);
            } else if (confirm('Detaching the last label will delete the annotation. Proceed?')) {
                this.events.$emit('annotations.delete', this.annotation);
            }
        },
    },
});
