<?php

use PHPUnit\Framework\TestCase;

namespace PHPSTORM_META {
    override(TestCase::createConfiguredMock(0), map([
        '' => '@|PHPUnit_Framework_MockObject_MockObject',
    ]));
    override(TestCase::createMock(0), map([
        '' => '@|PHPUnit_Framework_MockObject_MockObject',
    ]));
    override(TestCase::createPartialMock(0), map([
        '' => '@|PHPUnit_Framework_MockObject_MockObject',
    ]));
    override(TestCase::getMock(0), map([
        '' => '@|PHPUnit_Framework_MockObject_MockObject',
    ]));
    override(TestCase::getMockForAbstractClass(0), map([
        '' => '@|PHPUnit_Framework_MockObject_MockObject',
    ]));
}
