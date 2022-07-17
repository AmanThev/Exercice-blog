<?php
namespace App\HTML;

class Form
{
    private $error;
    private $theme;

    public function __construct(string $theme)
    {
        $this->theme = $theme;
    }

    public function inputText(string $key, $info = NULL): string
    {
        $value = $_POST[$key] ?? null;
        $infoText = array_slice(func_get_args(), 1);
        return <<<HTML
            <label for="{$key}">Your {$key} :</label>
            <input type="text" name="{$key}" id="{$key}" value="{$value}" aria-describebdy="{$key}Info" placeholder="Write your {$key}">
            {$this->smallText($key, $infoText)}
HTML;
    }

    public function textArea(string $key, int $rows, $info = NULL): string
    {
        $infoText = array_slice(func_get_args(), 2);
        return <<<HTML
            <label for="{$key}">Your {$key} :</label>
            {$this->smallText($key, $infoText)}
            <textarea type="text" name="{$key}" id="{$key}" rows="{$rows}"></textarea>
HTML;
    }

    public function ratingRadioStar(string $key, $values)
    {
        $radioElement = '';
        for($v = $values; $v >= 1; $v--){
            $radioElement .= <<<HTML
            <input type="radio" name="$key" id="$key-$v" value="$v">
            <label for="$key-$v" class="fas fa-star"></label>
HTML;
}
        return <<<HTML
        <h3>Your rating :</h3>
        <div>
            <input type="radio" name="$key" id="$key-0" value="0" checked>
                <label for="$key-0" class="fas fa-trash"></label>
        </div>
        <div class="$key">
            {$radioElement}
        </div>
HTML;
    }

    public function button($textInButton): string
    {
        return <<<HTML
        <button type="submit" name="submit">{$textInButton}</button>
HTML;
    }

    private function info(string $key, array $info)
    {
        switch ($info[0]) {
            case 'size':
                return 'Your '.$key.' must not exceed '.$info[1].' characters';
                break;
            case 'spoilers':
                return 'If you want to write spoilers, please surround them with tags [Spoiler] and [/Spoiler]';
            default:
                return 'This fields is required';
        }
    }

    private function smallText(string $key, array $info)
    {
        if($info){
            $smallText = $this->info($key, $info);
            return '<small class="form-info">'.$smallText.'</small><br>';
        }
        return '';
    }
}
