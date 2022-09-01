// ONCLICK FOR WATER SWITCH
function showWaterEntities() {
    if (showWaterEnabled) {
        map.removeLayer(waterLayerGroup);
        showWaterEnabled = false;
    } else {
        waterLayerGroup.addTo(map);
        showWaterEnabled = true;
    }
}