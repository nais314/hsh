<?php
namespace bony;

class Former {

    /**
     * STATIC TREEE
    generate html from array ---
    Tree:: loads $tree  =>  $tree= \bony\Tree::ti_load_childs
        *    #distill result set <<F&R
        *    $a[$row->id] = [
        *    'name' => $row->name,
        *    'css_class' => $row->css_class,
        *        'css_style' => $row->css_style,
        *        'level' => $level,
        *        'has_child' => $row->has_child,
        *    ];
    */
    public function gen_tree_nodes(&$tree, $selected = ''){
        //\app::$logger->log('info',__FUNCTION__ . var_export($tree,true));
        $tm = '';
        if($tree) // null on NEW, empty table
        //foreach($tree as $ti_id => $ti ){
        foreach($tree as $ti ){
            $css_class='';
            $ti_autofocus = '';

            if($selected == $ti['id']) {
                $css_class="iselected";
                $ti_autofocus = 'autofocus';
            }elseif($ti['level'] > 0) {
                $css_class =  ($ti['level'] > 1)? ( ($ti['level'] % 2 == 0)? 'ieven':'iodd') : "" ;
            };

            //..................................................


            $ti_name = str_repeat('&nbsp&nbsp', $ti['level']).$ti['name'];
                //! DEPRECATED $ti_is_open = ($ti['has_child'])? (($ti['childs_loaded'])? 1 : 0)  : -1;
                //! DEPRECATED $ti_childs_loaded = ($ti['childs_loaded'])? 'true' : 'false';
            $tm .= <<<EOT
            <div id='{$ti['id']}' data-id='{$ti['id']}'
                class='treeitem $css_class {$ti['css_class']}'
                style='' draggable='true'
                data-value='{$ti['name']}'
                data-has_child='{$ti['has_child']}'
                tabindex=0 role='treeitem'
                data-level='{$ti['level']}'
                {$ti_autofocus}
                >
                {$ti_name}
EOT;
            $tm .= "</div>";


            //$tm .= "<div id='treeitem_childs-$ti_id' class='treeitem_childs' style=''>";
                //if($ti['childs_loaded']) $tm .= self::gen_tree_nodes($ti['childs']);
                if(!empty($ti['childs'])) $tm .= self::gen_tree_nodes($ti['childs'], $selected);
            //$tm .= "</div>";

        }

        return $tm;
    }




    /**
    --------------------------------------------------------------------------
    --------------------------------------------------------------------------
    --------------------------------------------------------------------------
    --------------------------------------------------------------------------
    --------------------------------------------------------------------------

    $data = $row->getData(); */
    /**
     TODO: make_form(&$data, tableName) as tablename may resolved auto!
     */
    public static function make_form(&$data = [], $enableSave = true){
        # these variables are used in strings: "{$var}"
        $modelClass = \app::$modelClass;
        $tableName = \app::$tableName;
        //\app::$logger->debug(__METHOD__.": ".$tableName);
        $module = \app::$module;

        $form = null;
        //\app::$logger->log('info', var_export($data, true));
        //\app::$logger->log('info', $data['id']);

        if(!isset((\app::$modelClass)::$ruleset) || !isset((\app::$modelClass)::$ruleset['properties'])){
            return false;
        }


        foreach ((\app::$modelClass)::$ruleset['properties'] as $prop_name => $prop_val) {
            //\app::$logger->debug(__METHOD__.$prop_name ." ". $prop_val ." ". $prop_val['visibility']);

            if(!isset($prop_val['visibility'])) continue;
            if($prop_val['visibility'] == 'public'
            || $prop_val['visibility'] == 'readonly'
            || $prop_val['visibility'] == 'hidden'
            || $prop_val['visibility'] == 'custom' # display holds custom function os string
            || $prop_val['visibility'] == 'masked' # TODO secure data on client with a dict in session
            //|| $prop_val['visibility'] == 'private'
            ){}else continue;

            if($prop_val['visibility'] == 'custom'){
                if(is_callable($prop_val['display'])){
                    echo $prop_val['display']();
                }else{
                    echo $prop_val['display'];
                }
            }

            //.......................................
            $item = '';

            # assemble attributes array ( wich implodes to string):
            $attributes = [];
            if(isset($prop_val['attributes'])) $attributes = (is_array($prop_val['attributes']))?$prop_val['attributes'] : array($prop_val['attributes']);

            if($prop_val['visibility']=='readonly' || !$enableSave) $attributes[] = 'readonly';
            //if($prop_val['visibility']=='hidden' || !$enableSave) $attributes[] = 'hidden'; //not working

            if(isset($prop_val['autofocus'])) $attributes[] = 'autofocus';

            $req = null;
            if(isset($prop_val['required'])) {
                $req = "class='req'"; $attributes[] = 'required'; }

            $style = null;
            if(isset($prop_val['style'])) {
                $style .= $prop_val['style'];  }
            if(isset($prop_val['flex-basis'])) {
                $style .= 'flex-basis: '.$prop_val['flex-basis'].';';
            }
            if(isset($prop_val['visibility']) && $prop_val['visibility'] == 'hidden'){
                $style .= 'display: none;';
            }
            $style = "style='{$style}'";

            if(isset((\app::$modelClass)::$ruleset['common'][$prop_name]) && is_array((\app::$modelClass)::$ruleset['common'][$prop_name]))
            if(isset((\app::$modelClass)::$ruleset['common'][$prop_name]['maxlength']))
                $attributes[] = "maxlength='"
                .\app::$modelClass::$ruleset['common'][$prop_name]['maxlength']
                ."'";

            if(isset($prop_val['datalist'])){
                $datalist = null;
                $attributes[] = "list='{$prop_name}list'";

                if(is_array($prop_val['datalist'])){
                    $item .= "<datalist id='{$prop_name}list'>";
                    foreach($prop_val['datalist'] as $option){
                        $item .= "<option value='{$option}'/>";
                    }
                    $item .= "</datalist>";
                }elseif (is_callable($prop_val['datalist'])) {
                    $datalist = $prop_val['datalist']();
                    $item .= "<datalist id='{$prop_name}list'>";
                    foreach($datalist as $row){
                        $item .= "<option value='{$row[value]}'>";
                    }
                    $item .= "</datalist>";
                }
            }//..................................



            if(isset($prop_val['optionlist'])){
                $optionlist = null;
                //$attributes[] = " list='{$prop_name}list' ";

                if(is_array($prop_val['optionlist'])){
                    # 'optionlist' => [0 => 'some', 1 => 'other']
                    foreach($prop_val['optionlist'] as $opkey => $opvalue){
                        $optionlist .= "<option value='{$opkey}'> {$opvalue}</option>";
                    }
                }elseif (is_callable($prop_val['optionlist'])) {
                    # [0 => ['key' => 1, 'value' => 'some']]
                    $arr_options = $prop_val['optionlist']();

                    if(isset($prop_val['optionfilter'])){
                        $prop_val['optionfilter']($arr_options);
                    }

                    foreach($arr_options as $dat){
                        ($data[$prop_name] == $dat['key']) ? $selected = ' selected ' : $selected = '';
                        $optionlist .= "<option value='{$dat['key']}' $selected style=''> {$dat['value']}</option>";
                    }

                    unset($arr_options);
                }
            }//..................................







            # assemble item: ..................................

            $label = $prop_val['label'] ? $prop_val['label'] : ucfirst($prop_name);

            $attributes = implode(' ', $attributes);

            $display = (isset($prop_val['display']))? $prop_val['display'] : 'text';
            switch($display){ // 'display''display''display''display''display''display'

                case 'text':
                    $item .= <<<EOT
<div $req $style>
    <label>$label</label>
    <input type="text" name="{$tableName}[{$prop_name}]" value="{$data[$prop_name]}" $attributes />
</div>
EOT;
                break; //............................


                /**
                 * TODO CSS <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
                 */
                case 'textarea':
                    $item .= <<<EOT
<div $req $style>
    <label>$label</label>
    <textarea name="{$tableName}[{$prop_name}]"  $attributes >{$data[$prop_name]}</textarea>
</div>
EOT;
                break; //............................


                case 'ckeditor':
                    $item .= <<<EOT
<div $req $style>

    <textarea id="{$tableName}_{$prop_name}" name="{$tableName}[{$prop_name}]"  $attributes >{$data[$prop_name]}</textarea>
</div>
<script>
CKEDITOR.replace( "{$tableName}_{$prop_name}", { extraAllowedContent: 'b i mark small img svg xml', extraPlugins: 'autogrow,videodetector' } );
</script>
EOT;
                //if(!isset(\app::$arr_JS['ckeditor'])) \app::$arr_JS['ckeditor'] = "ckeditor/ckeditor.js";
                \app::$arr_JS['ckeditor'] = "ckeditor/ckeditor.js";
                break; //............................





                case 'select':
                $item .= <<<EOT
                <div $req $style>
                    <label>$label</label>
                    <select name="{$tableName}[{$prop_name}]"
                    value="{$data[$prop_name]}" $attributes >
                    <option value=""></option>
                    $optionlist
                    </select>
EOT;
                $item .= "</div>";


                break; //............................





                case "checkbox":
                $item .= <<<EOT
                <div $req $style>
                <label>$label</label>
                <input type="checkbox" name="{$tableName}[{$prop_name}]"
                value="{$data[$prop_name]}" $attributes >
                </div>
EOT;
                break; //............................



                case "checkbox-large": //!experimental
                $item .= <<<EOT
                <div $req $style>
                <label>$label</label>
                <input type="checkbox" name="{$tableName}[{$prop_name}]"
                value="{$data[$prop_name]}" $attributes
                style="transform:scale(2,2);"
                >
                </div>
EOT;
                break; //............................







                /**
                     /\     ***       ***
                    /  \   *****     *****
                   /    \ *******   *******
                 TREE: add input for view, input for id store and submit,
                 shadow overlay <div id="overlay_{$tableName}{$prop_name}" */
                case 'tree':

                # tree can relate to other table than this:
                # check if we are referencing the same sql-table
                //\app::$logger->log('info', $prop_val['tableName'] .' - '.$tableName);
                if(isset($prop_val['tableName'])){
                    if($prop_val['tableName'] == $tableName){
                        $ti_selfid = $data['id'];
                    }else {// 0 is not a valid sql ID, but a valid number ;D
                        $ti_selfid = 0;
                    }
                }
                $treeTableName = (isset($prop_val['tableName']))? $prop_val['tableName'] : $tableName;


                # id for JS fun:
                $treeid = \app::genid(5);


                if(isset($data[$prop_name])){ // new has no data
                    $selecteds_name = \bony\resql::fetch(['id'=>$data[$prop_name]],null,$treeTableName,'*')['name'];
                    //\app::$logger->debug(":::".$data[$prop_name].":::".$treeTableName."...".$selecteds_name);
                }else{
                    $selecteds_name = null;
                }

                # the form item - input text
                # visible input: for view, name, readable
                # hidden input: for saveing, db, value
                $item .= <<<EOT
                <div $req $style id='form_{$treeid}'>
                    <label>$label</label>
                    <input type="text"
                        id="{$tableName}{$prop_name}"
                        name="{$tableName}{$prop_name}"
                        value="{$selecteds_name}"
                        onClick="toggleActionMenu(document.getElementById('overlay_{$treeid}'));
                        document.querySelector('.iselected').focus();"
                        onKeydown="toggleActionMenu(document.getElementById('overlay_{$treeid}')); document.querySelector('.iselected').focus();"
                        onChange="$treeid()"
                        $attributes />
                    <input type="hidden"
                        name="{$tableName}[{$prop_name}]"
                        value="{$data[$prop_name]}"
                        id="selected_{$treeid}"
                        $attributes />
                    <script>
                        function $treeid() {
                            {$prop_val['onchange']}
                        }
                    </script>
                </div>
EOT;


                # the container overlay with the tree:
                $module=\app::$module;
                $tree = "<div id='' data-id='' data-value='' class='treeitem'>NONE</div>";
                $tree .= self::gen_tree_nodes(
                    TreeController::ti_load_childs(
                        $treeTableName,
                        $parent=null,
                        $level = 0,
                        $recursive = true
                        ),
                    $selected=$data[$prop_name]
                    );
                //\app::$logger->debug(__METHOD__.' '.$selected);
                $item .= <<<EOT
                <div id="overlay_{$treeid}" style="display:none; width:auto;">
EOT;
                    $item .= "<div id='{$treeid}'
                    class='tree' role='tree'
                    data-id='{$treeid}'
                    data-ti_selfid='{$ti_selfid}'
                    data-tablename='{$tableName}'
                    data-module='{$module}'
                    data-action='ti_activate'
                    data-template=''
                    >"
                        .$tree
                        .'</div>';

                $item .= "</div>";




                \bony\TreeController::load_js();

                $item .= <<<EOT
                <script style='display:none'>
                    treegrid_init("#{$treeid}");
                </script>

EOT;



                break; //............................
            }//-------end switch----------------

            $form .= $item;
        }// end foreach

    $form_header = <<<EOT
        <FORM action='' method='POST' enctype='application/x-www-form-urlencoded' >
            <input type='hidden' name='id' value='{$data['id']}' />
            <input type='hidden' name='module' value='{$module}' />
            <input type='hidden' name='token' value='{$_SESSION['token']}' />
EOT;

    if($enableSave){
        $form_footer = '
            <div class="actiondiv">';

        if(isset($_SESSION['undo'][\app::$module][$data['id']])){
            //TODO relocate save-undo to ::save()
            # send $data to server with token && reload page
        }

        $form_footer .= '
                <input type="button" value="ReLoad" onclick="window.location.href=window.location.href"/>
                <input type="submit" value="Save" />
                ';


        $form_footer .= '
            </div>
        </form>';

    }else{
        $form_footer = "</form>";
    }

    return $form_header.$form.$form_footer;
    }// end make from function ------------------------------------------




}