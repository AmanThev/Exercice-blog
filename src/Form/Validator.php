<?php

namespace App\Form;


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
    private $errors;
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
     * @param  mixed $field
     * @param  mixed $params
     */
    public function check(string $rule, $field, $params = NULL): self
    {    
        $checkMessage = $this->getMessage($rule);
        $message = '{field} ' . $checkMessage;
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
        foreach ($this->ruleData as $v){
            foreach ($v['fields'] as $field){
                $this->error($field, $v['message'], $v['params']);
            }
        }
    }
    
    private function getMessage(string $rule): string
    {
        $ruleMessage = static::$messages;
        $message = isset($ruleMessage[$rule]) ? $ruleMessage[$rule] : self::ERROR_DEFAULT;
        return $message;
    }

    private function error(string $field, string $message, ?array $params = array()){
        $message = $this->setMessage($field, $message, $params);
        $this->errors[$field][] = vsprintf($message, $params);
    }

    private function setMessage($field, $message, $params){
        $message = str_replace('{field}', ucwords($field), $message) ?: str_replace('{field} ', '', $message);

        return $message;
    }







}