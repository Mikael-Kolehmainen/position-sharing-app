document.getElementById("water-switch").addEventListener("click", function() 
{
    showWaterEntities();
});

function showWaterEntities() 
{
    let waterLayerGroup = L.layerGroup();
    waterLayerGroup.addLayer(L.geoJSON(vaasa));

    if (typeof showWaterEnabled == "undefined" || !showWaterEnabled) {
        waterLayerGroup.addTo(map);
        showWaterEnabled = true;
    } else {
        map.removeLayer(waterLayerGroup);
        showWaterEnabled = false;
    }
}