<?php

class Timlabel {
    function getLabel($key) {
        $labels = array(
            "uri" => "URI",
            "dataSetID" => "Dataset ID",
            "dataSetName" => "Dataset name",
            "ownerId" => "Owner ID",
            "published" => "Published",
            "promoted" => "Promoted",
            "title" => "Title",
            "description" => "Description",
            "imageUrl" => "Image URL",
            "owner" => "Owner",
            "contact" => "Contact",
            "provenanceInfo" => "Provenance info",
            "license" => "License",
            "collection" => "Collection",
            "collectionList" => "Collection list",
            "dataSetImportStatus" => "Dataset import status",
            "importStatus" => "Import status",
            "archetypes" => "Archetypes",
            "DataSetMetadata" => "Dataset metadata",
            "dataSetId" => "Dataset ID",
            "clusius_Places" => "Clusius places",
            "tim_name" => "Name",
            "tim_country" => "Country",
            "tim_latitude" => "Latitude",
            "tim_longitude" => "Longitude",
            "tim_remarks" => "Remarks",
            "skos_altLabelList" => "Alternative names"
        );
        if (array_key_exists($key, $labels)) {
            return $labels[$key];
        } else {
            return $key;
        }
    }
}

