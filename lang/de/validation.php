<?php

return [
    'accepted'             => ':attribute muss akzeptiert werden.',
    'active_url'           => ':attribute ist keine gültige URL.',
    'after'                => ':attribute muss ein Datum nach :date sein.',
    'after_or_equal'       => ':attribute muss ein Datum nach oder gleich :date sein.',
    'alpha'                => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => ':attribute darf nur Buchstaben, Zahlen und Bindestriche enthalten.',
    'alpha_num'            => ':attribute darf nur Buchstaben und Zahlen enthalten.',
    'array'                => ':attribute muss ein Array sein.',
    'before'               => ':attribute muss ein Datum vor :date sein.',
    'before_or_equal'      => ':attribute muss ein Datum vor oder gleich :date sein.',
    'between'              => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'file'    => ':attribute muss zwischen :min und :max Kilobytes groß sein.',
        'string'  => ':attribute muss zwischen :min und :max Zeichen lang sein.',
        'array'   => ':attribute muss zwischen :min und :max Elemente enthalten.',
    ],
    'boolean'              => 'Das Feld :attribute muss true oder false sein.',
    'confirmed'            => 'Die Bestätigung von :attribute stimmt nicht überein.',
    'date'                 => ':attribute ist kein gültiges Datum.',
    'date_equals'          => ':attribute muss ein gleiches Datum wie :date sein.',
    'date_format'          => ':attribute entspricht nicht dem Format :format.',
    'different'            => ':attribute und :other müssen unterschiedlich sein.',
    'digits'               => ':attribute muss :digits Ziffern haben.',
    'digits_between'       => ':attribute muss zwischen :min und :max Ziffern haben.',
    'email'                => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'exists'               => 'Das ausgewählte :attribute ist ungültig.',
    'file'                 => ':attribute muss eine Datei sein.',
    'filled'               => 'Das Feld :attribute muss einen Wert haben.',
    'gt'                   => [
        'numeric' => ':attribute muss größer als :value sein.',
        'file'    => ':attribute muss größer als :value Kilobytes sein.',
        'string'  => ':attribute muss länger als :value Zeichen sein.',
        'array'   => ':attribute muss mehr als :value Elemente enthalten.',
    ],
    'gte'                  => [
        'numeric' => ':attribute muss größer oder gleich :value sein.',
        'file'    => ':attribute muss größer oder gleich :value Kilobytes sein.',
        'string'  => ':attribute muss länger oder gleich :value Zeichen sein.',
        'array'   => ':attribute muss :value Elemente oder mehr haben.',
    ],
    'image'                => ':attribute muss ein Bild sein.',
    'in'                   => 'Das ausgewählte :attribute ist ungültig.',
    'in_array'             => 'Das Feld :attribute existiert nicht in :other.',
    'integer'              => ':attribute muss eine ganze Zahl sein.',
    'ip'                   => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4'                 => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6'                 => ':attribute muss eine gültige IPv6-Adresse sein.',
    'json'                 => ':attribute muss ein gültiger JSON-String sein.',
    'lt'                   => [
        'numeric' => ':attribute muss kleiner als :value sein.',
        'file'    => ':attribute muss kleiner als :value Kilobytes sein.',
        'string'  => ':attribute muss kürzer als :value Zeichen sein.',
        'array'   => ':attribute muss weniger als :value Elemente enthalten.',
    ],
    'lte'                  => [
        'numeric' => ':attribute muss kleiner oder gleich :value sein.',
        'file'    => ':attribute muss kleiner oder gleich :value Kilobytes sein.',
        'string'  => ':attribute muss kürzer oder gleich :value Zeichen sein.',
        'array'   => ':attribute darf nicht mehr als :value Elemente haben.',
    ],
    'max'                  => [
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'file'    => ':attribute darf nicht größer als :max Kilobytes sein.',
        'string'  => ':attribute darf nicht länger als :max Zeichen sein.',
        'array'   => ':attribute darf nicht mehr als :max Elemente enthalten.',
    ],
    'mimes'                => ':attribute muss eine Datei des Typs: :values sein.',
    'min'                  => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file'    => ':attribute muss mindestens :min Kilobytes groß sein.',
        'string'  => ':attribute muss mindestens :min Zeichen lang sein.',
        'array'   => ':attribute muss mindestens :min Elemente enthalten.',
    ],
    'required'             => 'Das Feld :attribute ist erforderlich.',
    'string'               => ':attribute muss ein String sein.',
    'unique'               => ':attribute ist bereits vergeben.',
    'url'                  => 'Das Format von :attribute ist ungültig.',

    /*
    |--------------------------------------------------------------------------
    | Benutzerdefinierte Validierungsattribute
    |--------------------------------------------------------------------------
    |
    | Hier können Sie benutzerdefinierte Namen für Attribute angeben.
    |
    */

    'custom' => [
        'role.name' => [
            'required_with' => 'Rollenname ist erforderlich',
            'unique' => 'Rollenname ist bereits vergeben',
        ],
        'role.permissions' => [
            'required_with' => 'Berechtigung ist erforderlich',
            'array' => 'Berechtigung muss ein Array sein',
        ],
        'roles.id' => [
            'required_with' => 'Rollen-ID ist erforderlich',
            'exists' => 'Rollen-ID muss existieren',
        ],
        'roles.name' => [
            'required_with' => 'Rollenname ist erforderlich',
            'unique' => 'Rollenname ist bereits vergeben.',
            'string' => 'Rollenname muss ein String sein',
        ],
        'roles.permissions' => [
            'required_with' => 'Berechtigung ist erforderlich',
            'array' => 'Rollenberechtigungen müssen ein Array sein',
        ],
        'forms.name' => [
            'required_with' => 'Berechtigungsname ist erforderlich',
            'unique' => 'Berechtigungsname ist bereits vergeben',
        ],
        'permissions.id' => [
            'required_with' => 'Berechtigungs-ID ist erforderlich',
        ],
        'permissions.name' => [
            'required_with' => 'Berechtigungsname ist erforderlich',
            'unique' => 'Berechtigungsname ist bereits vergeben.',
            'string' => 'Berechtigungsname muss eine Zeichenkette sein',
        ],
        'platform' => [
            'required' => 'Der Name des sozialen Netzwerks ist erforderlich.',
        ],
        'platform.unique'   => 'Dieser Name des sozialen Netzwerks existiert bereits.',
        'platform.string'   => 'Der Name muss eine Zeichenkette sein.',

        'location.required' => 'Der Standort ist erforderlich.',

        'date.required'     => 'Das Datum ist erforderlich.',
        'date.date'         => 'Bitte geben Sie ein gültiges Datum ein.',
        'date_must_be_today' => ':attribute muss heute sein.',

        'forms.array'       => 'Die Beitragsdaten müssen ein Array sein.',

        'forms.*.name.required_with' => 'Der Name des Beitrags ist erforderlich.',
        'forms.*.name.string'        => 'Der Name des Beitrags muss Text sein.',
        'forms.*.name.max'           => 'Der Name des Beitrags darf 255 Zeichen nicht überschreiten.',

        'forms.*.description.required_with' => 'Die Beschreibung des Beitrags ist erforderlich.',
        'forms.*.description.string'        => 'Die Beschreibung des Beitrags muss Text sein.',
        'forms.*.description.max'           => 'Die Beschreibung darf 255 Zeichen nicht überschreiten.',

        'forms.*.tags.array'         => 'Die Tags des Beitrags müssen ein Array sein.',
        'forms.*.tags.required_with' => 'Die Tags des Beitrags sind erforderlich.',

        'forms.required' => 'Das Post-Feld ist erforderlich.',
        'forms.*.name.required' => 'Der Name des Posts ist erforderlich.',
        'forms.*.description.required' => 'Die Beschreibung des Posts ist erforderlich.',
        'forms.*.tags.required' => 'Die Tags des Posts sind erforderlich.',
        'id.required' => 'Die Social-Media-ID ist erforderlich.',
        'id.exists' => 'Die ausgewählte Social-Media-ID ist ungültig.',

        'posts.*.id.required_with' => 'Die Beitrags-ID ist erforderlich.',
        'posts.*.id.exists' => 'Die ausgewählte Beitrags-ID ist ungültig.',

        'posts.*.name.required_with' => 'Der Name des Beitrags ist erforderlich.',
        'posts.*.name.string' => 'Der Name des Beitrags muss eine Zeichenkette sein.',
        'posts.*.name.max' => 'Der Name des Beitrags darf 255 Zeichen nicht überschreiten.',

        'posts.*.description.required_with' => 'Die Beschreibung des Beitrags ist erforderlich.',
        'posts.*.description.string' => 'Die Beschreibung des Beitrags muss eine Zeichenkette sein.',
        'posts.*.description.max' => 'Die Beschreibung darf 255 Zeichen nicht überschreiten.',

        'posts.*.tags.required_with' => 'Mindestens ein Tag ist für jeden Beitrag erforderlich.',
        'posts.*.tags.array' => 'Die Tags des Beitrags müssen ein Array sein.',

        'name' => [
            'required' => 'Name ist erforderlich.',
            'string'   => 'Name muss eine Zeichenkette sein.',
            'max'      => 'Name darf nicht länger als 30 Zeichen sein.',
        ],
        'email' => [
            'required' => 'E-Mail ist erforderlich.',
            'string'   => 'E-Mail muss eine Zeichenkette sein.',
            'email'    => 'E-Mail muss eine gültige Adresse sein.',
            'max'      => 'E-Mail darf nicht länger als 200 Zeichen sein.',
            'unique'   => 'Diese E-Mail-Adresse ist bereits vergeben.',
        ],
        'password' => [
            'required'  => 'Passwort ist erforderlich.',
            'string'    => 'Passwort muss eine Zeichenkette sein.',
            'confirmed' => 'Passwörter stimmen nicht überein.',
            'min'       => 'Passwort muss mindestens 8 Zeichen lang sein.',
        ],
        'user_role' => [
            'required' => 'Benutzerrolle ist erforderlich.',
            'nullable' => 'Benutzerrolle kann null sein.',
            'string'   => 'Benutzerrolle muss eine Zeichenkette sein.',
            'exists'   => 'Die ausgewählte Rolle existiert nicht.',
        ],
    ],


    'attributes' => [],
];