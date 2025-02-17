<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Το :attribute πρέπει να γίνει αποδεκτό.',
    'accepted_if' => 'Το :attribute πρέπει να γίνει αποδεκτό όταν :other είναι :value.',
    'active_url' => 'Το :attribute δεν είναι έγκυρη διεύθυνση URL.',
    'after' => 'Το :attribute πρέπει να είναι ημερομηνία μετά από την :date.',
    'after_or_equal' => 'Το :attribute πρέπει να είναι ημερομηνία μετά ή ίση με την :date.',
    'alpha' => 'Το :attribute πρέπει να περιέχει μόνο γράμματα.',
    'alpha_dash' => 'Το :attribute πρέπει να περιέχει μόνο γράμματα, αριθμούς, παύλες και κάτω παύλες.',
    'alpha_num' => 'Το :attribute πρέπει να περιέχει μόνο γράμματα και αριθμούς.',
    'array' => 'Το :attribute πρέπει να είναι πίνακας.',
    'before' => 'Το :attribute πρέπει να είναι ημερομηνία πριν από την :date.',
    'before_or_equal' => 'Το :attribute πρέπει να είναι ημερομηνία πριν ή ίση με την :date.',
    'between' => [
        'array' => 'Το :attribute πρέπει να έχει ανάμεσα σε :min και :max αντικείμενα.',
        'file' => 'Το :attribute πρέπει να είναι ανάμεσα σε :min και :max kilobytes.',
        'numeric' => 'Το :attribute πρέπει να είναι ανάμεσα σε :min και :max.',
        'string' => 'Το :attribute πρέπει να είναι ανάμεσα σε :min και :max χαρακτήρες.',
    ],
    'boolean' => 'Το πεδίο :attribute πρέπει να είναι true ή false.',
    'confirmed' => 'Η επιβεβαίωση του :attribute δεν ταιρι άζει.',
    'current_password' => 'Ο κωδικός πρόσβασης δεν είναι σωστός.',
    'date' => 'Το :attribute δεν είναι έγκυρη ημερομηνία.',
    'date_equals' => 'Το :attribute πρέπει να είναι ημερομηνία ίση με :date.',
    'date_format' => 'Το :attribute δεν ταιριάζει με το μορφότυπο :format.',
    'declined' => 'Το :attribute πρέπει να απορριφθεί.',
    'declined_if' => 'Το :attribute πρέπει να απορριφθεί όταν :other είναι :value.',
    'different' => 'Το :attribute και το :other πρέπει να είναι διαφορετικά.',
    'digits' => 'Το :attribute πρέπει να έχει :digits ψηφία.',
    'digits_between' => 'Το :attribute πρέπει να έχει ανάμεσα σε :min και :max ψηφία.',
    'dimensions' => 'Το :attribute έχει μη έγκυρες διαστάσεις εικόνας.',
    'distinct' => 'Το πεδίο :attribute έχει διπλή τιμή.',
    'email' => 'Το :attribute πρέπει να είναι μια έγκυρη διεύθυνση email.',
    'ends_with' => 'Το :attribute πρέπει να τελειώνει με ένα από τα παρακάτω: :values.',
    'enum' => 'Η επιλογή :attribute δεν είναι έγκυρη.',
    'exists' => 'Η επιλογή :attribute δεν είναι έγκυρη.',
    'file' => 'Το :attribute πρέπει να είναι αρχείο.',
    'filled' => 'Το πεδίο :attribute πρέπει να έχει μια τιμή.',
    'gt' => [
        'array' => 'Το :attribute πρέπει να έχει περισσότερα από :value αντικείμενα.',
        'file' => 'Το :attribute πρέπει να είναι μεγαλύτερο από :value kilobytes.',
        'numeric' => 'Το :attribute πρέπει να είναι μεγαλύτερο από :value.',
        'string' => 'Το :attribute πρέπει να είναι μεγαλύτερο από :value χαρακτήρες.',
    ],

    'gte' => [
        'array' => 'Το :attribute πρέπει να έχει :value στοιχεία ή περισσότερα.',
        'file' => 'Το :attribute πρέπει να είναι μεγαλύτερο ή ίσο με :value kilobytes.',
        'numeric' => 'Το :attribute πρέπει να είναι μεγαλύτερο ή ίσο με :value.',
        'string' => 'Το :attribute πρέπει να είναι μεγαλύτερο ή ίσο με :value χαρακτήρες.',
    ],
    'image' => 'Το :attribute πρέπει να είναι μια εικόνα.',
    'in' => 'Η επιλογή :attribute δεν είναι έγκυρη.',
    'in_array' => 'Το πεδίο :attribute δεν υπάρχει σε :other.',
    'integer' => 'Το :attribute πρέπει να είναι ακέραιος αριθμός.',
    'ip' => 'Το :attribute πρέπει να είναι μια έγκυρη διεύθυνση IP.',
    'ipv4' => 'Το :attribute πρέπει να είναι μια έγκυρη διεύθυνση IPv4.',
    'ipv6' => 'Το :attribute πρέπει να είναι μια έγκυρη διεύθυνση IPv6.',
    'json' => 'Το :attribute πρέπει να είναι μια έγκυρη συμβολοσειρά JSON.',
    'lt' => [
        'array' => 'Το :attribute πρέπει να έχει λιγότερα από :value στοιχεία.',
        'file' => 'Το :attribute πρέπει να είναι μικρότερο από :value kilobytes.',
        'numeric' => 'Το :attribute πρέπει να είναι μικρότερο από :value.',
        'string' => 'Το :attribute πρέπει να είναι μικρότερο από :value χαρακτήρες.',
    ],
    'lte' => [
        'array' => 'Το :attribute δεν πρέπει να έχει περισσότερα από :value στοιχεία.',
        'file' => 'Το :attribute πρέπει να είναι μικρότερο ή ίσο με :value kilobytes.',
        'numeric' => 'Το :attribute πρέπει να είναι μικρότερο ή ίσο με :value.',
        'string' => 'Το :attribute πρέπει να είναι μικρότερο ή ίσο με :value χαρακτήρες.',
    ],
    'mac_address' => 'Το :attribute πρέπει να είναι μια έγκυρη διεύθυνση MAC.',
    'max' => [
        'array' => 'Το :attribute δεν πρέπει να έχει περισσότερα από :max στοιχεία.',
        'file' => 'Το :attribute δεν πρέπει να είναι μεγαλύτερο από :max kilobytes.',
        'numeric' => 'Το :attribute δεν πρέπει να είναι μεγαλύτερο από :max.',
        'string' => 'Το :attribute δεν πρέπει να είναι μεγαλύτερο από :max χαρακτήρες.',
    ],
    'mimes' => 'Το :attribute πρέπει να είναι ένα αρχείο τύπου: :values.',
    'mimetypes' => 'Το :attribute πρέπει να είναι ένα αρχείο τύπου: :values.',
    'min' => [
        'array' => 'Το :attribute πρέπει να έχει τουλάχιστον :min στοιχεία.',
        'file' => 'Το :attribute πρέπει να είναι τουλάχιστον :min kilobytes.',
        'numeric' => 'Το :attribute πρέπει να είναι τουλάχιστον :min.',
        'string' => 'Το :attribute πρέπει να είναι τουλάχιστον :min χαρακτήρες.',
    ],
    'multiple_of' => 'Το :attribute πρέπει να είναι πολλαπλάσιο του :value.',
    'not_in' => 'Η επιλογή :attribute δεν είναι έγκυρη.',
    'not_regex' => 'Η μορφή :attribute δεν είναι έγκυρη.',
    'numeric' => 'Το :attribute πρέπει να είναι ένας αριθμός.',
    'present' => 'Το πεδίο :attribute πρέπει να είναι παρόν.',
    'prohibited' => 'Το πεδίο :attribute είναι απαγορευμένο.',
    'prohibited_if' => 'Το πεδίο :attribute είναι απαγορευμένο όταν το :other είναι :value.',
    'prohibited_unless' => 'Το πεδίο :attribute είναι απαγορευμένο εκτός αν το :other βρίσκεται στα :values.',
    'prohibits' => 'Το πεδίο :attribute απαγορεύει το :other να είναι παρόν.',
    'regex' => 'Η μορφή :attribute δεν είναι έγκυρη.',
    'required' => 'Το πεδίο :attribute απαιτείται.',
    'required_array_keys' => 'Το πεδίο :attribute πρέπει να περιέχει εισαγωγές για: :values.',
    'required_if' => 'Το πεδίο :attribute απαιτείται όταν το :other είναι :value.',
    'required_unless' => 'Το πεδίο :attribute απαιτείται εκτός αν το :other βρίσκεται στα :values.',
    'required_with' => 'Το πεδίο :attribute απαιτείται όταν το :values είναι παρόν.',
    'required_with_all' => 'Το πεδίο :attribute απαιτείται όταν τα :values είναι παρόντα.',
    'required_without' => 'Το πεδίο :attribute απαιτείται όταν το :values δεν είναι παρόν.',
    'required_without_all' => 'Το πεδίο :attribute απαιτείται όταν κανένα από τα :values δεν είναι παρόντα.',
    'same' => 'Το :attribute και το :other πρέπει να ταιριάζουν.',
    'size' => [
        'array' => 'Το :attribute πρέπει να περιέχει :size στοιχεία.',
        'file' => 'Το :attribute πρέπει να είναι :size kilobytes.',
        'numeric' => 'Το :attribute πρέπει να είναι :size.',
        'string' => 'Το :attribute πρέπει να έχει :size χαρακτήρες.',
    ],
    'starts_with' => 'Το :attribute πρέπει να ξεκινά με ένα από τα εξής: :values.',
    'string' => 'Το :attribute πρέπει να είναι μια συμβολοσειρά.',
    'timezone' => 'Το :attribute πρέπει να είναι μια έγκυρη ζώνη ώρας.',
    'unique' => 'Το :attribute έχει ήδη ληφθεί.',
    'uploaded' => 'Το :attribute απέτυχε να μεταφορτωθεί.',
    'url' => 'Το :attribute πρέπει να είναι ένα έγκυρο URL.',
    'uuid' => 'Το :attribute πρέπει να είναι ένα έγκυρο UUID.',    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */
    'attributes' => [],

];
