import DrawInteraction from '@biigle/ol/interaction/Draw';
import Styles from '../../stores/styles';
import VectorLayer from '@biigle/ol/layer/Vector';
import VectorSource from '@biigle/ol/source/Vector';
import {Keyboard} from '../../import';

/**
 * Mixin for the annotationCanvas component that contains logic for the measure interaction.
 *
 * @type {Object}
 */
let measureLayer;
let measureInteraction;

export default {
    data() {
        return {
            hasMeasureFeature: false,
            measureFeaturePosition: [0, 0],
        };
    },
    computed: {
        isMeasuring() {
            return this.interactionMode === 'measure';
        },
    },
    methods: {
        toggleMeasuring() {
            if (this.isMeasuring) {
                this.resetInteractionMode();
            } else {
                this.interactionMode = 'measure';
            }
        },
        handleMeasureDrawStart(e) {
            measureLayer.getSource().clear();
            this.setMeasureFeature(e.feature);
        },
        updateMeasureFeature(e) {
            this.measureFeaturePosition = e.target.getGeometry().getLastCoordinate();
            this.$emit('changeMeasureFeature', [e.target]);
        },
        setMeasureFeature(feature) {
            this.measureFeature = feature;
            this.hasMeasureFeature = !!feature;

            if (feature) {
                // Set initial tooltip position.
                this.updateMeasureFeature({target: feature});
                feature.on('change', this.updateMeasureFeature);
            }
        },
    },
    watch: {
        isMeasuring(measuring) {
            if (measuring) {
                this.map.addLayer(measureLayer);
                this.map.addInteraction(measureInteraction);
                this.$emit('measuring');
            } else {
                measureLayer.getSource().clear();
                this.setMeasureFeature(undefined);
                this.map.removeLayer(measureLayer);
                this.map.removeInteraction(measureInteraction);
            }
        },
        image() {
            if (this.isMeasuring) {
                // Wait for the new image to be propagated down to the measureTooltip
                // then update it. We have to do this manually since we don't want to
                // process the OpenLayers features reactively (see below).
                this.$nextTick(function () {
                    this.updateMeasureFeature({target: this.measureFeature});
                });
            }
        },
    },
    created() {
        measureLayer = new VectorLayer({
            source: new VectorSource(),
            style: Styles.editing,
            zIndex: 200,
            updateWhileAnimating: true,
            updateWhileInteracting: true,
        });
        measureInteraction = new DrawInteraction({
            source: measureLayer.getSource(),
            type: 'LineString',
            style: measureLayer.getStyle(),
        });
        measureInteraction.on('drawstart', this.handleMeasureDrawStart);
        Keyboard.on('Shift+f', this.toggleMeasuring, 0, this.listenerSet);

        // Do not make this reactive.
        // See: https://github.com/biigle/annotations/issues/108
        this.measureFeature = undefined;
    },
};
