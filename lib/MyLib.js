//Regex validator:
$.validator.addMethod("regex", function (value, element, pattern) {
    if (pattern instanceof Array) {
        for(p of pattern) {
            if (!p.test(value))
                return false;
        }
        return true;
    } else {
        return pattern.test(value);
    }
}, "Please enter a valid input.");