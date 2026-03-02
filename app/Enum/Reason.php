<?php

namespace App\Enums;

enum Reason: string
{
    case SALE = 'SALE';
    case RESTOCK = 'RESTOCK';
    case ADJUSTMENT = 'ADJUSTMENT';
    case RETURN = 'RETURN'; 
}
