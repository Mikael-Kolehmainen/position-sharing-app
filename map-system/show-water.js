document.getElementById("water-checkbox").addEventListener("click", function() 
{
    showWaterEntities();
});

function showWaterEntities() 
{
    if (showWaterEnabled) {
        map.removeLayer(waterLayerGroup);
        showWaterEnabled = false;
    } else {
        waterLayerGroup.addTo(map);
        showWaterEnabled = true;
    }
}