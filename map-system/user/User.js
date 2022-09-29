class User
{
    #STYLE_CLASS_NAME = "user-marker-style";
    #MARKER_CLASS_NAME = "user-marker-";

    constructor(usersData)
    {
        this.usersData = usersData;
        this.markerStyleSheetContent = "";
    }

    addMarkersToMap()
    {
        let marker, markerClassName;
        this.markerStyleSheetContent = "";
        for (let i = 0; i < this.usersData.length; i++) {
            marker = L.marker(L.latLng(this.usersData[i].position), {icon: userIcon});
            refreshedLayerGroup.addLayer(marker);
            user_markers.push(marker);

            markerClassName = this.#MARKER_CLASS_NAME + i;
            this.markerStyleSheetContent += '.' + markerClassName + '{ background-color: ' + this.usersData[i].color + '; }';

            this.markerStyleSheetContent += '.' + markerClassName + '::before { content: ' + '\"' + this.usersData[i].initials + '\"' + '; }';

            marker._icon.classList.add(markerClassName);
        }

        this.#updateMarkerStyle();
    }

    #updateMarkerStyle()
    {
        const userMarkerStyle = new Style(this.#STYLE_CLASS_NAME);
        userMarkerStyle.removeStyle();

        userMarkerStyle.styleSheetContent = this.markerStyleSheetContent;
        userMarkerStyle.createStyle();
    }
}