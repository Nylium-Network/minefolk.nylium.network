<?php
    $old_library = json_decode(file_get_contents("library.json"), true);
    $latest_version = 0;
    if($old_library != null and count($old_library) > 0) {
        foreach ($old_library as $i) {
            if ($i["version"] > $latest_version) $latest_version = $i["version"];
        }
    }
    // echo "version: $latest_version\n";

    $zotero = curl_init();
    curl_setopt_array(
        $zotero, 
        array(
            CURLOPT_URL => "https://api.zotero.org/groups/6093877/items?limit=100&since=$latest_version", 
            CURLOPT_RETURNTRANSFER => true
        )
    );
    $library_update = json_decode(curl_exec($zotero), true);
    curl_close($zotero);
    // print_r($library_update);

    if ($library_update !=null and count($library_update) > 0) {
        if ($old_library != null and count ($old_library) > 0) {
            $library = $old_library;
            foreach ($library_update as $ni) {
                $is_new = true;
                foreach ($old_library as $oi) {
                    if ($oi["key"] == $ni["key"]) {
                        $library[array_search($oi, $library)] = $ni;
                        $is_new = false;
                    }
                }
                if ($is_new) $library[] = $ni;
            }
        }
        else $library = $library_update;

        $library_output = json_encode($library, JSON_PRETTY_PRINT);

        $library_file = fopen("library.json", "w");
        fwrite($library_file, $library_output);
        fclose($library_file);
    }
?>