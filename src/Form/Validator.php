<?php

namespace App\Form;

use App\Manager\Database;

class Validator
{

    const ERROR_DEFAULT = "Invalid";
    
    /**
     * @var mixed
     */
    private $data;    
    /**
     * @var array
     */
    private $ruleData = array();    
    /**
     * @var array
     */
    private $errors = array();
    /**
     * @var array
     */
    private static $messages = array();

    
    public function __construct(array $data)
    {
        $this->data = $data;
        $messageFile = stream_resolve_include_path("message.php");
        if($messageFile){
            $messages = include $messageFile;
            static::$messages = $messages;
        }else{
            throw new \Exception("Invalid file");
        }
    }
    
    /**
     * @param  string|array $field
     * @param  string|array $params
     */
    public function check(string $rule, $field, $params = NULL): self
    {    
        $message = $this->getMessage($rule);
        $params = array_slice(func_get_args(), 2);
        
        $this->ruleData[] = array(
            'rule' => $rule,
            'fields' => (array)$field,
            'params' => (array)$params,
            'message' => $message
        );

        return $this;
    }
    
    public function validateForm()
    {
        foreach($this->ruleData as $v){
            foreach($v['fields'] as $field){
                $callback = array($this, $v['rule']);
                $values = $this->getValue($this->data, $field);
                foreach($values as $value){
                    $result = call_user_func($callback, $field, $value, $v['params']);
                    if($result === false){
                        $this->error($field, $v['message'], $v['params']);
                    }
                }
            }
        }
        return count($this->getErrors()) === 0;
    }
    
    private function getMessage(string $rule): string
    {
        $ruleMessage = static::$messages;
        $checkMessage = isset($ruleMessage[$rule]) ? $ruleMessage[$rule] : self::ERROR_DEFAULT;
        if(preg_match('/[A-Z]/', $checkMessage)){
            return $checkMessage;
        }
        return '{field} ' . $checkMessage;
    }

    private function getValue(array $data, $field)
    {
        if(!empty($_FILES[$field])){
            $data[$field] = $_FILES[$field]['name'];
            return array($data[$field]);
        }
        return array($data[$field]);
    }

    private function error(string $field, string $message, ?array $params = array())
    {
        $message = $this->setMessage($field, $message);
        $this->errors[$field][] = vsprintf($message, $params);
    }
    
    private function setMessage(string $field, string $message)
    {
        $message = str_replace('{field}', ucwords($field), $message) ?: str_replace('{field} ', '', $message);
        return $message;
    }

    public function getErrors() :array
    {
        return $this->errors;
    }
    
    private function required(string $field, string $value) :bool
    {
        if (!isset($value) || is_null($value) || empty($value)) {
            return false;
        }
        return true;
    }

    private function email(string $field, string $value)
    {
        return filter_var($value, \FILTER_VALIDATE_EMAIL);
    }

    private function year(string $field, $value) :bool
    {
        if(preg_match("#^[1-2][0-9]{3}$#", $value) && $this->numeric($field, $value)){
            return true;
        }
        return false;
    }
    
    private function lengthBetween(string $field, string $value, array $params) :bool
    {
        $length = $this->stringLength($value);
        return ($length !== false) && $length >= $params[0] && $length <= $params[1];
    }

    private function lengthMax(string $field, string $value, array $params) :bool
    {
        $length = $this->stringLength($value);
        return ($length !== false) && $length <= $params[0];
    }

    private function lengthMin(string $field, string $value, array $params) :bool
    {
        $length = $this->stringLength($value);
        return ($length !== false) && $length >= $params[0];
    }

    private function stringLength(string $data)
    {
        if(is_string($data)){
            return mb_strlen($data);
        }
        return false;
    }
    
    /**
     * $params --> only a second field
     */
    private function equals(string $field, $value, array $params) :bool
    {
        $value2 = $this->getValue($this->data, $params[0]);
        $value2 = implode('', $value2);
        return $value === $value2;
    }

    private function different(string $field, $value, array $params) :bool
    {
        $value2 = $this->getValue($this->data, $params[0]);
        $value2 = implode('', $value2);
        return $value != $value2;
    }

    private function used(string $field, string $value, array $params) :bool
    {   
        $valueExist = new Database();
        return $valueExist->exist($field, $value, $params[0]);
    }
    
    /**
     * if field tabName != field form -> $params[1] === field tabName
     *
     * @param  string $field
     * @param  string $value
     * @param  array $params
     * @return bool
     */
    private function exist(string $field, string $value, array $params) :bool
    {   
        $valueExist = new Database();
        if(count($params) > 1){
            return !$valueExist->exist($params[1], $value, $params[0]);
        }
        return !$valueExist->exist($field, $value, $params[0], $params[1]);
    }

    private function numeric($field, $value) :bool
    {
        return is_numeric($value);
    }

    private function numberBetween(string $field, $value, $params) :bool
    {
        if(!is_numeric($value)){
            return false;
        }
        return $params[0] <= $value && $params[1] >= $value;
    }

    private function numberMin(string $field, $value, $params) :bool
    {
        if(!is_numeric($value)){
            return false;
        }
        return $params[0] < $value;
    }

    private function numberMax(string $field, $value, $params) :bool
    {
        if(!is_numeric($value)){
            return false;
        }
        return $params[0] > $value;
    }

    private function extensionPicture(string $field, $value, $params) :bool
    {
        if($params[0]['error'] > 0){
            throw new \Exception("Error: " . $params[0]['error']);
        }

        $file       = $params[0]['name'];
        $extensions = ['.png', '.jpg', '.jpeg', '.gif'];
        $extension  = strtolower(strrchr($file, '.'));

        if(!in_array($extension, $extensions)){
            return false;
        }
        return true;
    }
}