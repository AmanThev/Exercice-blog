<?php
namespace App\HTML;

class Form
{
    private $error;
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function inputText($key, string $label, $info = NULL): string
    {
        $value = $this->getValue($key);
        $infoText = array_slice(func_get_args(), 2);
        return <<<HTML
            <label for="{$key}">{$label} :</label>
            <input type="text" name="{$key}" id="{$key}" value="{$value}" aria-describedby="{$key}Info">
            {$this->smallText($key, $infoText)}
HTML;
    }

    public function textArea($key, string $label, int $rows, $info = NULL): string
    {
        $value = $this->getValue($key);
        $infoText = array_slice(func_get_args(), 3);
        return <<<HTML
            <label for="{$key}">{$label} :</label>
            {$this->smallText($key, $infoText)}
            <textarea type="text" name="{$key}" id="{$key}" rows="{$rows}">{$value}</textarea>
HTML;
    }

    public function ratingRadioStar(string $key, $values)
    {
        $radioElement = '';
        for($v = $values; $v >= 1; $v--){
            $radioElement .= <<<HTML
            <input type="radio" name="{$key}" id="{$key}-{$v}" value="{$v}">
            <label for="{$key}-{$v}" class="fas fa-star"></label>
HTML;
        }
        return <<<HTML
        <h3>Your rating :</h3>
        <div>
            <input type="radio" name="{$key}" id="{$key}-0" value="0" checked>
            <label for="{$key}-0" class="fas fa-trash"></label>
        </div>
        <div class="{$key}">
            {$radioElement}
        </div>
HTML;
    }

    public function searchBox(string $key, string $value){
        return <<<HTML
        <input class="search-text" type="search" name="{$key}" placeholder="search">
        <button type="submit" name="submit" class="search-btn" value="{$value}"><i class="fas fa-search"></i></button>
HTML;
    }

    public function button($textInButton): string
    {
        return <<<HTML
        <button type="submit" name="submit">{$textInButton}</button>
HTML;
    }

    private function getValue(string $key)
    {
        if(is_array($this->data)){
            // Data typed by the visitor ($_POST) : always escaped before it is printed.
            // (the values of a model object, below, are already encoded by the model)
            $value = $this->data[$key] ?? null;
            return is_string($value) ? htmlspecialchars($value) : null;
        }
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        return $this->data->$method();
    }

    private function info(string $key, array $info)
    {
        switch ($info[0]) {
            case 'size':
                return 'Your '.$key.' must not exceed '.$info[1].' characters';
                break;
            case 'spoilers':
                return 'If you want to write spoilers, please surround them with tags [Spoiler] and [/Spoiler]';
            case 'picture':
                return 'Choose a '.$key.' to illustrate';
            default:
                return 'This fields is required';
        }
    }

    private function smallText(string $key, array $info)
    {
        if($info){
            $smallText = $this->info($key, $info);
            return '<small class="form-info" id="'.$key.'Info">'.$smallText.'</small><br>';
        }
        return '';
    }
}