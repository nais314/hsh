<?php

    namespace bony;
    /**
    this class has been modified, to be able to:
    - filter DATA - make changes in input, see "&" mark
    - be able to handle similar, validator-like tasks, like Access-Controll,
      because of the "DRY" principle,
      this is why $validator_className is present.
    */
    class Validator {


        private static  $_errors = [];

        public static function getErrors(){
            return self::$_errors;
        }




        /**
        FIND AND RUN VALIDATOR FUNCTION------------------------------------------ */
        protected function _validate($key, &$value, $rule, $className='\bony\FormValidators'){
            $param = null; // reset method parameters, to know, there is not one
            $msg = null; // declare to remove Notice
            # process rule
            if(is_array($rule)){
                switch(count($rule)){
                    case 0: return false;
                    case 1: $rule = $rule[0]; // or return false???
                    break;
                    case 2:  
                            if(is_callable($rule[1])){ # ['unique' => fun(){}]
                                $result = $rule[1]($value);
                                if ($result !== true){
                                    array_push(self::$_errors, [$key, $result]);
                                    return $result;
                                } else return true;
                            }else{ # ['minsize', 4]
                                $msg=null; $param = $rule[1]; $rule = $rule[0];
                            }
                    break;
                    case 3:  $msg=$rule[2]; $param = $rule[1]; $rule  = $rule[0];
                    break;
                }

            }

            # if user, anonymous function:
            if (is_callable($rule)){
                $result = $rule($value, $param, $msg);
            }
            # if internal function:
            elseif(is_callable($className.'::v_'.$rule)){  # or: method_exists(get_called_class(), 'v_'.$rule)
                //$result = call_user_func($className.'::v_'.$rule, $value, $param, $msg);
                //$result = ($className.'::v_'.$rule)($value, $param, $msg);
                $result = call_user_func_array($className.'::v_'.$rule, [&$value, $param, $msg]);
                //return call_user_func($className.'::v_'.$rule, $value, $param, $msg);

            
            }

            # else is data:
            else{
                $value = $rule;
                $result = true;
            }


            if ($result !== true){ //die( var_export($rule,true));
                array_push(self::$_errors, [$key, $result]);
                return $result;
            } else return true;              
            
            return true;// end rule method call
        }//---------------------------------------------------------








        /**
        MAIN PUBLIC METHOD--------------------------------------------------------
        this function is a convinience functions:
        it should receive various user input, to ease typing,
        so formatting input for the v_alidator functions is neccesary,
        this is why it looks so complex,
        but it is really just a preprocessor for the _validate function:
        [ <rule: str|closure>, <param|[params]>, <err message: str> ]
        */
        public static function validate(&$data, &$ruleset=null, $scenario='common', $validator_className='bony\FormValidators'){
            
            # if no rules return true
            if($ruleset==null) return true;
            # reset validator:
            self::$_errors = [];
            $rules = [];

            
            # well, common section should exists, however... :
            if(isset($ruleset['common']))  $rules = $ruleset['common'];
            # scenario like save, insert, etc... :
            # if array -> merge
            # if string -> call_user_func (string)
            //if($scenario != 'common')
            if(isset($ruleset[$scenario]) && is_array($ruleset[$scenario])){
                $rules = array_merge_recursive($rules, $ruleset[$scenario]);
            }/**
            else {   WTF??? 
                //$rule = $ruleset[$scenario];
                //self::_validate($key, $value, $rule, $validator_className); // run validator function
            }*/

            //echo '<br><pre>' . print_r($rules) .'</pre>'; //@DEBUG
            if(!empty($rules)){
                $common_rules =[];
                if(array_key_exists('*', $rules)){ # if we have rules for all data fields
                    if(is_array($rules['*'])){
                        $common_rules = array_merge_recursive($common_rules, $rules['*']);
                    }else{
                        $common_rules[] = $rules['*'];
                    }
                }
                //\app::$logger->debug(__METHOD__.": ".var_export($data,true) );
                foreach($data as $key => &$value){ //var_dump($value);
            
                    $allrules = $common_rules; # reset rules

                    if(array_key_exists($key, $rules)){ # ex: we have 'name'
                        if(is_array($rules[$key])){ # ex: multiple rules to 'name'
                            $allrules = array_merge_recursive($allrules, $rules[$key]);
                        }else{ # simple string:    
                            //self::_validate($key, $value, $rule, $validator_className);
                            $allrules[] = $rules[$key];
                        }
                    }
                        
                    foreach($allrules as $rulekey => $rule){ # iterate them
                        if(is_string($rulekey)){ # convinience overhead for: 'minsize' => 4 vs ['minsize', 4]
                            if(!is_array($rule)) $rule = array($rule); #'minsize' => 4 is not array
                            array_unshift($rule, $rulekey);
                        }
                        self::_validate($key, $value, $rule, $validator_className); # run validator function
                    }

                }//end foreach data
                unset($allrules, $common_rules);
            }// end rules
            if(!empty(self::$_errors)) {
                \app::$logger->debug(var_export(self::$_errors,true));
                return self::$_errors;
            }
            return true;
        }// END validate()
    }