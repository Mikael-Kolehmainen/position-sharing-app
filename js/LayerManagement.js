class LayerManagement
{
    constructor()
    {
        this.goalLayerGroup = L.layerGroup();
        this.refreshedLayerGroup = L.layerGroup();
        this.draggableRouteLayerGroup = L.layerGroup();
        this.goalWaypointsLayerGroup = L.layerGroup();
    }

    static removeAndClearLayers(layers)
    {
        for (let i = 0; i < layers.length; i++) {
            map.removeLayer(layers[i]);
            layers[i].eachLayer(function(layer) {layers[i].removeLayer(layer)});
        }
    }
}