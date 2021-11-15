<div id="<?= $data['id'] ?>" data-id="<?= $data['id'] ?>"
class='treeitem <?= $data['css_class'] ?>' 
role='treeitem' 

data-model=''
data-droptarget=''
data-dragdata='<?= $data['id'] ." ". $tableName ?>'

data-loader=''
data-has_child="<?= $data['has_child'] ?>"
data-level="<?= $data['level'] ?>" 
tabindex=0 
>

    <?= str_repeat('&nbsp', $data['level']).$data['name'] ?>

</div>