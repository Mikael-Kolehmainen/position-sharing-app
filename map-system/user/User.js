class User
{
    constructor(usersData)
    {
        this.usersData = usersData;
        this.markerStyleSheetContent = "";
    }

    addMarkersToMap()
    {
        let className, marker;
        this.markerStyleSheetContent = "";
        for (let i = 0; i < this.usersData.length; i++) {
            marker = L.marker(L.latLng(this.usersData[i].position), {icon: userIcon});
            refreshedLayerGroup.addLayer(marker);
            user_markers.push(marker);

            className = 'user-marker-' + i;
            this.markerStyleSheetContent += '.' + className + '{ background-color: ' + this.usersData[i].color + '; }';

            this.markerStyleSheetContent += '.' + className + '::before { content: ' + '\"' + this.usersData[i].initials + '\"' + '; }';

            marker._icon.classList.add(className);
        }

        this.#updateMarkerStyle();
    }

    #updateMarkerStyle()
    {
        const userMarkerStyle = new Style('user-marker-style');
        userMarkerStyle.removeStyle();

        userMarkerStyle.styleSheetContent = this.markerStyleSheetContent;
        userMarkerStyle.createStyle();
    }
}