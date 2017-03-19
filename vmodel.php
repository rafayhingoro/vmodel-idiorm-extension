<?php
/**
 * VModel - Idiorm/Paris Extension
 *
 * Validations, Before Save After Save
 *
 * Copyright 2017 - Abdul Rafay
 * Permission is hereby granted, free of charge, to any
 * person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 *
 * @author     Abdul Rafay Hingoro
 * @copyright  2017 - Abdul Rafay Hingoro
 * @license    MIT
 * @link       https://github.com/rafayhingoro/vmodal-idiorm-extension
 * @since      File available since Release 1.0.0
 */

class VModel extends Model {

    protected $_fields = array();
    protected $_scenario;
    protected $_isValid = true;

    /**
     * Save the data associated with this model instance to the database.
     *
     * @return null
     */
    public function save()
    {
        // Updating query scenario
        $this->checkScenario();
        $orm = null;
        $this->beforeSave();
        if($this->_isValid){
            try {
                $orm = $this->orm->save();
            } catch (PDOException $ex){
                $orm = false;
                throw new Exception('Error: ' . $ex->getMessage());
            }
        } else {
            return false;
        }
        $this->afterSave();
        return $orm;
    }

    /**
     * @param string valid // validation name
     * @param string val   // form value
     * @param string field // field name
     *
     * @throws Exception if the value has not passed validation
     * @return null
     */
    protected function amIValid($valid, $val, $field, $form = array())
    {
        $field = ucwords(str_ireplace('_', ' ', $field));
        if(strstr($valid, '>', true) == 'limit'){
            $limit = str_replace('>', '', strstr($valid, '>'));
            $this->_isValid = (strlen($val) >= $limit);
            $sign = 'greater';
            $valid = 'limit';
        } else if(strstr($valid, '<', true) == 'limit'){
            $sign = 'less';
            $limit = str_replace('<', '', strstr($valid, '<'));
            $this->_isValid = (strlen($val) <= $limit);
            $valid = 'limit';
        }
        switch($valid){
            case 'required':
                if(empty(trim($val))){
                    $this->_isValid = false;
                    throw new Exception($field .' is required');
                }
            break;
            case 'email':
                if(!filter_var($val, FILTER_VALIDATE_EMAIL)){
                    $this->_isValid = false;
                    throw new Exception('Please enter valid email');
                }
            break;
            case 'match':
                if(!empty($form) && strtolower($field) == 'password'){
                    if(!isset($form['confirm_password']) || $val != $form['confirm_password']){
                        throw new Exception("Passwords do not match");
                    }
                }
            break;
            case 'integer':
            case 'int':
                if(!empty($form) && !is_int((int) $val)){
                    $this->_isValid = false;
                    throw new Exception($field . ' must be integer');
                }
            break;
            case 'limit':
                if(!$this->_isValid){
                    throw new Exception($field . ' must be equal to or '.$sign.' than '.$limit);
                }
            break;
            default;
        }
    }

    public function delete()
    {
        $this->_scenario = 'delete';
        $this->orm->delete();
    }

    public function delete_many()
    {
        $this->_scenario = 'delete';
        $this->orm->delete_many();
    }

    public function updateScenario($scenario)
    {
        $this->_scenario = $scenario;
    }

    public function checkScenario(){
        if(!$this->is_new()){
            return $this->_scenario = 'update';
        } else {
            return $this->_scenario = 'insert';
        }
    }

    /**
     * use this method if you want to add log
     * when creating or updating any row
     *
     * @param int created_by
     *
     * @return null
     */
    public function addCULog($id = false)
    {
        if($this->_scenario == 'insert'){
            if(in_array('created_on', $this->_fields)){
                $this->created_on = date('Y-m-d H:i:s');
            }
            if(in_array('created_by', $this->_fields) && $id != false){
                $this->created_by = $id;
            }
        } elseif ($this->_scenario == 'update'){
            if(in_array('updated_on', $this->_fields)){
                $this->updated_on = date('Y-m-d H:i:s');
            }
            if(in_array('updated_by', $this->_fields) && $id != false){
                $this->updated_by = $id;
            }
        }
    }

    /**
     * @param array form field and defined field name must match
     *
     * @return null
     */
    public function validateForm($form = array())
    {
        if(!is_array($form) || empty($form)){
            throw new Exception('Form is not valid');
        }

        foreach($this->_fields as $field){
            if(isset($form[$field]) && isset($this->_rules[$field])){
                $validations = explode('|', trim($this->_rules[$field]));
                foreach($validations as $validate){
                    $this->amIValid($validate, $form[$field], $field, $form);
                }
            }
        }
    }

    /**
     * Validate and Save
     *
     * @param array form
     *
     * @return modal
     */
    public function saveForm($form)
    {
        $this->validateForm($form);
        foreach($this->_fields as $field){
            if(isset($form[$field])){
                $this->$field = $form[$field];
            }
        }
        return $this->save();
    }

    /**
     * this function can be used for any conditions
     * you would like to use before saving
     */
    public function beforeSave()
    {
        //return true;
    }

    /**
     * this function can be used for any conditions
     * you would like to use after saving
     */
    public function afterSave()
    {
        //return true;
    }
}
