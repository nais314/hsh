<div id="<?= $data['id'] ?>"
    data-id="<?= $data['id'] ?>"
    class='treeitem <?= $data['css_class'] ?>'
    style='<?= $data['css_style'] ?>'
    role='treeitem'
    
    data-model=''

    data-has_child="<?= $data['has_child'] ?>"
        data-level="<?= $data['level'] ?>"
    tabindex=0

    title="<?= ($data['title']) ?? 'Click to view, DoubleClick to modify' ?>"

    onclick="window.location.href='?r=<?= \app::$tableName."/view&id={$data['id']}" ?>'"
    ondblclick="window.location.href='?r=<?= \app::$tableName."/update&id={$data['id']}" ?>'"

    draggable="true"
    data-dragdata='<?= $data['id'] ." ". $tableName ?>'
    ondragover="dragover(event)"
    ondragleave="dragleave(event)"
    ondragstart="dragstart(event)"
    ondrop="treeitem_drop(event)"
>

    <?= /* $data['level'].  */str_repeat('&nbsp&nbsp', $data['level']). $data['name'] ?>

</div>