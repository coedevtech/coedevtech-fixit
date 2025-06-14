<?php

namespace Fixit\Enum;

enum ErrorStatus: string
{
    case FIXED = 'fixed';
    case NOT_FIXED = 'not_fixed';
}
