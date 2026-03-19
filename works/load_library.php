<?php
    $prefix = "groups/6093877";
    $library_url = "https://api.zotero.org/$prefix/items?since=$start_version&format=versions&includeTrashed=1";
    $start_version = file_get_contents("cache/version.txt");

    $curl_options = array(
        CURLOPT_URL => $library_url,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true
    );

    $ch = curl_init();
    curl_setopt_array($ch, $curl_options);

    $item_list_array = explode("{", trim(curl_exec($ch), "}"), 2);

    $item_list_headers_simple = explode(PHP_EOL, $item_list_array[0]);
    $item_list_headers = array ();
    foreach ($item_list_headers_simple as $num => $header) {
        list($key, $value) = explode(': ', $header);
        $item_list_headers[$key] = $value;
    }

    $item_list = array ();
    $item_list_simple = explode(PHP_EOL, $item_list_array[1]);
    foreach ($item_list_simple as $num => $header) {
        list($key, $value) = explode(': ', $header);
        $item_list[$key] = $value;
    }

    //download the items here (see syncing instructions)

    print_r($item_list);


    $new_version = $item_list_headers["last-modified-version"];
    // file_put_contents("cache/version.txt", $new_version);
?>