<?php

namespace Infortis\Base\Model\System\Config\Source\Category;

class Altimagecolumn
{
    const PROPERTY_ROLE = 'role';

    const PROPERTY_POSITION = 'position';

    const PROPERTY_LABEL = 'label';

    public function toOptionArray()
    {
        return [
            ['value' => self::PROPERTY_ROLE,              'label' => __('Role')],
            ['value' => self::PROPERTY_POSITION,          'label' => __('Sort Order')],
            ['value' => self::PROPERTY_LABEL,             'label' => __('Label (Alt Text)')], // Deprecated
        ];
    }
}
