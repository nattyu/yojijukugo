<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'index.php';

$num_of_phrases = 8;  // 8個の四字熟語でしりとりを行う
$max_processing_time = 1;  // 最大処理時間（秒）

$result = "";

if (isset($_POST['A'])) {
    $result = "A";
    $jukugo_filename = "./jukugo_lists/jukugo_full_list_$result.txt"; // ファイル名
    $jukugo_list = loadJukugoList($jukugo_filename);
    $result_shiritori = playShiritori($jukugo_list, $num_of_phrases, $max_processing_time);
}
elseif (isset($_POST['B'])) {
    $result = "B";
    $jukugo_filename = "./jukugo_lists/jukugo_full_list_$result.txt"; // ファイル名
    $jukugo_list = loadJukugoList($jukugo_filename);
    $result_shiritori = playShiritori($jukugo_list, $num_of_phrases, $max_processing_time);
}
elseif (isset($_POST['C'])) {
    $result = "C";
    $jukugo_filename = "./jukugo_lists/jukugo_full_list_$result.txt"; // ファイル名
    $jukugo_list = loadJukugoList($jukugo_filename);
    $result_shiritori = playShiritori($jukugo_list, $num_of_phrases, $max_processing_time);
}
elseif (isset($_POST['D'])) {
    $result = "D";
    $jukugo_filename = "./jukugo_lists/jukugo_full_list_$result.txt"; // ファイル名
    $jukugo_list = loadJukugoList($jukugo_filename);
    $result_shiritori = playShiritori($jukugo_list, $num_of_phrases, $max_processing_time);
}

if ($result){
    $last_index = count($result_shiritori) - 1;
    $answer_list = [];
    $question_list = [];
    $choice_list = [];
    foreach ($result_shiritori[$last_index] as $kanji){
        array_push($answer_list, $kanji);
        array_push($question_list, $kanji);
    }
    for ($i = 0; $i < 18; $i++){
        $random_index = random_int(0, count($answer_list) - 1);
        if ($question_list[$random_index] == ""){
            $i--;
            continue;
        } else {
            array_push($choice_list, $question_list[$random_index]);
            $question_list[$random_index] = "";
        }
    }
    $answer_json = json_encode($answer_list); //JSONエンコード
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="drag_and_drop.js"></script>
    <title>四字熟語しりとり</title>
</head>
<body>
    <h1 class="title">四字熟語しりとり</h1>
    <div id="container-quiz">
        <div id="question-area">
            <table>
                <?php
                    for ($i = 0; $i < count($cell_list); $i++){
                        if ($i % 5 === 0) {
                            echo '<tr>';
                        }
                        if ($question_list[$cell_list[$i]["number"]] != ""){
                            echo '<td id="cell' . $cell_list[$i]["number"] . '" class="' . $cell_list[$i]["style"] . '"' . drag_functions($cell_list, $i) . '><div>' . $question_list[$cell_list[$i]["number"]] . '</div></td>';
                        } else {
                            echo '<td id="cell' . $cell_list[$i]["number"] . '" class="' . $cell_list[$i]["style"] . '"' . drag_functions($cell_list, $i) . '>' . $question_list[$cell_list[$i]["number"]] . '</td>';
                        }
                        
                        if ($i % 5 === 4) {
                            echo '</tr>';
                        }
                    }
                ?>
            </table>
            <div id="button-area">
                <button id="undo-button" onclick="undo()">一手戻す</button>
                <button id="display-hint" type="button" onclick="toggleDisplay()">ヒントを表示</button>
            </div>
            <div id="choices_area" ondragover="allowDrop(event)" ondrop="drop(event, 'choices_area')" ondragleave="dragLeave(event)">
                <?php
                    for ($i = 0; $i < count($choice_list); $i++){
                        echo '<div id="choice' . $i . '" class="choice" draggable="true" ondragstart="drag(event)" ondragend="dragEnd(event)">' . $choice_list[$i] . '</div>';
                    }
                ?>
            </div>

            <script>
                function json_catch(){
                    const answer_array = <?php echo $answer_json; ?>;
                    const answer_js_list = updateAnswer();
                    return answer_array.join("") + " " + answer_js_list.join("");
                }
            </script>
        </div>

        <div id="hint-items-area">
            <?php
            $kanji_index_list_num = count($result_shiritori) - 2;
            $index_list = $result_shiritori[$kanji_index_list_num];
            for ($i = 0; $i < $kanji_index_list_num; $i++) {
                echo '<ul>';
                echo '<li class="kanji-yomi">' . $jukugo_list[$index_list[$i]][0] . '（' . $jukugo_list[$index_list[$i]][1] . '）</li>';
                echo '<li class="imi">' . $jukugo_list[$index_list[$i]][2] . '</li>';
                echo '</ul>';
            }
            ?>
        </div>
    </div>
</body>
</html>