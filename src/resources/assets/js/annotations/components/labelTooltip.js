import AnnotationTooltip from '../mixins/annotationTooltip';

/**
 * Tooltip showing labels of the hovered annotations.
 *
 * @type {Object}
 */
export default {
    mixins: [AnnotationTooltip],
    template:
    `<div class="annotation-tooltip">
        <ul class="annotation-tooltip__annotations">
            <li v-for="names in annotationLabels">
                <ul class="annotation-tooltip__labels">
                    <li v-for="name in names" v-text="name"></li>
                </ul>
            </li>
        </ul>
    </div>`,
    computed: {
        annotationLabels() {
            return this.annotations.map(function (annotation) {
                return annotation.labels.map(function (annotationLabel) {
                    return annotationLabel.label.name;
                });
            });
        },
    },
};
