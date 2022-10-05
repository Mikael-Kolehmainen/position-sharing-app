class LayerManagement
{
    static removeAndClearLayers(layers)
    {
        for (let i = 0; i < layers.length; i++) {
            map.removeLayer(layers[i]);
            layers[i].eachLayer(function(layer) {layers[i].removeLayer(layer)});
        }
    }
}