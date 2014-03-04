<?php
/**
This interface indicates that the given layer implements spherical mercator projection and needs, therefore, special adjustment
when layering over ellipsoidial mercators.

NOTE: According to http://docs.openlayers.org/library/spherical_mercator.html, "WMS layers automatically inherit the
projection from the base layer of a map, so there is no need to set the projection option on the layer."
*/
interface GIS_OpenLayers_Map_Layer_iSphericalMercator {
}

?>
