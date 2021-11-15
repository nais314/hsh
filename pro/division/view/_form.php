<form action="" method="POST" enctype="application/x-www-form-urlencoded" >
    <div>
        <label><?= divisionModel::$ruleset['label']['name'] ? divisionModel::$ruleset['label']['name'] : 'Name' ?></label>
        <input type="text" name="division[name]" value="<?= $data['name'] ?>" />
    </div>


    <div>
        <label><?= divisionModel::$ruleset['label']['parent'] ? divisionModel::$ruleset['label']['parent'] : 'Parent' ?></label>
        <input name="division[parent]" list="parentlist" value="<?= $data['parent'] ?>" />
        <datalist id="parentlist">
            <option value="parent">
        </datalist>
    </div>

    <?php echo \app::render('__parent'); ?>


    <div>
        <label><?= divisionModel::$ruleset['label']['hostname'] ? divisionModel::$ruleset['label']['hostname'] : 'hostname' ?></label>
        <input type="text" name="division[hostname]" value="<?= $data['hostname'] ?>" />
    </div>

    <div></div>

    <div class="actiondiv"><input type="submit" value="Save" /></div>

</form>