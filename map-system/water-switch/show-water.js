let waterLayerGroup = L.layerGroup();
waterLayerGroup.addLayer(L.geoJSON(vaasa));

function showWaterEntities() 
{
    if (map.hasLayer(waterLayerGroup)) {
        map.removeLayer(waterLayerGroup);
    } else {
        waterLayerGroup.addTo(map);
    }
}