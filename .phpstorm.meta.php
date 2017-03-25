<?php

use PHPUnit\Framework\TestCase;

namespace PHPSTORM_META {
    $STATIC_METHOD_TYPES = [
        TestCase::createConfiguredMock('') => [
            '' == '@|PHPUnit_Framework_MockObject_MockObject',
        ],
        TestCase::createMock('') => [
            '' == '@|PHPUnit_Framework_MockObject_MockObject',
        ],
        TestCase::createPartialMock('') => [
            '' == '@|PHPUnit_Framework_MockObject_MockObject',
        ],
        TestCase::getMock('') => [
            '' == '@|PHPUnit_Framework_MockObject_MockObject',
        ],
        TestCase::getMockForAbstractClass('') => [
            '' == '@|PHPUnit_Framework_MockObject_MockObject',
        ],
    ];
}
