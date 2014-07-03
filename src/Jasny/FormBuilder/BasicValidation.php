<?php

namespace Jasny\FormBuilder;

/**
 * Basic input validation
 *  - Server side equivilent of HTML5 validation.
 *  - Process upload errors.
 *  - Client side support for minlength and matching element values (using JavaScript).
 */
trait BasicValidation
{
    /**
     * Validate if the control has a value if it's required.
     * 
     * @return boolean
     */
    protected function validateRequired()
    {
        if ($this->getAttr('required')) {
            $value = $this->getValue();
            if ($value === null || $value === '') {
                $this->error = $this->setError($this->getOption('error:required'));
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate if min and max for value.
     * 
     * @return boolean
     */
    protected function validateMinMax()
    {
        $value = $this->format($this->getValue());
        
        $min = $this->getAttr('min');
        if (isset($min) && $min !== false && $value < $min) {
            $this->setError($this->getOption('error:min'));
            return false;
        }
        
        $max = $this->getAttr('max');
        if (isset($max) && $max !== false && $value > $max) {
            $this->setError($this->getOption('error:max'));
            return false;
        }
        
        return true;
    }

    /**
     * Validate the length of the value.
     * 
     * @return boolean
     */
    protected function validateLength()
    {
        $value = $this->getValue();
        
        $minlength = $this->getAttr('minlength') ?: $this->getAttr('data-minlength');
        if (isset($minlength) && $minlength !== false && strlen($value) > $minlength) {
            $this->setError($this->getOption('error:minlength'));
            return false;
        }
        
        $maxlength = $this->getAttr('maxlength');
        if (isset($maxlength) && $maxlength !== false && strlen($value) > $maxlength) {
            $this->setError($this->getOption('error:maxlength'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate the value of the control against a regex pattern.
     * 
     * @return boolean
     */
    protected function validatePattern()
    {
        $pattern = $this->getAttr('pattern');
        if ($pattern && !preg_match('/' . str_replace('/', '\/', $pattern) . '/', $this->getValue())) {
            $this->setError($this->getOption('error:pattern'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Match value against another control.
     * 
     * @return boolean
     */
    protected function validateMatch()
    {
        $other = $this->getOption('match');
        if (!isset($other)) return true;
        
        if (!$other instanceof Control) $other = $this->getForm()->getElement($other);
        
        if ($this->getValue() != $other->getValue()) {
            $this->setError($this->getOption('error:match'));
            return false;
        }
        
        return true;
    }

    /**
     * Check if there ware upload errors.
     * 
     * @return boolean
     */
    protected function validateUpload()
    {
        $value = $this->getValue();
        
        // No error
        if (!is_array($value) || empty($value['error'])) return true;
        
        // An error
        $errors = $this->getOption('error:upload');
        $this->setError($errors[$value['error']]);
        return false;
    }
    
    
    /**
     * Validate if value matches the input type.
     * 
     * @return boolean
     */
    protected function validateType()
    {
        $type = $this->attr['type'];
        $method = 'validateType' . str_replace(' ', '', ucwords(str_replace('-', ' ', $type)));
        
        if (!method_exists($this, $method) || $this->$method()) return true;
        
        if ($type !== 'file') $this->setError($this->getOption('error:type'));
        return false;
    }

    /**
     * Validate the value for 'color' input type.
     * 
     * @return boolean
     */
    protected function validateTypeColor()
    {
        $value = $this->getValue();
        return strlen($value) === 7 && $value[0] === '#' && ctype_xdigit(substr($value, 1));
    }
    
    /**
     * Validate the value for 'number' input type.
     * 
     * @return boolean
     */
    protected function validateTypeNumber()
    {
        $value = $this->getValue();
        return is_int($value) || ctype_digit((string)$value);
    }
    
    /**
     * Validate the value for 'range' input type.
     * 
     * @return boolean
     */
    protected function validateTypeRange()
    {
        return is_numeric($this->getValue());
    }
    
    /**
     * Validate the value for 'date' input type.
     * 
     * @return boolean
     */
    protected function validateTypeDate()
    {
        $res = date_parse_from_format("Y-m-d", $this->getValue());
        return $res['error_count'] === 0;
    }
    
    /**
     * Validate the value for 'datetime' input type.
     * 
     * @return boolean
     */
    protected function validateTypeDatetime()
    {
        $res = date_parse_from_format("Y-m-d\TH:i:s", $this->getValue());
        return $res['error_count'] === 0;
    }
    
    /**
     * Validate the value for 'datetime' input type.
     * 
     * @return boolean
     */
    protected function validateTypeDatetimeLocal()
    {
        $res = date_parse_from_format(DateTime::RFC3339, $this->getValue());
        return $res['error_count'] === 0;
    }
    
    /**
     * Validate the value for 'datetime' input type.
     * 
     * @return boolean
     */
    protected function validateTypeTime()
    {
        $res = date_parse_from_format("H:i:s", $this->getValue());
        return $res['error_count'] === 0;
    }
    
    /**
     * Validate the value for 'month' input type.
     * 
     * @return boolean
     */
    protected function validateTypeMonth()
    {
        $res = date_parse_from_format("Y-m", $this->getValue());
        return $res['error_count'] === 0;
    }
    
    /**
     * Validate the value for 'week' input type.
     * 
     * @return boolean
     */
    protected function validateTypeWeek()
    {
        $res = date_parse_from_format("o-\WW", $this->getValue());
        return $res['error_count'] === 0;
    }
    
    /**
     * Validate the value for 'url' input type.
     * 
     * @return boolean
     */
    protected function validateTypeUrl()
    {
        $value = $this->getValue();
        $pos = strpos($value, ':');
        return $pos !== false && ctype_alpha(substr($value, 0, $pos));
    }

    /**
     * Validate the value for 'email' input type.
     * 
     * @return boolean
     */
    protected function validateTypeEmail()
    {
        return preg_match('/^[\w\-\.]+@[\w\-\.]+\w+$/', $this->getValue());
    }
    
    
    /**
     * Get JavaScript for custom validation.
     * 
     * @return string
     */
    public function getValidationScript()
    {
        if (!$this->getOption('validation-script')) return null;
        
        $rules = $this->getValidationScriptRules();
        if (empty($rules)) return null;
        
        foreach ($this->getDecorators() as $decorator) {
            if ($decorator->applyToValidationScript($this, $rules));
        }
        
        return $this->generateValidationScript($rules);
    }
    

    /**
     * Get the rules to build up the validation script
     * 
     * @return array
     */
    protected function getValidationScriptRules()
    {
        $rules['minlength'] = $this->getValidationScriptMinlength();
        $rules['match'] = $this->getValidationScriptMatch();
        
        return array_filter($rules);
    }
    
    /**
     * Generate validation script
     * 
     * @param array $rules
     */
    protected function generateValidationScript(array $rules)
    {
        $id = addcslashes($this->getId(), '"');
        
        foreach ($rules as $test => &$rule) {
            $message = $this->parseForScript($this->getOption('error:' . $test));
            
            $rule = <<<SCRIPT
if (!$rule) { 
    this.setCustomValidity("$message");
    return;
} else {
    this.setCustomValidity("");
}
SCRIPT;
        }
        
        $script = join("\n", $rules);
        
        return <<<SCRIPT
<script type="text/javascript">
    document.getElementById("$id").addEventListener("input", function() {
        $script
    });
</script>
SCRIPT;
    }
    
    /**
     * Get script to match the minimum length
     * 
     * @return string
     */
    protected function getValidationScriptMinlength()
    {
        $attr = 'minlength';
        $minlength = $this->getAttr('minlength');
        
        if (!$minlength) {
            $attr = 'data-minlength';
            $minlength = $this->getAttr('data-minlength');
        }
        
        if (!isset($minlength)) return null;
        
        return 'this.value.length >= this.getAttribute("' . $attr . '")';
    }
    
    /**
     * Get script to match other element
     * 
     * @return string
     */
    protected function getValidationScriptMatch()
    {
        $other = $this->getOption('match');
        if (!$other) return null;
        
        if (!$other instanceof Control) $other = $this->getForm()->getElement($other);
        
        return "this.value == " . $this->castForScript($other);
    }
    
    
    /**
     * Parse a message, inserting values for placeholders for JavaScript.
     * 
     * @param string $message
     * @return string
     */
    public function parseForScript($message)
    {
        return preg_replace_callback('/{{\s*([^}])++\s*}}/', array($this, 'resolvePlaceholderForScript'), $message);
    }
    
    /**
     * Get a value for a placeholder for JavaScript.
     * 
     * @param string $var
     * @return string
     */
    protected function resolvePlaceholderForScript($var)
    {
        // preg_replace callback
        if (is_array($var)) {
            $var = $var[1];
        }
        
        if ($this->getAttr($var) !== null) {
            return '" + this.getAttribute("' . addcslashes($var, '"') . '") + "';
        }
        
        switch ($var) {
            case 'value':  return '" + this.value + "';
            case 'length': return '" + this.value.length + "';
        }
        
        $value = $this->resolvePlaceholder($var);
        
        if ($value instanceof Control) {
            $id = addcslashes($value->getId(), '"');
            return '" + document.getElementById("' . $id . '").value + "';
        }
        
        return json_encode($value);
    }
}
