<?php
class Person
{
    protected $firstname;
    protected $lastname;
    protected $pdo;

    public function __construct($pdo = null, $firstname = '', $lastname = '')
    {
        $this->pdo = $pdo;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function completeName() {
        return $this->lastname .", ". $this->firstname;
    }

    protected function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    protected function validateDate($date) {
        return (bool)strtotime($date);
    }

    protected function validateRequired($data, $fields) {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        return $errors;
    }
}
?>
