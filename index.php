<?php

$cell_list = array(
    array("number" => 0, "style" => "top"),
    array("number" => 1, "style" => "top"),
    array("number" => 2, "style" => "top"),
    array("number" => 3, "style" => "top bk-color"),
    array("number" => 4, "style" => "top right"),
    array("number" => 15, "style" => "top left bk-color"),
    array("number" => 16, "style" => "top"),
    array("number" => 17, "style" => "top"),
    array("number" => 18, "style" => "top right bk-color"),
    array("number" => 5, "style" => "right"),
    array("number" => 14, "style" => "left"),
    array("number" => 23, "style" => "left top"),
    array("number" => 24, "style" => "top right bottom"),
    array("number" => 19, "style" => "right"),
    array("number" => 6, "style" => "right bk-color"),
    array("number" => 13, "style" => "left"),
    array("number" => 22, "style" => "left bottom"),
    array("number" => 21, "style" => "bottom bk-color"),
    array("number" => 20, "style" => "bottom right"),
    array("number" => 7, "style" => "right"),
    array("number" => 12, "style" => "left bottom bk-color"),
    array("number" => 11, "style" => "bottom"),
    array("number" => 10, "style" => "bottom"),
    array("number" => 9, "style" => "bottom bk-color"),
    array("number" => 8, "style" => "bottom right")
);

function loadJukugoList($filename) {
    $jukugo_list = array();
    
    if (file_exists($filename)) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            if (!empty($line)) {
                // コンマで分割して配列に格納
                $parts = explode(',', $line);
                if (count($parts) === 3) {
                    // 四字熟語、読み、意味の3つの要素を持つ場合のみ追加
                    $jukugo_list[] = $parts;
                } else {
                    // 要素数が3でない場合、無効な行としてスキップ
                    echo "無効な行: $line\n";
                }
            }
        }
    } else {
        echo "指定されたファイルが存在しません。";
        exit;
    }
    return $jukugo_list;
}

function extraction_jukugo($jukugo_list) {
    $kanji_list = [];
    foreach ($jukugo_list as $jukugo){
        array_push($kanji_list, $jukugo[0]);
    }
    return $kanji_list;
}

function playShiritori($jukugo_list, $num_of_phrases, $max_processing_time) {
    $result_list = [];
    $result_index_list = [];
    $result_split_list = [];
    $first_char_groups = array();

    $kanji_list = extraction_jukugo($jukugo_list);

    foreach ($jukugo_list as $jukugo) {
        $first_char = mb_substr($jukugo[0], 0, 1); // mb_substr を使用してマルチバイト文字列を正しく扱う
        if (!isset($first_char_groups[$first_char])) {
            $first_char_groups[$first_char] = array();
        }
        array_push($first_char_groups[$first_char], $jukugo[0]);
    }

    while (true) {
        $start_time = microtime(true);  // 処理開始時刻を記録
        $result_list = [];
        $random_index = random_int(0, count($jukugo_list) - 1); // random_int を使用する
        array_push($result_list, $jukugo_list[$random_index][0]);

        while (count($result_list) < $num_of_phrases) {
            $last_char = mb_substr($result_list[count($result_list) - 1], -1); // mb_substr を使用

            $found_jukugo = false;
            $possible_next_jukugo_list = isset($first_char_groups[$last_char]) ? $first_char_groups[$last_char] : array();

            $unused_next_jukugo_list = array_filter($possible_next_jukugo_list, function($jukugo) use ($result_list) {
                return !in_array($jukugo, $result_list);
            });

            if (!empty($unused_next_jukugo_list)) {
                $next_jukugo = $unused_next_jukugo_list[array_rand($unused_next_jukugo_list)]; // array_rand を使用
                array_push($result_list, $next_jukugo);
                $found_jukugo = true;
            }

            $elapsed_time = microtime(true) - $start_time;
            if ($elapsed_time > $max_processing_time) {
                break; // 内側のループから抜ける
            }

            if (!$found_jukugo) {
                array_pop($result_list);
                if (count($result_list) === 0) {
                    $random_index = random_int(0, count($jukugo_list) - 1); // random_int を使用
                    array_push($result_list, $jukugo_list[$random_index][0]);
                }
            }
        }

        if (count($result_list) === $num_of_phrases) {
            foreach($result_list as $result) {
                array_push($result_index_list, array_search($result, $kanji_list));
            }
            array_push($result_list, $result_index_list);
            for ($i = 0; $i < count($result_list) - 1; $i++) {
                if ($i == 0) {
                    for ($j = 0; $j < 4; $j++) {
                        array_push($result_split_list, mb_substr($result_list[$i], $j, 1));
                    }
                } else {
                    for ($j = 1; $j < 4; $j++) {
                        array_push($result_split_list, mb_substr($result_list[$i], $j, 1));
                    }
                }
            }
            array_push($result_list, $result_split_list);
            return $result_list;
        }
    }
}

function drag_functions($cell_list, $number) {
    return 'ondragover="allowDrop(event)" ondrop="drop(event, ' . "'cell" . $cell_list[$number]["number"] . "'" . ')" ondragleave="dragLeave(event)"';
}

function find_jukugo_index($result_list) {
    $result_num_list = [];
    $num_result_jukugo = count($result_list) - 1;
    for ($i = 0; $i < $num_result_jukugo; $i++){

    }

}


?>
