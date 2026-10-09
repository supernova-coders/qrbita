<?php

namespace App;

enum InteractionType: string
{
    case Scan = 'scan';
    case View = 'view';
    case LinkClick = 'link_click';
    case ContactSave = 'contact_save';
}
