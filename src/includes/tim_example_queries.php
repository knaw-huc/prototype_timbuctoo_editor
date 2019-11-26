<?php
$timQuery = array();

$timQuery["clusius_fields_of_interests"] = "{dataSets {u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo {clusius_Fields_of_interestList {items {uri tim_value {value}}}}}}";
$timQuery["simple_names"] = "query persons { dataSets { u33707283d426f900d4d33707283d426f900d4d0d__rob { schema_PersonList { items { uri schema_familyName { value } } } } }}";
$timQuery["simple_names_pre_query"] = "query beforePersonEdit {dataSets {u33707283d426f900d4d33707283d426f900d4d0d__rob {schema_PersonList {items {uri schema_familyName {value}}}}}}";
$timQuery["person"] = "query clusius_person { dataSets {u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo { clusius_Persons(uri: \"http://timbuctoo.huygens.knaw.nl/datasets/clusius/Persons_PE00002125\") { tim_namesList(count: 20) { items { value } } title { value } description { value } image { value } tim_birthDate { value } tim_hasBirthPlace { tim_name { value } tim_country { value } tim_remarks { value } } tim_deathDate { value } tim_hasDeathPlace { tim_name { value } tim_country { value } tim_remarks { value } } tim_gender { value } } } } }";
$timQuery["place_list"] = "query clusius_place { dataSets { u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo { clusius_PlacesList { items { uri tim_name { value } } } } }}";
$timQuery["datasets"] = "query setNames { dataSetMetadataList(promotedOnly: false, publishedOnly: false) { dataSetId dataSetName title {value} description { value}}}";

