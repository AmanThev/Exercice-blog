<?php
namespace App\Form;

/**
 * What the forms of the categories and of the sub-categories have in common.
 *
 * The name of a category or of a sub-category is part of the address of its page : the address is made
 * by joining the words ("History of the cinema" becomes "HistoryOfTheCinema"), and the page finds the name
 * again by cutting the address before every capital letter. A name only works if it is made of words with
 * letters only, with a capital letter only at the start of a word, and one space between the words.
 */
abstract class ForumForm extends FormHandler
{
    protected const NAME_PATTERN = '/^[A-Za-z][a-z]*( [A-Za-z][a-z]*)*$/';

    protected const NAME_RULE = 'A name is made of words with letters only (no digit, accent, hyphen or symbol, and a capital letter only at the start of a word), with one space between the words, for example "Visual Effects". It is part of the address of the page.';

    /**
     * Makes sure the fields exist and are strings (a hand-made POST with name[]=x would crash the Validator).
     */
    protected function ensureStrings(array $keys): void
    {
        foreach($keys as $key){
            $this->data[$key] = (isset($this->data[$key]) && is_string($this->data[$key])) ? trim($this->data[$key]) : '';
        }
    }

    /**
     * Is it the same name as the one in the database ? (it can stay the same, even if it is an old name
     * that would not be accepted now)
     */
    protected function isUnchanged(?string $currentName): bool
    {
        return $currentName !== null && mb_strtolower($this->data['name']) === mb_strtolower($currentName);
    }

    /**
     * The rules on "name" of the Validator. A name must not be used by another row of the table.
     */
    protected function checkName(Validator $data, string $table, ?string $currentName): void
    {
        $data->check('required', ['name']);
        $data->check('lengthMax', 'name', 100);
        if(!$this->isUnchanged($currentName)){
            $data->check('used', 'name', $table);
        }
    }

    /**
     * The shape of the name (there is no such rule in the Validator) : adds its error to the others.
     */
    protected function checkNameShape(?string $currentName): void
    {
        if($this->data['name'] !== '' && !$this->isUnchanged($currentName) && !preg_match(self::NAME_PATTERN, $this->data['name'])){
            $this->errors['name'][] = self::NAME_RULE;
        }
    }
}