<?php
$s = new Mysmarty();
$s->registerPlugin("modifier", 'base64_encode', 'base64_encode');
$tp = new Timpars();
$tq = new Timquery();
$timRights = array();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    $s->assign('logged_in', TRUE);
    $s->assign('user_name', $_SESSION['user_name']);
    $timRights = unserialize((file_get_contents(USER_CACHE . $_SESSION["id"] . '.rca')));
} else {
    $s->assign('logged_in', FALSE);
}

function publish_dataset($id)
{
    global $tq;
    global $s;

    $tq->publish_dataset($id);
    $s = null;
    header('Location: ' . BASE_URL . 'browse/metadata/' . $id);
}

function browse()
{
    //global $timQuery;
    //global $tq;
    global $s;

    $s->assign('head', 'Timbuctoo dataset browser');
    $s->assign('navSets', ds_menu());
    $s->view("browse_datasets_huc");
}

function ds_menu()
{
    global $tq;
    global $s;
    global $timQuery;

    $dataSets = $tq->get_graphql_data($timQuery["datasets"]);
    $dataSets = normalize_datasets($dataSets["data"]["dataSetMetadataList"]);
    error_log(print_r($dataSets, true));
    usort($dataSets, 'cmp_dsTitle');
    $s->assign('sets', $dataSets);
    //$navSets = $s->view2var("sub_datasets");
    $navSets = "";
    return $navSets;
}

function normalize_datasets($dataSets) {
    $retArray = array();
    foreach ($dataSets as $item) {
        $tmpArray = array();
        $tmpArray["dataSetId"] = $item["dataSetId"];
        $tmpArray["dataSetName"] = $item["dataSetName"];
        if (isset($item["title"]["value"])) {
            $tmpArray["title"] = $item["title"]["value"];
        } else {
            $tmpArray["title"] = $item["dataSetName"];
        }
        if (isset($item["description"]["value"])) {
            $tmpArray["description"] = $item["description"]["value"];
        } else {
            $tmpArray["description"] = "-- no description --";
        }
        $retArray[] = $tmpArray;
    }
    return $retArray;
}

function cmp_dsTitle($a, $b)
{

    return strcasecmp($a["title"], $b["title"]);
}

function show_list($set, $id, $cursor = "null")
{
    switch ($id) {
        //case "clusius_PersonsList":
        //    show_person_list($set, $id, $cursor);
        //    break;
        default:
            show_general_list($set, $id, $cursor);
            break;
    }
}

function login($id)
{
    global $tq;

    $_SESSION['logged_in'] = true;
    $_SESSION['hsid'] = $id;
    $user = $tq->whoAmI();
    if ($user) {
        $_SESSION['user_name'] = $user["name"];
        $_SESSION["id"] = $user["id"];
        $rights = serialize($user["rights"]);
        file_put_contents(USER_CACHE . $user["id"] . ".rca", $rights);
    } else {
        $_SESSION['logged_in'] = false;
        unset($_SESSION["hsid"]);
    }

}

function logout()
{
    global $s;

    $_SESSION['logged_in'] = FALSE;
    $s = null;
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    } else {
        header("Location: " . BASE_URL);
    }
}

function show_general_list($set, $id, $cursor)
{
    global $tq;
    global $s;
    global $queryTweak;
    global $timRights;

    //error_log(date("h:i:s"));
    $strippedID = str_replace("List", "", $id);


    if ($cursor != "null") {
        $newCursor = "\"$cursor\"";
    } else {
        $newCursor = $cursor;
    }

    $json = sprintf($queryTweak["general_list"], $set, $id, $newCursor);
    $list = $tq->get_graphql_data($json);
    $resCursor = getCursors($list["data"]["dataSets"][$set][$id]);
    $results = $list["data"]["dataSets"][$set][$id]["items"];
    usort($results, 'cmp_label');
    $s->assign('list', $results);
    $s->assign('cursor', $resCursor);
    $s->assign('id', $id);
    $s->assign('set', $set);
    $s->assign('list_title', $tq->getCollectionTitle($set, $strippedID));
    //$s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections($set));
    $s->assign('head', $tq->getDataSetTitle($set));
    $s->assign('listType', $id);
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        $s->assign('permissions', $timRights[$set]);
    }
    //error_log(date("h:i:s"));
    $s->view('generalList_huc');
}

function create_entity($dataSet, $object)
{
    global $s;
    global $tq;
    $tw = new Timwrite();

    $object = substr($object, 0, strlen($object) - 4);
    $type = "{$dataSet}_{$object}";
    $fields = $tq->buildCreationFields($dataSet, $object);
    $tweakIndex = createTweakIndex($dataSet, $object);
    $schema = $tq->getAdressedSchema($type);
    $editableData = $tw->makeCreateArray($fields, $tweakIndex);
    $objectArray = create_object_array($editableData, $object);
    $obj = create_editor_json($objectArray, "collection/{$dataSet}/{$object}/new");
    //$s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections($dataSet));
    $s->assign('head', $tq->getDataSetTitle($dataSet));
    $s->assign('json', json_encode($obj));
    $s->view('editor_huc');
}

function show_entity($dataSet, $object, $uri)
{
    global $tq;
    global $s;
    global $timRights;
    $tw = new Timwrite();
    $type = "{$dataSet}_{$object}";
    $dec_uri = base64_decode($uri);
    $json = $tq->buildObjectDisplayQuery($dataSet, $object, $dec_uri);
    //die($json);
    $data = $tq->get_graphql_data($json);
    $schema = $tq->getAdressedSchema($type);
    $tweakIndex = createTweakIndex($dataSet, $object);
    $smartyData = $tw->makeOutputArray($data, $schema, $dataSet, $object, $tweakIndex);
    $editURI = BASE_URL . "edit/{$dataSet}/{$object}/{$uri}";
    $dropURI = BASE_URL . "drop/item/{$dataSet}/{$object}/{$uri}";
    //$s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections($dataSet));
    $s->assign('head', $tq->getDataSetTitle($dataSet));
    $s->assign('editURI', $editURI);
    $s->assign('dataset', $dataSet);
    $s->assign('collection', $object);
    $s->assign('uri', $uri);
    $s->assign('data', $smartyData);
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        $s->assign('permissions', $timRights[$dataSet]);
    }
    $s->view('showCollectionItem_huc');
}

function edit_entity($dataSet, $object, $uri)
{
    global $tq;
    global $s;
    $tw = new Timwrite();
    check_logged_in();
    $type = "{$dataSet}_{$object}";
    $dec_uri = base64_decode($uri);
    $json = $tq->buildObjectDisplayQuery($dataSet, $object, $dec_uri);
    $data = $tq->get_graphql_data($json);
    $schema = $tq->getAdressedSchema($type);
    $tweakIndex = createTweakIndex($dataSet, $object);
    $editableData = $tw->makeEditArray($data, $schema, $dataSet, $object, $tweakIndex);
    $objectArray = create_object_array($editableData, $object);
    $obj = create_editor_json($objectArray, "collection/{$dataSet}/{$object}/{$uri}");
    $obj = add_coll_item_data($object, $obj, $editableData);
    //$s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections($dataSet));
    $s->assign('head', $tq->getDataSetTitle($dataSet));
    $s->assign('json', json_encode($obj));
    $s->view('editor_huc');
}

function check_logged_in() {
    global $s;

    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        unset($s);
        header("location:" . BASE_URL);
    }
}

function drop_item($dataset, $collection, $itemCode)
{
    global $tq;
    global $s;

    $s = NULL;
    $item = base64_decode($itemCode);
    $uri = $tq->dropItem($dataset, $collection, $item);
    refresh_collections($dataset);
    header("Location: " . BASE_URL . "browse/list/{$dataset}/{$collection}List");
}

function edit_settings($dataSet, $object)
{
    global $tq;
    global $s;
    check_logged_in();
    $s->assign('collectionID', $object);
    $object = str_replace("List", "", $object);
    $fields = get_possible_title_fields($dataSet, $object);
    //$type = "{$dataSet}_{$object}";
    //$fields = $tq->getPossibleTitleFields($type);

    $s->assign('collectionTitle', $tq->getCollectionTitle($dataSet, $object));
    if (isset($fields["active"])) {
        $s->assign('active', $fields['active']);
    } else {
        $s->assign('active', "0");
    }
    $s->assign('collectionURI', $fields["uri"]);
    $s->assign('fields', $fields["props"]);
    $s->assign('datasetID', $dataSet);
    //$s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections($dataSet));
    $s->assign('head', $tq->getDataSetTitle($dataSet));
    $s->assign('action', BASE_URL . 'submit');
    $s->view('settings');
}

function get_possible_title_fields($dataset, $object)
{
    global $tq;

    $retArray = array();
    $collection = filter_collection_props($tq->allCollectionProps($dataset), $dataset, $object);
    if (isset($collection["summaryProperties"]["title"]["path"][0]["step"])) {
        $retArray["active"] = $collection["summaryProperties"]["title"]["path"][0]["step"];
    }
    $retArray["props"] = $collection["properties"]["items"];
    $retArray["uri"] = $collection["uri"];
    return $retArray;
}

function filter_collection_props($props, $dataset, $object)
{
    foreach ($props["data"]["dataSets"][$dataset]["metadata"]["collectionList"]["items"] as $item) {
        if ($item["collectionId"] == $object) {
            return $item;
        }
    }
    return null;
}

function create_object_array($data, $object)
{
    $retArray = array();
    $tmpArray = array();
    $buffArray = array();
    $retArray["name"] = $object;
    foreach ($data as $key => $value) {
        $buffArray = array();
        $buffArray["name"] = $key;
        $buffArray["type"] = array(
            "name" => "Value"
        );
        $buffArray["attributes"] = array();
        $buffArray["attributes"]["label"] = $data[$key]["key"];
        if (isset($data[$key]["items"])) {
            $buffArray["items"] = $data[$key]["items"];
            $buffArray["attributes"]["CardinalityMax"] = "unbound";
            $buffArray["attributes"]["duplicate"] = "yes";
        } else {
            $buffArray["value"] = $data[$key]["value"];
            $buffArray["attributes"]["CardinalityMax"] = "1";
        }
        $tmpArray[$key] = $buffArray;
    }
    $retArray["fields"] = $tmpArray;
    return $retArray;
}

function add_coll_item_data($id, $obj, $data)
{
    $obj["record"] = array(array(), array(), array());
    $tmparr = parse_coll_data($id, $data);
    $obj["record"][2]["value"] = $tmparr; // This is not a very elegant solution, but due to inherited CMDI features
    return $obj;
}

//function parse_coll_data($id, $obj, $data)
function parse_coll_data($id, $data)

{
    $retArr = array();
    $dataArr = array();

    foreach ($data as $key => $value) {
        if (isset($value["items"]) && count($value["items"]) > 0) {
            foreach ($value["items"] as $item) {
                $tmpArray = array();
                $tmpArray["name"] = $key;
                $tmpArray["type"] = "element";
                if (isset($item["value"])) {
                    $tmpArray["value"] = $item["value"];
                } else {
                    if (isset($item["uri"])) {
                        $tmpArray["value"] = $item["uri"];
                    } else {
                        $tmpArray["value"] = "";
                    }
                }

                $dataArr[] = $tmpArray;
            }
        }
        if (isset($value["value"]) && trim($value["value"] != "")) {
            $tmpArray = array();
            $tmpArray = array();
            $tmpArray["name"] = $key;
            $tmpArray["type"] = "element";
            $tmpArray["value"] = $value["value"];
            $dataArr[] = $tmpArray;
        }
        if (isset($value["uri"]) && trim($value["uri"] != "")) {
            $tmpArray = array();
            $tmpArray = array();
            $tmpArray["name"] = $key;
            $tmpArray["type"] = "element";
            $tmpArray["value"] = $value["uri"];
            $dataArr[] = $tmpArray;
        }
    }
    $retArr["name"] = $id;
    $retArr["type"] = "component";
    $retArr["value"] = $dataArr;
    return $retArr;
}

function edit_metadata_nw($id)
{
    global $tq;
    //global $tp;
    global $s;
    check_logged_in();
    $schema = $tq->get_graphql_data($tq->getSchema("DataSetMetadata"));
    $inputFields = $tq->get_graphql_data($tq->getInputFields("DataSetMetadataInput"));
    $tweak = get_tweak_file("DataSetMetadata.json");
    // $json now hard coded, but must be derived from $schema and $inputFields
    $json = "query metadata{ dataSets { $id {DataSetMetadata: metadata {title {value} description {value} imageUrl {value} owner {name {value} email {value}} contact {name {value} email {value}} provenanceInfo {title {value} body {value}} license {uri}}}}}";
    $data = $tq->get_graphql_data($json);

    $tweak = index_object_array($tweak["data"]["tweaks"]);
    $objectArray = filter_input_fields($schema, $inputFields);
    $objectArray = extend_objects($objectArray);
    $objectArray = index_object_array($objectArray);
    $objectArray = merge_tweak($objectArray, $tweak);
    $obj = create_editor_json($objectArray, "metadata/{$id}");
    //$s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections($id));
    $s->assign('head', $tq->getDataSetTitle($id));
//    print_r($data);
//    die();
    $obj = add_data($id, $obj, $data);
    $s->assign('json', json_encode($obj));
    $s->view('editor_huc');
}

/*function createEntityEditor($data, $schema, $dataSet, $object, $tweakIndex) {
    echo "=== DATA ===";
    print_r($data);
    echo "=== SCHEMA ===";
    print_r($schema);
    echo "=== Tweaks ===";
    print_r($tweakIndex);
    die($dataSet . "_" . $object);
}*/

function go_home()
{
    global $s;
    $s = NULL;
    header('Location: ' . BASE_URL);
}

function createTweakIndex($dataSet, $obj)
{
    $retArray = get_tweak_file("{$dataSet}_{$obj}.json");
    if (isset($retArray["data"]["tweaks"])) {
        $retArray = index_object_array($retArray["data"]["tweaks"]);
    }
    return $retArray;
}

/*function show_person($cUri)
{
    global $tq;
    global $s;
    global $queryTweak;

    $uri = base64_decode($cUri);
    $json = str_replace("#URI#", $uri, $queryTweak["clusius_Persons"]);
    $result = $tq->get_graphql_data($json);
    $person = parsePersonResult($result["data"]["dataSets"]["u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo"]["clusius_Persons"]);
    $s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections("u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo"));
    $s->assign('head', $tq->getDataSetTitle("u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo"));
    $editURI = BASE_URL . "edit/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Persons/{$cUri}";
    $dropURI = BASE_URL . "drop/u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo/clusius_Persons/{$cUri}";
    $s->assign('editURI', $editURI);
    $s->assign('dropURI', $dropURI);
    $s->assign("person", $person);
    $s->view("show_person_huc");
}*/

/*function parsePersonResult($struc)
{
    $retArray = array();
    $retArray["name"] = buildName($struc["tim_namesList"]["items"][0]["value"]);
    $retArray["aka"] = array();
    for ($i = 1; $i < count($struc["tim_namesList"]["items"]); $i++) {
        $retArray["aka"][] = buildName($struc["tim_namesList"]["items"][$i]["value"]);
    }
    $retArray["birth_date"] = set_date($struc["tim_birthDate"]);
    $retArray["death_date"] = set_date($struc["tim_deathDate"]);;
    $retArray["birth_place"] = show_place($struc["tim_hasBirthPlace"]);
    $retArray["death_place"] = show_place($struc["tim_hasDeathPlace"]);
    $retArray["gender"] = set_gender($struc["tim_gender"]);
    $retArray["member"] = set_member($struc["_inverse_tim_hasMember"]);
    $retArray["residence"] = set_residence($struc["_inverse_tim_hasResidentList"]);
    $retArray["bio_uri"] = set_uri($struc["_inverse_tim_isScientistBioOf"]["uri"]);
    $retArray["bio"] = set_bio($struc["_inverse_tim_isScientistBioOf"]["tim_biography"]);;
    $retArray["interest"] = set_interest($struc["_inverse_tim_isScientistBioOf"]["tim_hasFieldOfInterestList"]);
    $retArray["occupation"] = set_activity($struc["_inverse_tim_isOccupationOfList"]);
    $retArray["education"] = set_activity($struc["_inverse_tim_isEducationOfList"]);

//    print_r($retArray);
//    die();
    return $retArray;
}*/
/*
function set_member($member)
{
    $retArray = array();
    if (!is_null($member)) {
        $retArray["uri"] = set_uri($member["uri"]);
        $retArray["title"] = $member["title"]["value"];
    }
    return $retArray;
}

function set_interest($obj)
{
    $retArray = array();
    if (!is_null($obj)) {
        foreach ($obj["items"] as $item) {
            $bufArray = array();
            $bufArray["uri"] = base64_encode($item["uri"]);
            $bufArray["interest"] = $item["tim_value"]["value"];
            $retArray[] = $bufArray;
        }
    }
    return $retArray;
}

function set_activity($obj)
{
    $retArray = array();
    if (!is_null($obj)) {
        foreach ($obj["items"] as $item) {
            $bufArray = array();
            $bufArray["uri"] = set_uri($item["uri"]);
            $bufArray["tim_description"] = $item["tim_description"]["value"];
            $bufArray["years"] = period($item["tim_beginDate"], $item["tim_endDate"]);
            $retArray[] = $bufArray;
        }
    }
    return $retArray;
}

function set_residence($residence)
{
    $retArray = array();
    if (!is_null($residence)) {
        foreach ($residence["items"] as $item) {
            $bufArray = array();
            $bufArray["uri"] = set_uri($item["uri"]);
            $bufArray["location"] = show_place($item["tim_hasLocation"]);
            $retArray[] = $bufArray;
        }
    }
    return $retArray;
}*/
/*
function set_uri($uri)
{
    if (is_null($uri)) {
        return "";
    } else {
        return base64_encode($uri);
    }
}

function set_bio($bio)
{
    if (is_null($bio)) {
        return "";
    } else {
        return $bio["value"];
    }
}

function set_date($date)
{
    if (!is_null($date)) {
        return $date["value"];
    } else {
        return "";
    }
}

function set_gender($gender)
{
    if (!is_null($gender)) {
        return $gender["value"];
    } else {
        return "";
    }
}*/

/*function show_interest($cUri) {
    global $tq;
    global $s;
    global $queryTweak;

    $uri = base64_decode($cUri);
    $list = $tq->get_graphql_data(sprintf($queryTweak["clusius_Fields_of_interest"], $uri));
    $screenList = parseInterest($list["data"]["dataSets"]["u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo"]["clusius_Fields_of_interest"]);
    $s->assign('interest', $screenList);
    $s->view('show_interest');
}*/

/*function parseInterest($list) {
    $retArray = array();
    $retArray["title"] = set_value($list["title"]);
    $retArray["tim_value"] = set_value($list["tim_value"]);
    $retArray["description"] = set_value($list["description"]);
    $retArray["persons"] = set_persons($list["_inverse_tim_hasFieldOfInterestList"]["items"]);
    return $retArray;
}*/

/*function set_value($item) {
    if (is_null($item)) {
        return "";
    } else {
        return $item["value"];
    }
}*/

/*function set_persons($list)
{
    $retArray = array();
    if (count($list)) {
        foreach ($list as $element) {
            $item = array();
            $item["uri"] = base64_encode($element["tim_isScientistBioOf"]["uri"]);
            $item["name"] = personNameAndDates($element["tim_isScientistBioOf"]["tim_namesList"]["items"][0]["value"], $element["tim_isScientistBioOf"]["tim_birthDate"], $element["tim_isScientistBioOf"]["tim_deathDate"]);
            $retArray[] = $item;
        }
    }
    return $retArray;
}*/
/*
function show_place($place)
{
    $retVal = "";
    if (is_null($place)) {
        return "";
    } else {
        if (is_null($place["tim_name"])) {
            if (!is_null($place["tim_country"])) {
                $retVal = $place["tim_country"]["value"];
            }
        } else {
            $retVal = $place["tim_name"]["value"];
            if (!is_null($place["tim_country"])) {
                $retVal = $retVal . ', ' . $place["tim_country"]["value"];
            }
        }
    }
    return $retVal;
}

function show_person_list($set, $id, $cursor = "null")
{
    global $tq;
    global $s;
    global $queryTweak;

    if ($cursor != "null") {
        $newCursor = "\"$cursor\"";
    } else {
        $newCursor = $cursor;
    }

    //$list = $tq->get_graphql_data(sprintf($queryTweak["clusius_PersonsList"], $newCursor));
    $list = $tq->get_graphql_data(sprintf($queryTweak[$id], $newCursor));
    $screenList = makePersonList($list);
    $cursor = getCursors($list["data"]["dataSets"]["u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo"]["clusius_PersonsList"]);
    //$s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections($set));
    $s->assign('head', $tq->getDataSetTitle($set));
    $s->assign('persons', $screenList);
    $s->assign('cursor', $cursor);
    $s->assign('set', $set);
    $s->assign('listType', 'clusius_PersonsList');
    $s->view('personList_huc');
}*/

function getCursors($cursors)
{
    $retArray = array();
    $retArray["prevCursor"] = $cursors["prevCursor"];
    $retArray["nextCursor"] = $cursors["nextCursor"];
    return $retArray;
}
/*
function makePersonList($list)
{
    $retArray = array();
    foreach ($list["data"]["dataSets"]["u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo"]["clusius_PersonsList"]["items"] as $element) {
        $item = array();
        $item["uri"] = base64_encode($element["uri"]);
        $item["name"] = personNameAndDates($element["tim_namesList"]["items"][0]["value"], $element["tim_birthDate"], $element["tim_deathDate"]);
        $retArray[] = $item;
    }
    return $retArray;
}*/

/*function personNameAndDates($nameStruc, $birthDate, $deathDate)
{

    $name = buildName($nameStruc);
    $name = $name . period($birthDate, $deathDate);
    return $name;
}*/

/*function buildName($nameStruc)
{
    $nameArray = json_decode($nameStruc, true);
    $name = '';
    if (isset($nameArray["components"][0])) {
        $name = $nameArray["components"][0]["value"];
    }
    if (isset($nameArray["components"][2])) {
        $name = $name . ' ' . $nameArray["components"][2]["value"];
    }
    if (isset($nameArray["components"][1])) {
        $name = $name . ' ' . $nameArray["components"][1]["value"];
    }
    return $name;
}*/

function period($bdArr, $ddArr)
{
    if ($bdArr == NULL) {
        if ($ddArr == NULL) {
            return '';
        } else {
            return " (" . show_year($ddArr["value"]) . ")";
        }
    } else {
        if ($ddArr == NULL) {
            return " (" . show_year($bdArr["value"]) . ")";
        } else {
            return " (" . show_year($bdArr["value"]) . "-" . show_year($ddArr["value"]) . ")";
        }
    }
}

function check_input()
{
    $id = $_POST["ccProfileID"];
    $data = $_POST["ccData"];
    $idParts = explode("/", $id);
    switch ($idParts[0]) {
        case "metadata":
            update_metadata($data, $idParts[1]);
            break;
        case "collection":
            if ($idParts[3] != 'new') {
                update_collection_item($data, $idParts[1], $idParts[2], $idParts[3]);
            } else {
                insert_collection_item($data, $idParts[1], $idParts[2]);
            }
            break;
        default :
            browse();
    }
}

function set_collection_settings()
{
    global $s;
    global $tq;

    $dataset = $_POST["dataset"];
    $collectionUri = $_POST["uri"];
    $object = $_POST["collection"];
    $titleField = $_POST["title_field"];
    $collectionName = $_POST["collection_name"];
    if (!is_numeric($titleField)) {
        $tq->setCollectionItemTitle($dataset, $collectionUri, $titleField);
    }
    $tq->setCollectionTitle($dataset, $object, $collectionName);
    refresh_collections($dataset);
    $s = null;
    header("Location: " . BASE_URL . "browse/list/" . $dataset . "/{$object}");
}

function update_collection_item($data, $dataset, $collection, $uriCode)
{
    global $tq;
    global $s;

    $dataArray = json_decode($data, 'JSON_OBJECT_AS_ARRAY');
    $fields = $dataArray[0]["content"];
    $grqlType = "{$dataset}_{$collection}ReplacementsInput";
    $typeJson = $tq->getInputFields($grqlType);
    $inputFieldTypes = get_input_types($tq->get_graphql_data($typeJson));

    $uri = base64_decode($uriCode);

    foreach ($fields as $field) {
        if (substr($field["name"], -4) <> "List") {
            $query = $tq->editQuery($collection, $dataset, $field["name"]);
            $vars = $tq->valueVariables($uri, $field["name"], $field["content"][0]["value"], $inputFieldTypes[$field["name"]]);
            $value = $tq->getTypeAndValue($dataset, $collection, $field["name"], $uri);
            $json["query"] = $query;
            $json["variables"] = $vars;
            $response = $tq->set_graphql_data(json_encode($json));
        } else {
            $query = $tq->editListQuery($collection, $dataset, $field["name"]);
            $vars = $tq->multiValueVariables(base64_decode($uriCode), $field["name"], $field["content"]);
            $json["query"] = $query;
            $json["variables"] = $vars;
            $response = $tq->set_graphql_data(json_encode($json, JSON_UNESCAPED_SLASHES));
        }
    }
    $s = null;
    header("Location: " . BASE_URL . "show/$dataset/$collection/$uriCode");
}

function get_input_types($arr)
{
    global $tq;

    $retArray = array();
    foreach ($arr["data"]["__type"]["inputFields"] as $field) {
        $retArray[$field["name"]] = $tq->getValueType($field["type"]["name"]);
    }
    return $retArray;
}

/*function update_collection($data, $dataset, $collection, $uri)
{
    //echo $collection;
    die($data);
}*/

function insert_collection_item($data, $dataset, $collection)
{
    global $tq;
    global $s;

    $data = json_decode($data, TRUE);
    if (count($data[0]["content"])) {
        $dataSetName = $tq->getDataSetName($dataset);
        $uri = "http://timbuctoo.huygens.nl/$dataSetName/$collection/" . guidv4(openssl_random_pseudo_bytes(16));
        save_new_collection_item($data, $uri, $dataset, $collection);
        $uriCode = base64_encode($uri);
        update_collection_item(json_encode($data), $dataset, $collection, $uriCode);
    }

    $s = null;
    refresh_collections($dataset);
    header("Location: " . BASE_URL . "browse/list/{$dataset}/{$collection}List");
}

function save_new_collection_item($data, $uri, $dataset, $collection)
{
    global $tq;

    $field = $data[0]["content"][0]["name"];
    $value = $data[0]["content"][0]["content"][0]["value"];
    $type = "xsd_string";
    $result = $tq->createCollectionItem($dataset, $collection, $uri, $field, $value, $type);
}


function update_metadata($data, $id)
{
    global $tq;
    global $s;

    $mutation = array();
    $mutation["query"] = 'mutation setMetadata($dataSet:String!, $metadata:DataSetMetadataInput!){setDataSetMetadata(dataSetId:$dataSet,metadata:$metadata){ title{value} description{value} imageUrl{value} owner{name{value}, email{value}} contact{name{value}, email{value}} provenanceInfo{title{value}, body{value}}}}';
    $mutation["variables"] = $tq->getMetadataQueryVars(json_decode($data, TRUE), $id);    //die(json_encode($mutation));
    $response = $tq->set_graphql_data(json_encode($mutation));
    $s = null;
    header("Location: " . BASE_URL . "browse/metadata/$id");
}

function show_year($dateStr)
{
    $arr = explode("-", $dateStr);
    return $arr[0];
}
/*
function show_object($id)
{
    global $tq;
    global $s;

    $obj = $tq->getObjectFields($id);
    $displayObject = parse_object($obj, $id);
    //$s->assign("name", $obj["data"]["__type"]["name"]);
    $name = $tq->getDataSetTitle($id);
    $s->assign("name", $name);
    $s->assign("lists", $displayObject);
    $s->assign('set', $id);
    $s->view("objectLists");
}*/

function parse_object($obj, $id)
{
    global $labelTweak;
    global $tq;

    $retArray = array();
    $fields = $obj["data"]["__type"]["fields"];

    foreach ($fields as $field) {
        if (substr($field["name"], -4) == 'List') {
            if (is_normal_collection($field["name"])) {
                $collectionTitle = $tq->getCollectionTitle($obj["data"]["__type"]["name"], $field["name"]);
                // if (isset($labelTweak[$field["name"]])) {
                //   $retArray[] = array("label" => $labelTweak[$field["name"]] . " (" . $tq->getTotalFromList($id, $field["name"]) . ")", "name" => $field["name"]);
                //} else {
                //$retArray[] = array("label" => $field["name"] . " (" . $tq->getTotalFromList($id, $field["name"]) . ")", "name" => $field["name"]);
                $retArray[] = array("label" => $collectionTitle . " (" . $tq->getTotalFromList($id, $field["name"]) . ")", "name" => $field["name"]);
                //}
            }
        }
    }
    return $retArray;
}

function is_normal_collection($name)
{
    $values = array(
        "unknown",
        "_ChangeKeyList",
        "_AgentList",
        "_DeletionsList",
        "_DeletionList",
        "_ValueList",
        "_PlanList",
        "_ActivityList",
        "_AssociationList",
        "prov_",
        "static_v5",
        "_OldValueList",
        "_ReplacementList",
        "_ReplacementsList",
        "_AdditionList",
        "_AdditionsList"
    );
    foreach ($values as $value) {
        if (strpos($name, $value)) {
            return false;
        }
    }
    return true;
}

/*function hash_obj($obj)
{
    $retArray = array();
    foreach ($obj as $arr) {
        if ($arr["type"] == 'element') {
            $retArray[$arr["name"]] = $arr["content"][0]["value"];
        }
    }
    return $retArray;
}

function hash_old($uri)
{
    global $tq;
    $retArray = array();
    $result = $tq->getPlace($uri);
    foreach ($result["data"]["dataSets"]["u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo"]["clusius_Places"] as $key => $value) {
        if (is_array($value) && key_exists("value", $value)) {
            $retArray[$key] = $value["value"];
        } else {
            $retArray[$key] = '';
        }
    }
    return $retArray;
}

function set_place_val($uri, $field, $input, $old, $type)
{
    global $tq;

    if (key_exists($field, $input)) {
        if ($input[$field] !== $old[$field]) {
            $tq->setPlace($uri, $field, $input[$field], $type);
        }
    } else {
        $tq->setPlace($uri, $field, '');
    }
}*/

//function edit_metadata($id) {
//    global $tq;
//    global $tp;
//    global $s;
//
//
//    $timData = $tq->get_graphql_data("query metadata{ dataSets { $id {metadata { owner {name {value} email {value}} contact {name {value} email {value}} provenanceInfo {title {value} body {value}} license {uri}}}}}");
//    $obj = $tp->parse($timData["data"]["dataSets"][$id]["metadata"], false, $tq);
//    $s->assign('json', json_encode($obj));
//    $s->view('editor');
//}

function drop_dataset($id)
{
    global $tq;
    global $timRights;

    if (isset($timRights[$id]["REMOVE_DATASET"]) && $timRights[$id]["REMOVE_DATASET"]) {
        refresh_collections($id);
        $tq->dropDataSet($id);
    } else {
        go_home();
    }

}

function create_editor_json($object, $id)
{
    $retObj = array();
    $tmpObj = array();
    $retObj["id"] = $id;
    $retObj["content"] = array();

    $tmpObj["type"] = "Component";
    $tmpObj["ID"] = uniqid();
    $tmpObj["level"] = 1;
    if (isset($object["attributes"])) {
        $tmpObj["attributes"] = $object["attributes"];
    } else {
        $tmpObj["attributes"] = array("CardinalityMin" => "1", "CardinalityMax" => "1");
    }
    $tmpObj["attributes"]["name"] = $object["name"];
    if (!isset($tmpObj["attributes"]["label"])) {
        $tmpObj["attributes"]["label"] = $tmpObj["attributes"]["name"];
    }
    $tmpObj["content"] = parse_fields($object["fields"], 1);
    $retObj["content"][] = $tmpObj;
    return $retObj;
}

function add_data($id, $obj, $data)
{
    $obj["record"] = array(array(), array(), array());
    $tmparr = parse_data($data["data"]["dataSets"][$id]);
    $obj["record"][2]["value"] = $tmparr[0]; // This is not a very elegant solution, but due to inherited CMDI features
    return $obj;
}

function parse_data($obj)
{
    $retArray = array();
    foreach ($obj as $key => $value) {
        $tmpArray = array("name" => $key);
        if (!is_null($value)) {
            if (isset($value["value"])) {
                $tmpArray["type"] = "element";
                $tmpArray["value"] = $value["value"];
            } else {
                if (isset($value["uri"])) {
                    $tmpArray["type"] = "element";
                    $tmpArray["value"] = $value["uri"];
                } else {
                    $tmpArray["type"] = "component";
                    $tmpArray["value"] = parse_data($value);
                }
            }
            $retArray[] = $tmpArray;
        }
    }
    return $retArray;
}

function parse_fields($obj, $level)
{
    $retObject = array();
    $level++;

    foreach ($obj as $element) {
        $tmpArr = array();
        $tmpArr["ID"] = uniqid();
        $tmpArr["level"] = $level;
        if (isset($element["content"])) {
            $tmpArr["type"] = "Component";
        } else {
            $tmpArr["type"] = "Element";
        }
        if (isset($element["attributes"])) {
            $tmpArr["attributes"] = $element["attributes"];
        } else {
            $tmpArr["attributes"] = array();
        }
        $tmpArr["attributes"]["name"] = $element["name"];
        if (!isset($tmpArr["attributes"]["label"])) {
            $tmpArr["attributes"]["label"] = $tmpArr["attributes"]["name"];
        }
        if (isset($element["content"])) {
            $tmpArr["content"] = parse_fields($element["content"]["fields"], $level);
        } else {
            $tmpArr["attributes"]["ValueScheme"] = "string";
        }
        $retObject[] = $tmpArr;
    }
    return $retObject;
}

function index_object_array($obj)
{
    $retArray = array("name" => $obj["name"]);
    if (isset($obj["attributes"])) {
        $retArray["attributes"] = $obj["attributes"];
    }
    $tmpArray = array();
    foreach ($obj["fields"] as $field) {
        $tmpArray[$field["name"]] = $field;
        if (isset($tmpArray[$field["name"]]["content"])) {
            $tmpArray[$field["name"]]["content"] = index_object_array($tmpArray[$field["name"]]["content"]);
        }
    }
    $retArray["fields"] = $tmpArray;
    return $retArray;
}

function merge_tweak($obj, $tweak)
{
    foreach ($tweak["fields"] as $key => $value) {
        if (isset($tweak["fields"][$key]["attributes"])) {
            $obj["fields"][$key]["attributes"] = $tweak["fields"][$key]["attributes"];
        }
        if (isset($obj["fields"][$key]["content"]) && isset($tweak["fields"][$key]["content"])) {
            $obj["fields"][$key]["content"] = merge_tweak($obj["fields"][$key]["content"], $tweak["fields"][$key]["content"]);
        }
    }
    if (isset($tweak["attributes"])) {
        $obj["attributes"] = $tweak["attributes"];
    }
    return $obj;
}

function get_object_inputs($objectName)
{
    global $tq;
    $objectInputs = $objectName . 'Input';

    $schema = $tq->get_graphql_data($tq->getSchema($objectName));
    $inputFields = $tq->get_graphql_data($tq->getInputFields($objectInputs));
    return filter_input_fields($schema, $inputFields);
}

function extend_objects($obj)
{
    foreach ($obj["fields"] as &$element) {
        switch ($element["type"]["name"]) {

            case "Value":
                break;
            default:
                $element["content"] = get_object_inputs($element["type"]["name"]);
        }
    }
    return $obj;
}

function filter_input_fields($schema, $inputFields)
{
    $retArray = array();
    $s = $schema["data"]["__type"]["fields"];
    $i = $inputFields["data"]["__type"]["inputFields"];
    $retArray["name"] = $schema["data"]["__type"]["name"];
    $retArray["fields"] = array();
    foreach ($s as $key => $value) {
        if (is_in_input_fields($value["name"], $i)) {
            $retArray["fields"][] = $s[$key];
        }
    }

    return $retArray;
}

function is_in_input_fields($name, $inputFields)
{
    foreach ($inputFields as $element) {
        if ($element["name"] == $name) {
            return true;
        }
    }
    return false;
}

function get_tweak_file($fileName)
{
    if (file_exists(TWEAK_PATH . $fileName)) {
        $handle = fopen(TWEAK_PATH . $fileName, 'r');
        if ($handle) {
            $json = fread($handle, filesize(TWEAK_PATH . $fileName));
            return json_decode($json, true);
        } else {
            return array();
        }
    } else {
        return array();
    }
}

/*function new_person() {
    global $tq;
    global $tp;
    global $s;

    $timData = $tq->get_graphql_data($tq->getSchema('u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo_schema_Person'));
    $obj = $tp->parse($timData, false, $tq);
    $s->assign('json', json_encode($obj));
    $s->view('editor');
}*/

function show_metadata($dataSet)
{
    //global $timQuery;
    global $s;
    global $tq;
    global $timRights;

    $json = "query metadata{ dataSets { $dataSet {metadata {published title {value} description {value} imageUrl {value} owner {name {value} email {value}} contact {name {value} email {value}} provenanceInfo {title {value} body {value}} license {uri}}}}}";
    $result = $tq->get_graphql_data($json);

    $data = parseMetadataResult($result["data"]["dataSets"][$dataSet]["metadata"]);

    $s->assign('head', $result["data"]["dataSets"][$dataSet]["metadata"]["title"]["value"]);
    //$s->assign('navSets', ds_menu());
    $s->assign('navColl', get_collections($dataSet));
    $s->assign('id', $dataSet);
    $s->assign('data', $data);

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        $s->assign('permissions', $timRights[$dataSet]);
    }

    $s->view('show_metadata_huc');
}

function get_collections($id)
{
    global $s;
    global $tq;

    if (file_exists(COLLECTION_CACHE . $id . '.rca')) {
        $navColl = file_get_contents(COLLECTION_CACHE . $id . '.rca');
    } else {
        $obj = $tq->getObjectFields($id);
        $displayObject = parse_object($obj, $id);
        $s->assign('dataset', $id);
        $s->assign('collections', $displayObject);
        $navColl = $s->view2var('browse_collections_huc');
        file_put_contents(COLLECTION_CACHE . $id . '.rca', $navColl);
    }

    return $navColl;
}

function refresh_collections($id) {
    if (file_exists(COLLECTION_CACHE . $id . '.rca')) {
        unlink(COLLECTION_CACHE . $id . '.rca');
    }
}

function parseMetadataResult($md)
{
    $retArray = array();
    $retArray["Title"] = $md["title"]["value"];
    $retArray["Description"] = $md["description"]["value"];
    $retArray["Image URL"] = $md["imageUrl"]["value"];
    $retArray["Owner"] = showContactInfo($md["owner"]);
    $retArray["Contact"] = showContactInfo($md["contact"]);
    $retArray["Provenance"] = $md["provenanceInfo"]["title"]["value"] . "<br>" . $md["provenanceInfo"]["body"]["value"];
    $retArray["License"] = $md["license"]["uri"];
    if ($md["published"]) {
        $retArray["Published"] = "Yes";
    } else {
        $retArray["Published"] = "No";
    }
    return $retArray;
}

function showContactInfo($md)
{
    $retStr = $md["name"]["value"];
    if ($md["email"]["value"] != "") {
        $retStr .= " (" . $md["email"]["value"] . ")";
    }
    return $retStr;
}
/*
function show_foi()
{
    global $timQuery;
    global $s;
    global $tq;

    $timData = $tq->get_graphql_data($timQuery["clusius_fields_of_interests"]);
    $prefields = $timData["data"]["dataSets"]["u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo"]["clusius_Fields_of_interestList"]["items"];
    $fields = array();
    foreach ($prefields as $prefield) {
        $fields[] = array("uri" => $prefield["uri"], "field" => $prefield["tim_value"]["value"]);
    }
    usort($fields, 'cmp');
    $s->assign('fields', $fields);
    $s->view('clusius_foi');
}*/
/*
function cmp($a, $b)
{
    if ($a["field"] == $b["field"]) {
        return 0;
    } else {
        return ($a["field"] < $b["field"]) ? -1 : 1;
    }
    //return strcmp($a["sortOrder"], $b["sortOrder"]);
}*/

/*function cmp_names($a, $b) {
    if ($a["name"] == $b["name"]) {
        return 0;
    } else {
        return ($a["name"] < $b["name"]) ? -1 : 1;
    }
}*/

function cmp_label($a, $b)
{
    return strnatcasecmp($a["label"]["value"], $b["label"]["value"]);
}

/*function get_editable_object($uri, $type, $single) {
    global $tp;
    global $tq;
    $json = createJSONquery($uri, $type);
    $timArray = $tq->get_graphql_data($json);
    $result = $tp->parse($timArray, $single);
    send_ok(json_encode($result));
}*/

/*function createJSONquery($uri, $type)
{
    switch ($type) {
        case 'simple_person':
            return "query persons {dataSets { u33707283d426f900d4d33707283d426f900d4d0d__rob { schema_Person(uri: \"$uri\") { schema_familyName { value}}}}}";
            break;
        case 'interest_field':
            return "query foi {dataSets {u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo {clusius_Fields_of_interest(uri: \"$uri\") {tim_value {value}}}}}";
            break;
        default:
            return "";
    }
}*/

/*function test()
{
    global $timQuery;
    global $tq;
    $json = $timQuery["simple_names_pre_query"];
    $result = $tq->get_graphql_data($json);
    $json = $tq->setSimplePerson("http://example.org/person1", "Bonzo");
    $result = set_graphql_data($json);
    print_r($result);
}*/
/*
function set_simple_person($uri, $name)
{
    global $timQuery;
    global $tq;

    $json = $timQuery["simple_names_pre_query"];
    $result = $tq->get_graphql_data($json);
    $json = $tq->setSimplePerson($uri, $name);
    $result = $tq->set_graphql_data($json);
    send_ok("OK");
}*/

function guidv4($data)
{
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
/*
function rewrite_simple_person($uri, $struc)
{
    global $timQuery;
    global $tq;
    global $tp;

    $name = $tp->getValueFromStruc($struc);
    $json = $timQuery["simple_names_pre_query"];
    $result = $tq->get_graphql_data($json);
    $json = $tq->setSimplePerson($uri, $name);
    $result = $tq->set_graphql_data($json);
    simple_names();
}

function rewrite_foi($uri, $struc)
{
    global $timQuery;
    global $tq;
    global $tp;

    $name = $tp->getValueFromStruc($struc);
    $json = $timQuery["simple_names_pre_query"];
    $result = $tq->get_graphql_data($json);
    $json = $tq->setSimplePerson($uri, $name);
    $result = $tq->set_graphql_data($json);
    show_foi();
}

function send_ok($msg)
{
    header("HTTP/1.0 200 OK");
    echo $msg;
}

function sendError($error)
{
    header("HTTP/1.0 401 Unauthorized");
    echo $error;
}
*/

/*
 * Function for showing the content of arrays in a log file
 */
function show_content($arr)
{
    $content = print_r($arr, TRUE);
    file_put_contents('/data1/php_var_contents.log', $content);
}
