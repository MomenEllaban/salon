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

    'accepted' => 'Das :attribute muss akzeptiert werden.',
    'accepted_if' => 'Das :attribute muss akzeptiert werden, wenn :other :value ist.',
    'active_url' => 'Das :attribute ist keine gültige URL.',
    'after' => 'Das :attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => 'Das :attribute muss ein Datum nach oder gleich dem :date sein.',
    'alpha' => 'Das :attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => 'Das :attribute darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche enthalten.',
    'alpha_num' => 'Das :attribute darf nur Buchstaben und Zahlen enthalten.',
    'array' => 'Das :attribute muss ein Array sein.',
    'before' => 'Das :attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => 'Das :attribute muss ein Datum vor oder gleich dem :date sein.',
    'between' => [
        'array' => 'Das :attribute muss zwischen :min und :max Elemente haben.',
        'file' => 'Die Dateigröße des :attribute muss zwischen :min und :max Kilobyte liegen.',
        'numeric' => 'Der Wert des :attribute muss zwischen :min und :max liegen.',
        'string' => 'Die Länge des :attribute muss zwischen :min und :max Zeichen liegen.',
    ],
    'boolean' => 'Das :attribute Feld muss true oder false sein.',
    'confirmed' => 'Die :attribute Bestätigung stimmt nicht überein.',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => 'Das :attribute ist kein gültiges Datum.',
    'date_equals' => 'Das :attribute muss ein Datum gleich :date sein.',
    'date_format' => 'Das :attribute entspricht nicht dem Format :format.',
    'declined' => 'Das :attribute muss abgelehnt werden.',
    'declined_if' => 'Das :attribute muss abgelehnt werden, wenn :other :value ist.',
    'different' => 'Das :attribute und :other müssen unterschiedlich sein.',
    'digits' => 'Das :attribute muss :digits Ziffern enthalten.',
    'digits_between' => 'Die Länge des :attribute muss zwischen :min und :max Ziffern liegen.',
    'dimensions' => 'Das :attribute hat ungültige Bildabmessungen.',
    'distinct' => 'Das :attribute Feld enthält einen Duplikatwert.',
    'email' => 'Das :attribute muss eine gültige E-Mail-Adresse sein.',
    'ends_with' => 'Das :attribute muss mit einem der folgenden Werte enden: :values.',
    'enum' => 'Die ausgewählte :attribute ist ungültig.',
    'exists' => 'Das ausgewählte :attribute ist ungültig.',
    'file' => 'Das :attribute muss eine Datei sein.',
    'filled' => 'Das :attribute Feld muss einen Wert enthalten.',
    'gt' => [
        'array' => 'Das :attribute muss mehr als :value Elemente haben.',
        'file' => 'Die Dateigröße des :attribute muss größer als :value Kilobyte sein.',
        'numeric' => 'Der Wert des :attribute muss größer als :value sein.',
        'string' => 'Die Länge des :attribute muss größer als :value Zeichen sein.',
    ],
    'gte' => [
        'array' => 'Das :attribute muss :value Elemente oder mehr enthalten.',
        'file' => 'Die Dateigröße des :attribute muss größer oder gleich :value Kilobyte sein.',
        'numeric' => 'Der Wert des :attribute muss größer oder gleich :value sein.',
        'string' => 'Die Länge des :attribute muss größer oder gleich :value Zeichen sein.',
    ],
    'image' => 'Das :attribute muss ein Bild sein.',
    'in' => 'Das ausgewählte :attribute ist ungültig.',
    'in_array' => 'Das :attribute Feld existiert nicht in :other.',
    'integer' => 'Das :attribute muss eine ganze Zahl sein.',
    'ip' => 'Das :attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => 'Das :attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6' => 'Das :attribute muss eine gültige IPv6-Adresse sein.',
    'json' => 'Das :attribute muss ein gültiger JSON-String sein.',
    'lt' => [
        'array' => 'Das :attribute muss weniger als :value Elemente haben.',
        'file' => 'Die Dateigröße des :attribute muss kleiner als :value Kilobyte sein.',
        'numeric' => 'Der Wert des :attribute muss kleiner als :value sein.',
        'string' => 'Die Länge des :attribute muss kleiner als :value Zeichen sein.',
    ],
    'lte' => [
        'array' => 'Das :attribute darf nicht mehr als :value Elemente enthalten.',
        'file' => 'Die Dateigröße des :attribute muss kleiner oder gleich :value Kilobyte sein.',
        'numeric' => 'Der Wert des :attribute muss kleiner oder gleich :value sein.',
        'string' => 'Die Länge des :attribute muss kleiner oder gleich :value Zeichen sein.',
    ],
    'mac_address' => 'Das :attribute muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => 'Das :attribute darf nicht mehr als :max Elemente enthalten.',
        'file' => 'Die Dateigröße des :attribute darf nicht größer als :max Kilobyte sein.',
        'numeric' => 'Der Wert des :attribute darf nicht größer als :max sein.',
        'string' => 'Die Länge des :attribute darf nicht größer als :max Zeichen sein.',
    ],
    'mimes' => 'Das :attribute muss eine Datei vom Typ: :values sein.',
    'mimetypes' => 'Das :attribute muss eine Datei vom Typ: :values sein.',
    'min' => [
        'array' => 'Das :attribute muss mindestens :min Elemente enthalten.',
        'file' => 'Die Dateigröße des :attribute muss mindestens :min Kilobyte sein.',
        'numeric' => 'Der Wert des :attribute muss mindestens :min sein.',
        'string' => 'Die Länge des :attribute muss mindestens :min Zeichen sein.',
    ],
    'multiple_of' => 'Das :attribute muss ein Vielfaches von :value sein.',
    'not_in' => 'Das ausgewählte :attribute ist ungültig.',
    'not_regex' => 'Das Format des :attribute ist ungültig.',
    'numeric' => 'Das :attribute muss eine Zahl sein.',
    'present' => 'Das :attribute Feld muss vorhanden sein.',
    'prohibited' => 'Das :attribute Feld ist verboten.',
    'prohibited_if' => 'Das :attribute Feld ist verboten, wenn :other :value ist.',
    'prohibited_unless' => 'Das :attribute Feld ist verboten, es sei denn, :other befindet sich in :values.',
    'prohibits' => 'Das :attribute Feld verbietet das Vorhandensein von :other.',
    'regex' => 'Das Format des :attribute ist ungültig.',
    'required' => 'Das :attribute Feld ist erforderlich.',
    'required_array_keys' => 'Das :attribute Feld muss Einträge für: :values enthalten.',
    'required_if' => 'Das :attribute Feld ist erforderlich, wenn :other :value ist.',
    'required_unless' => 'Das :attribute Feld ist erforderlich, es sei denn, :other befindet sich in :values.',
    'required_with' => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all' => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden sind.',
    'required_without' => 'Das :attribute Feld ist erforderlich, wenn :values nicht vorhanden ist.',
    'required_without_all' => 'Das :attribute Feld ist erforderlich, wenn keines der :values vorhanden ist.',
    'same' => 'Das :attribute und :other müssen übereinstimmen.',
    'size' => [
        'array' => 'Das :attribute muss :size Elemente enthalten.',
        'file' => 'Die Dateigröße des :attribute muss :size Kilobyte betragen.',
        'numeric' => 'Der Wert des :attribute muss :size sein.',
        'string' => 'Die Länge des :attribute muss :size Zeichen betragen.',
    ],
    'starts_with' => 'Das :attribute muss mit einem der folgenden Werte beginnen: :values.',
    'string' => 'Das :attribute muss eine Zeichenkette sein.',
    'timezone' => 'Das :attribute muss eine gültige Zeitzone sein.',
    'unique' => 'Das :attribute wurde bereits verwendet.',
    'uploaded' => 'Das :attribute konnte nicht hochgeladen werden.',
    'url' => 'Das :attribute muss eine gültige URL sein.',
    'uuid' => 'Das :attribute muss eine gültige UUID sein.',
    /*
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
