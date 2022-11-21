class User
{
    #STYLE_CLASS_NAME = "user-marker-style";
    #MARKER_CLASS_NAME = "user-marker-";

    constructor(usersData)
    {
        this.usersData = usersData;
        
        this.markerStyleSheetContent = "";
        this.user_markers = [];
        this.userIcon = L.divIcon ({
            iconSize: [25, 25],
            iconAnchor: [12.5, 25],
            className: "user-marker",
            popupAnchor: [0, -20]
        });
    }

    addMarkersToMap()
    {
        let marker, markerClassName;
        this.markerStyleSheetContent = "";
        for (let i = 0; i < this.usersData.length; i++) {
            marker = L.marker(L.latLng(this.usersData[i].position), {icon: this.userIcon});
            layerManagement.refreshedLayerGroup.addLayer(marker);
            this.user_markers.push(marker);

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