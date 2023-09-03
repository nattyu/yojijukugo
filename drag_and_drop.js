// 操作履歴を記録するスタック
let operationStack = [];

// Answer を更新する処理
function updateAnswer() {
    let cells = document.querySelectorAll('td');
    let answer_list = [];
    let answer_kanji = [];
    cells.forEach(function(cell) {
        let div = cell.querySelector('div');
        if (div) {
            answer_list.push([parseInt(cell.id.substring(4)), div.textContent]);
        }
    });
    answer_list.sort(function(a,b){return(a[0] - b[0]);});
    answer_list.forEach(item => {
        answer_kanji.push(item[1]);
    });

    return answer_kanji;
}

// 一手戻す操作を実行する関数
function undo() {
    if (operationStack.length > 0) {
        let operation = operationStack.pop();
        let sourceElement = document.getElementById(operation.sourceId);
        let prevParent = document.getElementById(operation.prevParentId);
        let prevSibling = operation.prevSiblingId ? document.getElementById(operation.prevSiblingId) : null;

        if (operation.state === "change") undo();
        // ドロップ先の親要素にドロップする処理を追加
        let targetId = prevParent.id;
        let target = document.getElementById(targetId);
        
        if (prevSibling) {
            target.insertBefore(sourceElement, prevSibling);
        } else {
            target.appendChild(sourceElement);
        }
        updateAnswer();
        let answer_result = json_catch();
        answer_result = answer_result.split(" ");
        if (answer_result[0] === answer_result[1]) {
            // 正解のアラートを表示
            alert("正解！");
            
            // result.phpにリダイレクト
            window.location.href = "result.php";
        }
    }
}

// ドラッグアンドドロップ操作の許可
function allowDrop(event) {
    event.preventDefault();
}

// 要素をドラッグしたときの処理
function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
    let sourceId = event.target.id;
    let prevParentId = event.target.parentElement.id;
    let prevSiblingId = event.target.nextElementSibling ? event.target.nextElementSibling.id : null;
    operationStack.push({ state: "drop", sourceId: sourceId, prevParentId: prevParentId, prevSiblingId: prevSiblingId });
}

// ドロップされたときの処理
function drop(event, targetId) {
    event.preventDefault();
    let data = event.dataTransfer.getData("text");
    let target = document.getElementById(targetId);

    if (!target.querySelector('div')) {
        target.appendChild(document.getElementById(data));
    } else if (targetId === 'choices_area' && target.querySelectorAll('div').length < 25) {
        target.appendChild(document.getElementById(data));
    } else if (targetId.includes('cell')) {
        let cellDiv = target.querySelector('div');
        if (!cellDiv) {
            target.appendChild(document.getElementById(data));
            operationStack.push({ state: "drop", sourceId: data, prevParentId: targetId, prevSiblingId: null });
        } else {
            let prevSiblingId = cellDiv.nextElementSibling ? cellDiv.nextElementSibling.id : null;
            let choiceArea = document.getElementById('choices_area');
            choiceArea.appendChild(cellDiv); // 先にitem_areaにdiv要素を戻す
            operationStack.push({ state: "change", sourceId: cellDiv.id, prevParentId: targetId, prevSiblingId: prevSiblingId }); // 操作履歴を追加
            target.appendChild(document.getElementById(data)); // ドロップされたデータを追加
        }
    }
    updateAnswer();
    let answer_result = json_catch();
    answer_result = answer_result.split(" ");
    if (answer_result[0] === answer_result[1]) {
        // 正解のアラートを表示
        alert("正解！");
        
        // result.phpにリダイレクト
        window.location.href = "result.php";
    }
}

// ドラッグが離されたときの処理
function dragLeave(event) {
    event.preventDefault();
}

// ドラッグが終了したときの処理
function dragEnd(event) {
    event.preventDefault();
}


document.getElementById("hint-items-area").style.display ="none";

function toggleDisplay() {
    const hint_items_area = document.getElementById("hint-items-area");

    if(hint_items_area.style.display=="block"){
        // noneで非表示
        hint_items_area.style.display ="none";
    }else{
        // blockで表示
        hint_items_area.style.display ="block";
	}
}
