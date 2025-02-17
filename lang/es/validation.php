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

   'accepted'=>'El: el atributo debe ser aceptado.',
   'accepted_if'=>'El: el atributo debe ser aceptado cuando: otro es: valor.',
   'active_url'=>' El: atributo no es una URL válida.',
   'after'=>'El: el atributo debe ser una fecha después: fecha.',
   'after_or_equal'=>'El atributo: debe ser una fecha después o igual a: fecha.',
   'alpha'=>'El: el atributo solo debe contener letras.',
   'alpha_dash'=>'El: el atributo solo debe contener letras, números, guiones y subrayos.',
   'alpha_num'=>'El: el atributo solo debe contener letras y números.',
   'array'=>'El: el atributo debe ser una matriz.',
   'before'=>'El: el atributo debe ser una fecha antes: fecha.',
   'before_or_equal'=>'El: el atributo debe ser una fecha anterior o igual a: fecha.',
   'between'=> [
       'array'=>'El: el atributo debe tener entre: Min y: elementos máximos.',
       'file'=>'El: el atributo debe ser entre: min y: max kilobytes.',
       'numeric'=>'El: el atributo debe ser entre: min y: max.',
       'string'=>'El: el atributo debe ser entre: Min y: Max caracteres.',
    ],
   'boolean'=>'El campo de atributo: debe ser verdadero o falso.',
   'confirmed'=>':: La confirmación del atributo no coincide.',
   'current_password'=>'La contraseña es incorrecta.',
   'date'=>'El atributo: no es una fecha válida.',
   'date_equals'=>'El: el atributo debe ser una fecha igual a: fecha.',
   'date_format'=>'El: atributo no coincide con el formato: formato.',
   'declined'=>'El: el atributo debe ser rechazado.',
   'declined_if'=>'El: el atributo debe ser rechazado cuando: otro es: valor.',
   'different'=>'El: atributo y: otro debe ser diferente.',
   'digits'=>'El: el atributo debe ser: dígitos dígitos.',
   'digits_between'=>'El: el atributo debe ser entre: Min y: dígitos máximos.',
   'dimensions'=>'El: el atributo tiene dimensiones de imagen no válidas.',
   'distinct'=>'El campo de atributo: tiene un valor duplicado.',
   'email'=>'El: el atributo debe ser una dirección de correo electrónico válida.',
   'ends_with'=>'El atributo: debe terminar con uno de los siguientes :: valores.',
   'enum'=>'El atributo seleccionado: no es válido.',
   'exists'=>'El atributo seleccionado: no es válido.',
   'file'=>'El atributo: debe ser un archivo.',
   'filled'=>'El campo de atributo: debe tener un valor.',
   'gt'=> [
       'array'=>'El: el atributo debe tener más de: elementos de valor.',
       'file'=>'El: el atributo debe ser mayor que: valor kilobytes.',
       'numeric'=>'El: el atributo debe ser mayor que: valor.',
       'string'=>'El: el atributo debe ser mayor que: Value caracteres.',
    ],
   'gte'=> [
       'array'=>'El: atributo debe tener: artículos de valor o más.',
       'file'=>'El: el atributo debe ser mayor o igual a: valor kilobytes.',
       'numeric'=>'El: el atributo debe ser mayor o igual a: valor.',
       'string'=>'El: el atributo debe ser mayor o igual a los caracteres de valor.',
    ],
   'image'=>'El: el atributo debe ser una imagen.',
   'in'=>'El atributo seleccionado: no es válido.',
   'in_array'=>'El campo de atributo: no existe en: otro.',
   'integer'=>'El: el atributo debe ser un entero.',
   'ip'=>'El: el atributo debe ser una dirección IP válida.',
   'ipv4'=>'El: el atributo debe ser una dirección IPv4 válida.',
   'ipv6'=>'El: el atributo debe ser una dirección IPv6 válida.',
   'json'=>'El: el atributo debe ser una cadena JSON válida.',
   'lt'=> [
       'array'=>'El: el atributo debe tener menos de: elementos de valor.',
       'file'=>'El: el atributo debe ser menor que: valor kilobytes.',
       'numeric'=>'El: el atributo debe ser menor que: valor.',
       'string'=>'El: el atributo debe ser menor que: los caracteres de valor.',
    ],
   'lte'=> [
       'array'=>'El: atributo no debe tener más de: elementos de valor.',
       'file'=>'El: el atributo debe ser menor o igual a: valor kilobytes.',
       'numeric'=>'El: el atributo debe ser menor o igual a: valor.',
       'string'=>'El: el atributo debe ser menor o igual a los caracteres de valor.',
    ],
   'mac_address'=>'El: el atributo debe ser una dirección MAC válida.',
   'max'=> [
       'array'=>'El atributo: no debe tener más de: elementos máximos.',
       'file'=>'El: el atributo no debe ser mayor que: max kilobytes.',
       'numeric'=>'El: el atributo no debe ser mayor que: max.',
       'string'=>'El: el atributo no debe ser mayor que: personajes máximos.',
    ],
   'mimes'=>'El atributo: debe ser un archivo de tipo :: valores.',
   'mimetypes'=>'El atributo: debe ser un archivo de tipo :: valores.',
   'min'=> [
       'array'=>'El: el atributo debe tener al menos: Min elementos.',
       'file'=>'El: el atributo debe ser al menos: Min Kilobytes.',
       'numeric'=>'El: el atributo debe ser al menos: min.',
       'string'=>'El: el atributo debe ser al menos: caracteres min.',
    ],
   'multiple_of'=>'El: el atributo debe ser un múltiplo de: valor.',
   'not_in'=>'El atributo seleccionado: no es válido.',
   'not_regex'=>'El formato de atributo: no es válido.',
   'numeric'=>'El: el atributo debe ser un número.',
   'present'=>'El campo de atributo: debe estar presente.',
   'prohibited'=>'El campo de atributo: está prohibido.',
   'prohibited_if'=>'El campo de atributo: está prohibido cuando: otro es: valor.',
   'prohibited_unless'=>'El campo de atributo: está prohibido a menos: otro está en: valores.',
   'prohibits'=>'El campo de atributo: prohíbe: otro estar presente.',
   'regex'=>'El formato de atributo: no es válido.',
   'required'=>'El campo de atributo: se requiere.',
   'required_array_keys'=>'El campo de atributo: debe contener entradas para :: valores.',
   'required_if'=>'El campo de atributo: se requiere cuando: otro es: valor.',
   'required_unless'=>'El campo de atributo: se requiere a menos que el otro esté en: valores.',
   'required_with'=>'El campo de atributo: se requiere cuando: los valores están presentes.',
   'required_with_all'=>'El campo de atributo: se requiere cuando: los valores están presentes.',
   'required_without'=>'El campo de atributo: se requiere cuando: los valores no están presentes.',
   'required_without_all'=>'El campo de atributo: se requiere cuando ninguno de: los valores están presentes.',
   'same'=>'El: atributo y: otro debe coincidir.',
   'size'=> [
       'array'=>'El: el atributo debe contener: Elementos de tamaño.',
       'file'=>'El: el atributo debe ser: kilobytes de tamaño.',
       'numeric'=>'El: el atributo debe ser: tamaño.',
       'string'=>'El atributo: debe ser: caracteres de tamaño.',
    ],
   'starts_with'=>'El: atributo debe comenzar con uno de los siguientes valores.',
   'string'=>'El: el atributo debe ser una cadena.',
   'timezone'=>'El: el atributo debe ser una zona horaria válida.',
   'unique'=>'El: el atributo ya se ha tomado.',
   'uploaded'=>'El atributo: no pudo cargar.',
   'url'=>'El: el atributo debe ser una URL válida.',
   'uuid'=>'El: el atributo debe ser un UUID válido.',

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

   'custom'=> [
       'attribute-name'=> [
           'rule-name'=>'mensaje personalizado',
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

   'attributes'=> [],

];
