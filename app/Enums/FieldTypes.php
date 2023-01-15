<?php

namespace App\Enums;

enum FieldTypes: string
{
    case text = 'text';
    case date = 'date';
    case number = 'number';
    case select = 'select';
}