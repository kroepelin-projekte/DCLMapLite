L.DomEvent.on(document, "DOMContentLoaded", () => {

    try {
        const mapIds = document.querySelectorAll('.map-id');
        for (let i = 0; i < mapIds.length; i++) {
            const container = mapIds[i];
            const viewpoint_data = JSON.parse(container.querySelector('#viewpoint_data').innerText);
            const canvas = container.querySelector('#map-container');
            const map = L.map(container.querySelector('#map-container'));
            const linkListGrp = container.querySelector('#result-list');
            const latLngMemberBounds = [];
            const groups = createGroupsFromHtml(container, linkListGrp, latLngMemberBounds, map);
            dclMap(container, canvas, groups, linkListGrp, latLngMemberBounds, map);
        }
    }
    catch (e) {
    }
});
function iconMarker(identifier, markerId, color) {
    let html = "";
    let classname = identifier + "_";
    let styleId = '#' + markerId;
        html = '<svg class="' + classname + 'svg' + '" id="' + markerId + '" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 48 48" ' +
            'style="enable-background:new 0 0 48 48; margin-left: -17px; margin-top: -38px;width: 45px;height: 45px" xml:space="preserve">' +
            '<style type="text/css">' + styleId + '{fill:' + color + ';stroke:#FFFFFF;stroke-miterlimit:10;}' + styleId + ' circle{fill:#F4F4F4;}</style>' +
            '<path class="' + classname + '" d="M19.8,40.2C14.6,35.6,8.2,29.8,7.6,21l0-0.2C7.5,16.4,9.1,12.2,12.2,9c3.1-3.2,7.4-5,11.8-5l0.1,0' +
            'c4.4,0,8.7,1.8,11.8,5c3.1,3.2,4.7,7.4,4.5,11.8l0,0.2c-0.6,8.8-7,14.5-12.2,19.2L24,44L19.8,40.2z"/>' +
            '<circle class="' + classname + '" cx="24" cy="20" r="4.8"/>' +
            '</svg>';

    return L.divIcon({ className: 'ship-div-icon', html: html });
}
const hasContent = (htmlString) => {
    const textContent = new DOMParser().parseFromString(htmlString, 'text/html').body.textContent.trim();
    return textContent !== '';
};
const getContent = (htmlString) => {
    return new DOMParser().parseFromString(htmlString, 'text/html').body.textContent.trim();
};

function createMarker(container, mapId, member, grp) {

    this.institution = container.querySelector('#li-btn-' + member.id + ' p.institution').outerHTML;
    this.street = container.querySelector('#li-btn-' + member.id + ' p.street').outerHTML;
    this.zip = container.querySelector('#li-btn-' + member.id + ' p.zip').outerHTML;
    this.website = container.querySelector('#li-btn-' + member.id + ' a.website').outerHTML;



    let popupContent = this.institution + this.street + this.zip + "<br />";

    if (this.website && hasContent(this.website)) {
        popupContent += this.website + "<br />";
    }

    return L.marker(
        new L.LatLng(member.lat, member.lon),
        {
            id: member.id,
            icon: iconMarker(grp.identifier, 'marker_' + mapId + '_' + member.id, JSON.parse(container.querySelector('#viewpoint_data').innerText).lbl_location_marker),
            opacity: 0.7,
            className: grp.identifier,
            riseOnHover: true,
            riseOffset: 1000,
            alt: member.lat + '/' + member.lon + ' ' + grp.identifier + ' ' + 'marker'
        }).bindPopup(popupContent).openPopup();
}

/**
 * Creates groups from HTML elements and returns an array of group objects.
 * @returns {Array} The array of group objects.
 */
function createGroupsFromHtml(container, linkListGrp, latLngMemberBounds, map) {
    let mapId = container.id;
    let groupContainers = container.querySelectorAll('.dc_marker_group');
    let groups = [];
    for (let i = 0; i < groupContainers.length; i++) {
        const groupData = JSON.parse(groupContainers[i].innerText);
        const group = {
            id: groupContainers[i].dataset.id,
            title: groupContainers[i].dataset.title,
            color: groupContainers[i].dataset.color,
            identifier: groupContainers[i].dataset.identifier,
            markerList: [],
            htmlElementList: []
        };

        Object.entries(groupData).forEach(([key, member]) => {

            group.markerList[member.id] = createMarker(container, mapId, member, group);
            latLngMemberBounds.push([member.lat, member.lon]);
            const li = container.querySelector('#li_' + member.id);
            const li_btn = container.querySelector('#li-btn-' + member.id);
            const li_btns = container.querySelectorAll('.item-inner');
            li.onclick = (e) => {
                li_btns.forEach((c) => {
                    c.removeAttribute('active');
                });
                li_btn.setAttribute('active', '');
                const position = new L.LatLng(li.dataset.lat, li.dataset.lng);
                map.flyTo(position, 11);
            };
        });
        groups.push(group);
    }
    return groups;
}

/**
 * Function that maps data onto a leaflet map based on the provided parameters.
 *
 * @param {HTMLElement} container - The container element that holds the map display.
 * @param {HTMLElement} canvas - The canvas element used for displaying the map.
 * @param {Array} groups - Array of groups to be mapped.
 * @param {HTMLElement} linkListGrp - The element containing the list of links for groups.
 * @param {Array} latLngMemberBounds - Array of latitude and longitude member bounds.
 * @param {Object} map - The Leaflet map object.
 * @return {Object} - An object containing various mapping functions and properties.
 */
function dclMap(container, canvas, groups, linkListGrp, latLngMemberBounds, map) {
    const viewpoint_data = JSON.parse(container.querySelector('#viewpoint_data').innerText);
    const viewpoint = {
        marker: L.marker(
            [],
            {
                icon: iconMarker('viewpoint', 'my_location', "#3A4C65"),
                opacity: 0.8,
                zIndexOffset: 1000,
                alt: 'My Location Marker',
                title: "",
                riseOnHover: true,
            }),
        circle: L.circle(
            [],
            {
                color: viewpoint_data['lbl_location_circle'],
                fillColor: viewpoint_data['lbl_location_circle'],
                fillOpacity: 0.1,
            }),
        adress: {
            street: container.querySelector('.dcl_form_street') ?? "",
            zip: container.querySelector('.dcl_form_zip') ?? "",
            city: container.querySelector('.dcl_form_city') ?? "",
            status: false
        },
        display: container.querySelector('.rs-label'),
        slider: container.querySelector('.geo-slide-range'),
        checkInput() {
            if (
                this.adress.street.value !== "" ||
                this.adress.zip.value !== "" ||
                this.adress.city.value !== "") {
                this.adress.status = true;
            } else {
                this.adress.status = false;
            }
        },
        create(point) {
            this.createMarker(point);
            this.createCircle(point);
        },
        createMarker(point) {
            this.marker.setLatLng(new L.LatLng(point.lat, point.lon));
            this.marker.fontsize = "60px";
            this.marker.addTo(map);
        },
        createCircle(point) {
            this.circle.setLatLng([point.lat, point.lon]);
            this.circle.setRadius(25000);
            this.slider.disabled = false;
            this.display.style.visibility = "visible";
            this.updateDisplay();
            this.sortListByLatLng(this.circle.getLatLng());
            this.circle.addTo(map);
        },
        update() {
            this.updateCircle();
            this.updateDisplay();
        },
        updateCircle() {
            this.circle.setRadius(this.slider.value);
            this.display.children[1].innerHTML = " " + ((this.slider.value / 1000) * 2);
            this.circle.redraw();
        },
        updateDisplay() {
            this.display.children[1].innerHTML = (this.circle.getRadius() * 2 / 1000);
            this.slider.value = this.circle.getRadius();
        },
        getAdress() {
            return (viewpoint_data['lbl_location_marker'] !== "" ? viewpoint_data['lbl_location_marker'] + "<br>" : "") +
                (this.adress.street.value !== "" ? this.adress.street.value + "<br>" : "") +
                (this.adress.zip.value !== "" ? this.adress.zip.value + "<br>" : "") +
                (this.adress.city.value !== "" ? this.adress.city.value + "<br>" : "");
        },
        reset() {
            this.marker.remove();
            this.circle.remove();
            this.slider.disabled = true;
            this.slider.value = 62500;
            this.display.style.visibility = "hidden";
            this.adress.street.value = '';
            this.adress.zip.value = '';
            this.adress.city.value = '';
            this.adress.status = false;

            const allListItems = container.querySelectorAll('#result-list > li');
            allListItems.forEach((li) => {
                li.classList.add('inner');
                li.classList.remove('hide');
            });

            this.sortListByLatLng(L.latLngBounds(latLngMemberBounds).getCenter());
        },
        sortListByLatLng(point) {
            let viewpointPoint = L.latLng(point.lat, point.lng);
            let listItems = Array.from(container.querySelectorAll('#result-list > li'));
            listItems.sort((a, b) => {
                let aLatLng = L.latLng(a.dataset.lat, a.dataset.lng);
                let bLatLng = L.latLng(b.dataset.lat, b.dataset.lng);
                return viewpointPoint.distanceTo(aLatLng) - viewpointPoint.distanceTo(bLatLng);
            });
            let resultList = container.querySelector('#result-list');
            resultList.innerHTML = '';
            listItems.forEach((li) => resultList.appendChild(li));
        },
        search() {
            return 'https://nominatim.openstreetmap.org/search?' +
                'format=json&countrycodes=de&limit=1&addressdetails=1' +
                '&street=' + this.adress.street.value +
                '&postalcode=' + this.adress.zip.value +
                '&city=' + this.adress.city.value;
        }
    }

    const Group = L.Class.extend({
        initialize: function (id, name, color, identifier, linkList, markerList, options) {
            this.id = id;
            this.name = name;
            this.color = color;
            this.activeStatus = true;
            this.identifier = identifier;
            this.listGroup = container.querySelectorAll('.grp_' + this.id);
            this.marker = markerList;
            this.lay = L.layerGroup().addTo(map);
            this.btn = container.querySelector('#tgl-btn-' + this.id);
            this.setLinkList(container.querySelectorAll('.grp_' + this.id));
            this.activeGroupToggle();
            L.setOptions(this, options);
            this.update();
        },
        setGroupButtonColor() {
            if (this.activeStatus) {
                this.btn.style.backgroundColor = this.color;
                this.btn.style.borderColor = "#FFF";
                this.btn.style.color = "#FFF";
            } else {
                this.btn.style.backgroundColor = "#FFF";
                this.btn.style.borderColor = this.color;
                this.btn.style.color = this.color;
            }
        },
        setLinkList(listGroup) {
            listGroup.forEach((l) => {
                linkListGrp.appendChild(l);
            });
        },
        activeGroupToggle() {
            this.btn.onclick = (e) => {
                this.setActiveStatus(!this.activeStatus);
                Array.from(this.listGroup).forEach((e) => {
                    this.setElement(e);
                });
            }
        },
        setActiveStatus(active) {
            this.activeStatus = active;
            this.setGroupButtonColor(active);
        },
        hideElement(e) {
            e.classList.add('hide');
            this.lay.removeLayer(this.marker[e.dataset.id]);
        },
        showElement(e) {
            e.classList.remove('hide');
            this.lay.addLayer(this.marker[e.dataset.id]);
        },
        setElement(e) {
            if (this.activeStatus && e.classList.contains('inner')) {
                this.showElement(e);
            } else {
                this.hideElement(e);
            }
        },
        updateForMember(e, member) {
            if (map.hasLayer(viewpoint.circle)) {
                if (
                    L.latLng(member.marker._latlng.lat, member.marker._latlng.lng)
                        .distanceTo(viewpoint.circle.getLatLng()) <= viewpoint.circle.getRadius()
                ) {
                    member.link.classList.add('inner');
                    member.link.classList.remove('hide');
                } else {
                    member.link.classList.remove('inner');
                    member.link.classList.add('hide');
                }
            } else {
                member.link.classList.add('inner');
                member.link.classList.remove('hide');
            }
        },
        update() {

            Array.from(this.listGroup).forEach((e) => {
                let member = { link: e, marker: this.marker[e.dataset.id] };
                this.updateForMember(member.link, member);
                this.setElement(member.link);
            });
        },
        reset() {
            this.activeStatus = true;
            this.setGroupButtonColor();
            this.update();
        }
    });

    const lGArray = {};
    groups.forEach((el) => {
        lGArray[el.id] = new Group(el.id,
            el.title,
            el.color,
            el.identifier,
            el.htmlElementList,
            el.markerList);
    });

    L.DomEvent.on(container.querySelector('#search-button'), 'click', function (ev) {
        viewpoint.checkInput();
        if (viewpoint.adress.status) {
            fetch(viewpoint.search())
                .then(result => result.json())
                .then(parsedResult => {
                    viewpoint.create(parsedResult[0]);
                    Object.entries(lGArray).forEach(([k, v]) => {
                        v.update()
                    });
                    map.flyToBounds(viewpoint.circle.getBounds(),
                        { targetZoom: 18, paddingBottomRight: [20, 80] });
                });
        } else {
            viewpoint.adress.city.focus();
        }
    });

    const performReset = () => {
        resetDclMap();
    };

    const updateViewpointAndBounds = () => {
        viewpoint.update();
        Object.entries(lGArray).forEach(([k, v]) => {
            v.update()
        });
        map.flyToBounds(viewpoint.circle.getBounds(), { targetZoom: 18, paddingBottomRight: [20, 80] });
    };
    const createOnKeyActionWithElement = (element) => {
        L.DomEvent.on(element, 'keyup', onKeyAction);
    };

    L.DomEvent.on(container.querySelector("#clear-button"), 'click', performReset);
    L.DomEvent.on(viewpoint.slider, 'change', updateViewpointAndBounds);
    createOnKeyActionWithElement(viewpoint.adress.street);
    createOnKeyActionWithElement(viewpoint.adress.zip);
    createOnKeyActionWithElement(viewpoint.adress.city);

    function resetDclMap() {
        const btnTable = container.querySelector('#result-list-tabs');
        map.flyTo([viewpoint_data.lbl_latitude, viewpoint_data.lbl_longitude], 7);
        map.closePopup();
        Object.entries(btnTable.children).forEach(([key, value]) => {
            if (!value.classList.contains('active')) {
                value.classList.add('active');
            }
        });
        container.querySelectorAll('.item-inner').forEach((e) => {
            e.removeAttribute('active');
        });

        viewpoint.reset();

        Object.entries(lGArray).forEach(([k, group]) => {
            group.update();
            group.reset();
        });
    }

    function onKeyAction(e) {
        if (e.key === 'Enter') {
            container.querySelector('#search-button').click();
        }
    }

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);
    map.setView([viewpoint_data.lbl_latitude, viewpoint_data.lbl_longitude], 7);

}
