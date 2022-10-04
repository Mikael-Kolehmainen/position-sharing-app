function showWaterEntities() 
{
    if (map.hasLayer(waterLayerGroup)) {
        map.removeLayer(waterLayerGroup);
    } else {
        waterLayerGroup.addTo(map);
    }
}