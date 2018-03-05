<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5/12/16 9:27 PM
 */

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\TypeService;

class TestConfigExtraFields extends AbstractConfig
{
    protected $extraFieldsAllowed = true;

    public function getRules()
    {
        return [
            'name' => ['type' => TypeService::TYPE_ANY, 'required' => true],
        ];
    }
}
