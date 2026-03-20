<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Artist = 'artist';
    case Commissioner = 'commissioner';
}
