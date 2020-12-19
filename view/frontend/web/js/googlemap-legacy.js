define([
    'jquery',
    'jquery/ui',
    'mage/translate'
], function ($) {
    'use strict';
    // noinspection JSDuplicatedDeclaration
    $.widget('mage.OXGoogleMap', {
        options: {
            apiKey: '',
            responsiveWidth: 768,
            responsive: {
                zoom: 16
            },
            map: {
                zoom: 17,
                zoom_closer: 17,
                center: {
                    lat: null,
                    lng: null
                },
                streetViewControl: true,
                mapTypeId: "roadmap",
                mapTypeControlOptions: {
                    mapTypeIds: []
                }
            },
            styles: [],
            locations: [],
        },
        _create: function () {
            this.markers = [];
            this.map = undefined;
            if (!window.hasOwnProperty('OxFuncGoogleMap')) {
                this.OXInitGoogleMap();
            }
        },
        _init: function () {
            if (window.hasOwnProperty('OxFuncGoogleMap')) {
                window.OxFuncGoogleMap.push($.proxy(this.CreateMap, this));
            } else {
                this._showError(
                    $.mage.__('Initialization element "OxFuncGoogleMap" not defined!')
                );
            }
        },
        _showError: function (message) {
            if (!this.element.find('.message.error').length)
                this.element.html('<div class="message error"><span>' + message + '</span></div>');
        },
        CreateMap: function () {
            this.element.html('');
            let config = this._mapConfig();
            // noinspection JSCheckFunctionSignatures,JSUnresolvedVariable,AmdModulesDependencies
            this.map = new google.maps.Map(this.element[0], config);
            // noinspection JSUnresolvedFunction,JSUnresolvedVariable,AmdModulesDependencies
            if (Array.isArray(this.options.styles) && 0 < this.options.styles.length) {
                // noinspection JSUnresolvedFunction,JSUnresolvedVariable,AmdModulesDependencies
                let styledMapType = new google.maps.StyledMapType(this.options.styles);
                // noinspection JSUnresolvedVariable
                this.map.mapTypes.set("styled_map", styledMapType);
                // noinspection JSUnresolvedFunction
                this.map.setMapTypeId("styled_map");
            }
            // noinspection JSUnresolvedFunction,JSUnresolvedVariable,AmdModulesDependencies
            this.infoWindows = new google.maps.InfoWindow();
            this.ReloadMarkers();
        },
        _mapConfig: function () {
            let map = $.extend({}, this.options.map);
            if (window.innerWidth < this.options.responsiveWidth)
                map = $.extend(map, this.options.responsive);
            if (null === map.center.lat || null === map.center.lng)
                map.center = this._createCenter(this.options.locations);
            if (null === map.center.lat || null === map.center.lng)
                this._showError($.mage.__('Ox Google Map: You need to set central point Latitude and Longitude.'));
            return map;
        },
        ReloadMarkers: function () {
            this.removeOldMarkers();
            if (!this.options.locations.length) {
                this._showError($.mage.__('Ox Google Map: Location markers are missing!'));
                return;
            }
            for (let i = 0; i < this.options.locations.length; i++) {
                // noinspection JSUnresolvedFunction,JSUnresolvedVariable,AmdModulesDependencies
                let marker = new google.maps.Marker(this._prepareLocation(this.options.locations[i]));
                marker.addListener('click', this._clickerHandler(i));
                this.markers[i] = marker;
            }
        },
        _prepareLocation: function (location) {
            // noinspection JSUnresolvedFunction,JSUnresolvedVariable,AmdModulesDependencies
            location.position = new google.maps.LatLng(location.position);
            location.icon = this._prepareIcon(location.icon);
            location.map = this.map;
            return location;
        },
        _clickerHandler: function (i) {
            let _self = this;
            return function () {
                let marker = _self.markers[i];
                _self.map.panTo(this.getPosition());
                // noinspection JSUnresolvedFunction
                if (_self.options.zoom_closer && _self.options.zoom_closer !== _self.options.zoom) {
                    // noinspection JSUnresolvedFunction
                    _self.map.setZoom(_self.options.zoom_closer);
                }
                if (_self.options.locations[i].description) {
                    _self.infoWindows.setContent('<div class="ox-gmap-popup">' + _self.options.locations[i].description + '</div>');
                    _self.infoWindows.open(_self.map, marker);
                }
            };
        },
        removeOldMarkers: function () {
            let i = 0;
            while (i < this.markers.length) { // noinspection JSUnresolvedFunction
                if (this.markers[i++].setMap()) { // noinspection JSUnresolvedFunction
                    this.markers[i++].setMap(null);
                }
            }
            this.markers = [];
        },
        _prepareIcon: function (icon) {
            if ('object' !== typeof icon)
                return icon;
            for (let i in icon)
                if (icon.hasOwnProperty(i))
                    switch (i) {
                        case 'scaledSize':
                        case 'size':
                            // noinspection JSUnresolvedFunction,JSUnresolvedVariable,AmdModulesDependencies
                            icon[i] = new google.maps.Size(parseInt(icon[i][0]), parseInt(icon[i][1]));
                            break;
                        case 'anchor':
                        case 'origin':
                            // noinspection JSUnresolvedVariable,AmdModulesDependencies
                            icon[i] = new google.maps.Point(parseInt(icon[i][0]), parseInt(icon[i][1]));
                            break;
                    }
            return icon;
        },
        _createCenter: function (locations) {
            // noinspection ES6ConvertVarToLetConst,JSDuplicatedDeclaration
            var locations = locations || [];
            if (!locations.length)
                return {
                    lat: null,
                    lng: null,
                };
            let position = {
                    lat: 0,
                    lng: 0,
                },
                i = 0;
            while (i < locations.length) {
                let pos = locations[i++].position;
                position.lat += pos.lat;
                position.lng += pos.lng;
            }
            if (1 === locations.length)
                return position;
            position.lat /= locations.length;
            position.lng /= locations.length;
            return position;
        },
        OXInitGoogleMap: function () {
            if (!this.options.apiKey) {
                this._showError(
                    $.mage.__('Ox Google Map: Google Map API Key is not set ( Olegnax / GoogleMap : Configuration ).')
                );
                return;
            }
            let init = function (apiKey) {
                this.functions = [];
                this.runned = false;
                this.appended = false;
                this.apiKey = apiKey;
                this.funcInit = 'OXInitGoogleMap';
                this.push = function (func) {
                    if ('function' === typeof func) {
                        this.functions.push(func);
                    }
                    if (this.runned) {
                        this.run();
                    } else {
                        this.appendJS();
                    }
                };
                this.appendJS = function () {
                    if (this.appended)
                        return;
                    this.appended = true;
                    let s = document.createElement("script");
                    s.type = "text/javascript";
                    s.async = true;
                    s.defer = true;
                    s.src = "https://maps.googleapis.com/maps/api/js?key=" + this.apiKey + "&callback=" + this.funcInit;
                    window.document.body.append(s);
                };
                this.Run = function () {
                    this.runned = true;
                    while (this.functions.length) {
                        let func = this.functions.shift();
                        if ('function' === typeof func) {
                            func();
                        }
                    }
                };
                let timerId;
                window[this.funcInit] = function () {
                    clearTimeout(timerId);
                    // noinspection JSUnresolvedVariable,AmdModulesDependencies
                    if ('object' === typeof google && google.hasOwnProperty('maps') && 'object' === typeof google.maps) {
                        window.OxFuncGoogleMap.Run();
                    } else {
                        // noinspection JSPotentiallyInvalidUsageOfThis
                        timerId = setTimeout(window[this.funcInit], 50);
                    }
                };
                timerId = setTimeout(window[this.funcInit], 50);
            };
            window.OxFuncGoogleMap = new init(this.options.apiKey);
        }
    });
    return $.mage.OXGoogleMap;
});
