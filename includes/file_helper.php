<?php
// ============================================================
// File helper - icons & labels (including GIS formats)
// ============================================================

function file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function file_type_icon($filename) {
    $ext = file_extension($filename);
    $iconMap = [
        'pdf'  => 'pdf.svg', 'doc' => 'doc.svg', 'docx' => 'doc.svg',
        'xls'  => 'xls.svg', 'xlsx' => 'xls.svg',
        'ppt'  => 'ppt.svg', 'pptx' => 'ppt.svg', 'txt' => 'txt.svg',
        'jpg'  => 'jpg.svg', 'jpeg' => 'jpg.svg', 'png' => 'img.svg',
        'gif'  => 'img.svg', 'bmp' => 'img.svg', 'webp' => 'img.svg',
        'zip'  => 'zip.svg', 'rar' => 'zip.svg', '7z' => 'zip.svg',
        // GIS
        'shp'=>'gis.svg','shx'=>'gis.svg','dbf'=>'gis.svg','prj'=>'gis.svg',
        'sbn'=>'gis.svg','sbx'=>'gis.svg','cpg'=>'gis.svg','qix'=>'gis.svg',
        'geojson'=>'gis.svg','json'=>'gis.svg','kml'=>'gis.svg','kmz'=>'gis.svg',
        'gpx'=>'gis.svg','gpkg'=>'gis.svg','tab'=>'gis.svg','mif'=>'gis.svg',
        'mid'=>'gis.svg','gml'=>'gis.svg','dxf'=>'gis.svg',
        'tif'=>'gis.svg','tiff'=>'gis.svg','geotiff'=>'gis.svg','asc'=>'gis.svg','dem'=>'gis.svg',
    ];
    return 'assets/icons/' . ($iconMap[$ext] ?? 'file.svg');
}

function file_type_label($filename) {
    $ext = file_extension($filename);
    $labelMap = [
        'pdf'=>'PDF','doc'=>'Word','docx'=>'Word','xls'=>'Excel','xlsx'=>'Excel',
        'ppt'=>'PowerPoint','pptx'=>'PowerPoint','jpg'=>'JPG','jpeg'=>'JPG',
        'png'=>'PNG','gif'=>'GIF','txt'=>'Text','zip'=>'ZIP','rar'=>'RAR','7z'=>'7z',
        'shp'=>'Shapefile','shx'=>'SHX','dbf'=>'DBF','prj'=>'PRJ',
        'geojson'=>'GeoJSON','json'=>'JSON','kml'=>'KML','kmz'=>'KMZ','gpx'=>'GPX',
        'gpkg'=>'GeoPackage','tif'=>'GeoTIFF','tiff'=>'GeoTIFF','geotiff'=>'GeoTIFF',
        'asc'=>'ASCII Grid','dem'=>'DEM','tab'=>'MapInfo','mif'=>'MIF','gml'=>'GML','dxf'=>'DXF',
    ];
    return $labelMap[$ext] ?? strtoupper($ext ?: 'FILE');
}

function human_filesize($bytes, $decimals = 2) {
    $size = ['B', 'KB', 'MB', 'GB', 'TB'];
    $factor = (int)floor((strlen((string)$bytes) ? log($bytes, 1024) : 0));
    return sprintf("%.{$decimals}f %s", $bytes / (1024 ** max(0, $factor)), $size[max(0, $factor)]);
}

function allowed_upload_extensions() {
    return [
        'pdf','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png','gif','txt','zip',
        'shp','shx','dbf','prj','sbn','sbx','cpg','qix','geojson','json','kml','kmz','gpx','gpkg',
        'tif','tiff','geotiff','asc','dem','tab','mif','mid','gml','dxf'
    ];
}
