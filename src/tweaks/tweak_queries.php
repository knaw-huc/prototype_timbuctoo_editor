<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$queryTweak = array();
$queryTweak["clusius_Persons"] = "query Person {dataSets {u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo {clusius_Persons(uri: \"#URI#\") {tim_namesList {items {value}} tim_birthDate {value} tim_deathDate {value} tim_gender {value} tim_hasBirthPlace {tim_name {value} tim_country {value}} tim_hasDeathPlace {tim_name {value} tim_country {value}} _inverse_tim_hasMember {uri title {value} tim_hasLocation {uri tim_name {value} tim_country {value}}} _inverse_tim_hasResidentList {items {uri tim_hasLocation {tim_name {value} tim_country {value}}}} _inverse_tim_isScientistBioOf {uri tim_biography {value} tim_hasFieldOfInterestList {items {uri tim_value {value}}}} _inverse_tim_isOccupationOfList {items {uri tim_description {value} tim_beginDate {value} tim_endDate {value}}} _inverse_tim_isEducationOfList {items {uri tim_description {value} tim_beginDate {value} tim_endDate {value}}}}}}} ";

$queryTweak["clusius_PersonsList"] = "query personList {dataSets {u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo {clusius_PersonsList(cursor: %s, count: 40) {prevCursor nextCursor  items {uri tim_namesList {items {value}} tim_birthDate {value} tim_deathDate {value}}}}}}";

$queryTweak["clusius_Fields_of_interest"] = "query showInterest { dataSets { u33707283d426f900d4d33707283d426f900d4d0d__hpp6demo { clusius_Fields_of_interest(uri: \"%s\") { title { value } tim_value { value }  description { value } _inverse_tim_hasFieldOfInterestList(cursor: null, count: 100) { items { uri tim_isScientistBioOf { uri tim_namesList { items { value } } tim_birthDate { value } tim_deathDate { value } } } } } } } }";

$queryTweak["general_list"] = "query list { dataSets {%s {%s(cursor: %s, count: 10) { prevCursor nextCursor items {uri label: title {value}}}}}}";