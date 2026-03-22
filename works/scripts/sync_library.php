<?php
    function sync_library(string $prefix, string $cache_path, /* bool $bypass_timer = false */) {
        //check for cache directories and create if nonexistent
        if (!(file_exists($cache_path) and is_dir($cache_path))) mkdir($cache_path);
        if (!(file_exists("$cache_path/items") and is_dir("$cache_path/items"))) mkdir("$cache_path/items");

        $metadata = array (
            "version" => 0, //latest item version in the cache
            "last_sync" => 0 //unix timestamp of last library sync
        );
        if (!(file_exists("$cache_path/metadata.json"))) file_put_contents("$cache_path/metadata.json", json_encode($metadata)); //create metadata file if none exists and initialize with zeros
        else $metadata = json_decode(file_get_contents("$cache_path/metadata.json"), true); //otherwise, set metadata

        //if it has been at least 24 hours since the last sync...
        /* if ( $bypass_timer or ((time() - $metadata["last_sync"]) >= 86400) ) { */
            //update last sync time to now
            $metadata["last_sync"] = time();

            $library_url = "https://api.zotero.org/$prefix/items"; //base URL for remote library
        
            //initialize and set options for curl process
            $ch = curl_init();
            curl_setopt_array(
                $ch, 
                array(
                    CURLOPT_URL => "$library_url?since={$metadata["version"]}&format=versions&includeTrashed=1", //downloads list of items in library along with versions
                    CURLOPT_HEADER => true, //use response header as well
                    CURLOPT_RETURNTRANSFER => true //download as string
                )
            );

            //download item list + headers and separate
            $item_list_response = explode("{", curl_exec($ch), 2);

            //convert headers to array
            $item_list_headers_simple = explode(PHP_EOL, $item_list_response[0]); //separate headers into 
            $item_list_headers = array ();
            foreach ($item_list_headers_simple as $num => $header) {
                list($key, $value) = explode(': ', $header);
                $item_list_headers[$key] = $value;
            }
            //read version from headers
            $new_version = (int)$item_list_headers["last-modified-version"];

            //if remote library is newer than cache...
            if ($new_version > $metadata["version"]) {
                //update version number
                $metadata["version"] = $new_version;

                //convert items to array
                $item_list = json_decode("{ $item_list_response[1]", true);
                //evaluate item list for download
                //items that either do not exist in the cache yet, or are older in the cache than their remotes, should be downloaded
                $items_to_download = array(); //initialize array
                foreach ($item_list as $key => $version) { //for each item in the list
                    if (!file_exists("$cache_path/items/$key.txt")) $items_to_download[] = $key; //if it's not cached at all, mark it for download
                    else { //if it *is* cached
                        $stored_item = json_decode(file_get_contents("$cache_path/items/$key.txt")); //check the cached item's version
                        if ($version > $stored_item["version"]) $items_to_download[] = $key; //if it's older than the remote item's version, mark it for download
                    }
                }

                //download items, max 50 at a time, and write to files
                while (count($items_to_download) > 0) { //until there are no more items marked for download...
                    $queued_items = ""; //initialize queue string
                    for ($i = 0; $i < 50; $i++) { //queue max 50 items at a time
                        $item_to_queue = array_shift($items_to_download); //take the next item marked for download, removing it from the array
                        $queued_items .= "$item_to_queue,"; //add it to the queue string
                    }
                    $queued_items = trim($queued_items, ","); //queue is full; remove the final comma

                    //change URL to use the queue and don't use response header
                    curl_setopt_array(
                        $ch, 
                        array(
                            CURLOPT_URL => "$library_url?itemKey=$queued_items&includeTrashed=1",
                            CURLOPT_HEADER => false,
                        )
                    );
                    $new_items = json_decode(curl_exec($ch), true); //download items in queue and convert to array
                    foreach ($new_items as $num => $item) { //for each newly downloaded item
                        $new_item = json_encode($item); //extract as json
                        file_put_contents("$cache_path/items/{$item["key"]}.json", $new_item); //write to file
                    }
                }
            }

            //update metadata file
            file_put_contents("$cache_path/metadata.json", json_encode($metadata));
        /* }
        else echo "library updates once per 24hr!"; */
    }
?>