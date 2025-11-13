<?php
namespace Src\Validation;

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];

    private function __construct($data, $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make($data, $rules) {
        return new self($data, $rules);
    }

    public function fails() {
        $this->errors = [];

        foreach ($this->rules as $f => $r) {
            $val = $this->data[$f] ?? null;

            foreach (explode('|', $r) as $rule) {
                if ($rule === 'required' && ($val == null || $val === '')) {
                    $this->errors[$f][] = 'required';
                } 
                elseif (str_starts_with($rule, 'min:') && strlen((string)$val) < (int)substr($rule, 4)) {
                    $this->errors[$f][] = $rule;
                } 
                elseif (str_starts_with($rule, 'max:') && strlen((string)$val) > (int)substr($rule, 4)) {
                    $this->errors[$f][] = $rule;
                } 
                elseif ($rule === 'email' && $val != null && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$f][] = 'email';
                }

                // bagian numeric
                elseif ($rule === 'numeric' && $val != null && !is_numeric($val)) {
                    $this->errors[$f][] = 'numeric';
                }
                
                //bagian integer
                elseif ($rule === 'integer' && $val != null && filter_var($val, FILTER_VALIDATE_INT) === false) {
                    $this->errors[$f][] = 'integer';
                }

                elseif (str_starts_with($rule, 'enum:')) {
                    $opts = explode(',', substr($rule, 5));
                    if ($val != null && !in_array($val, $opts, true)) {
                        $this->errors[$f][] = 'enum';
                    }
                }
            }
        }

        return !empty($this->errors);
    }

    public function errors() {
        return $this->errors;
    }

    public static function sanitize(array $in) {
        foreach ($in as $k => $v) {
            if (is_string($v)) {
                $in[$k] = trim($v);
            }
        }
        return $in;
    }
}