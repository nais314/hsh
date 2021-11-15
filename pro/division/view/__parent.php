    <div>
        <label><?= divisionModel::$ruleset['label']['parent'] ? divisionModel::$ruleset['label']['parent'] : 'Parent' ?></label>
        <input name="division[parent]" list="parentlist" value="<?= $data['parent'] ?>" />
        <datalist id="parentlist">
            <option value="parent">
        </datalist>
    </div>