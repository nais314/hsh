<div id="<?= $data['id'] ?>"
    data-id="<?= $data['id'] ?>"
    data-name="<?= $data['name'] ?>"
    
    class='treeitem <?= $data['css_class'] ?>'
    style='<?= $data['css_style'] ?>'
    role='treeitem'
    
    data-model=''

    data-has_child="<?= $data['has_child'] ?>"
        data-level="<?= $data['level'] ?>"
    tabindex=0

    title="<?= ($data['title']) ?? '' ?>"



    draggable="true"
    data-dragdata='<?= $data['id'] ." ". $tableName ?>'
    ondragover="dragover(event)"
    ondragleave="dragleave(event)"
    ondragstart="dragstart(event)"
    ondrop="treeitem_drop(event)"
>

    <?= /* $data['level'].  */str_repeat('&nbsp&nbsp', $data['level']). $data['name'] ?>

</div>