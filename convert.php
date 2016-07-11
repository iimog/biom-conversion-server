<?php

namespace biomcs;

/**
 * This file converts "content" "to" the desired format (hdf5 or json)
 * The output format is json that contains hdf5 data base64 encoded or json data as part of the output array:
 * Example (to = hdf5):
 * {
 *   content: "iUhERg0KGgoAAAAAAAgIAAQAEAAAAAAAAAAAAAAAAAD\/\/\/\/\/\/\/\/\/\/zCEAAAAAAAA...AAAAAAAAAAAAAAAAAAA",
 *   error: null
 * }
 * Example (to = json):
 * {
 *   content: {"id": "NoID", shape: [5,10], ..., data: [[0,1,15]]},
 *   error: null
 * }
 */

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$to = isset($_REQUEST["to"]) ? $_REQUEST["to"] : false;
$content = isset($_REQUEST["content"]) ? $_REQUEST["content"] : false;
if (!$to) {
    echo json_encode(array("error" => "Missing parameter 'to' please set to 'json' or 'hdf5'"));
} elseif ($to !== 'json' && $to !== 'hdf5') {
    echo json_encode(array("error" => "Illegal value of 'to' parameter please set to 'json' or 'hdf5'"));
} elseif (!$content) {
    echo json_encode(array("error" => "Missing parameter 'content' please send the content to convert"));
} else {
    $biomcs = new BiomCS();
    try {
        if ($to === "json") {
            echo json_encode(
                array(
                    "content" => json_decode($biomcs->convertToJSON($content), true),
                    "error" => null
                ),
                JSON_PRETTY_PRINT
            );
        } elseif ($to === "hdf5") {
            print json_encode(
                array(
                    "content" => base64_encode($biomcs->convertToHDF5($content)),
                    "error" => null
                ),
                JSON_PRETTY_PRINT
            );
        }
    } catch (\Exception $e) {
        echo json_encode(array("error" => $e->getMessage()), JSON_PRETTY_PRINT);
    }
}
