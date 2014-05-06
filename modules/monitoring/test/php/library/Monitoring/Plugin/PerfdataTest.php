<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Tests\Icinga\Module\Monitoring\Plugin;

use Icinga\Test\BaseTestCase;
use Icinga\Module\Monitoring\Plugin\Perfdata;

class PerfdataTest extends BaseTestCase
{
    /**
     * @expectedException   Icinga\Exception\ProgrammingError
     */
    public function testWhetherFromStringThrowsExceptionWhenGivenAnEmptyString()
    {
        Perfdata::fromString('');
    }

    public function testWhetherGetValueReturnsValidValues()
    {
        $this->assertEquals(
            1337.0,
            Perfdata::fromString('1337')->getValue(),
            'Perfdata::getValue does not return correct values'
        );
        $this->assertEquals(
            1337.0,
            Perfdata::fromString('1337;;;;')->getValue(),
            'Perfdata::getValue does not return correct values'
        );
    }

    public function testWhetherDecimalValuesAreCorrectlyParsed()
    {
        $this->assertEquals(
            1337.5,
            Perfdata::fromString('1337.5')->getValue(),
            'Perfdata objects do not parse decimal values correctly'
        );
        $this->assertEquals(
            1337.5,
            Perfdata::fromString('1337.5B')->getValue(),
            'Perfdata objects do not parse decimal values correctly'
        );
    }

    public function testWhetherGetValueReturnsNullForInvalidOrUnknownValues()
    {
        $this->assertNull(
            Perfdata::fromString('U')->getValue(),
            'Perfdata::getValue does not return null for unknown values'
        );
        $this->assertNull(
            Perfdata::fromString('i am not a value')->getValue(),
            'Perfdata::getValue does not return null for invalid values'
        );
    }

    public function testWhetherGetWarningTresholdReturnsCorrectValues()
    {
        $this->assertEquals(
            '10',
            Perfdata::fromString('1;10')->getWarningTreshold(),
            'Perfdata::getWarningTreshold does not return correct values'
        );
        $this->assertEquals(
            '10:',
            Perfdata::fromString('1;10:')->getWarningTreshold(),
            'Perfdata::getWarningTreshold does not return correct values'
        );
        $this->assertEquals(
            '~:10',
            Perfdata::fromString('1;~:10')->getWarningTreshold(),
            'Perfdata::getWarningTreshold does not return correct values'
        );
        $this->assertEquals(
            '10:20',
            Perfdata::fromString('1;10:20')->getWarningTreshold(),
            'Perfdata::getWarningTreshold does not return correct values'
        );
        $this->assertEquals(
            '@10:20',
            Perfdata::fromString('1;@10:20')->getWarningTreshold(),
            'Perfdata::getWarningTreshold does not return correct values'
        );
    }

    public function testWhetherGetCriticalTresholdReturnsCorrectValues()
    {
        $this->assertEquals(
            '10',
            Perfdata::fromString('1;;10')->getCriticalTreshold(),
            'Perfdata::getCriticalTreshold does not return correct values'
        );
        $this->assertEquals(
            '10:',
            Perfdata::fromString('1;;10:')->getCriticalTreshold(),
            'Perfdata::getCriticalTreshold does not return correct values'
        );
        $this->assertEquals(
            '~:10',
            Perfdata::fromString('1;;~:10')->getCriticalTreshold(),
            'Perfdata::getCriticalTreshold does not return correct values'
        );
        $this->assertEquals(
            '10:20',
            Perfdata::fromString('1;;10:20')->getCriticalTreshold(),
            'Perfdata::getCriticalTreshold does not return correct values'
        );
        $this->assertEquals(
            '@10:20',
            Perfdata::fromString('1;;@10:20')->getCriticalTreshold(),
            'Perfdata::getCriticalTreshold does not return correct values'
        );
    }

    public function testWhetherGetMinimumValueReturnsCorrectValues()
    {
        $this->assertEquals(
            1337.0,
            Perfdata::fromString('1;;;1337')->getMinimumValue(),
            'Perfdata::getMinimumValue does not return correct values'
        );
        $this->assertEquals(
            1337.5,
            Perfdata::fromString('1;;;1337.5')->getMinimumValue(),
            'Perfdata::getMinimumValue does not return correct values'
        );
    }

    public function testWhetherGetMaximumValueReturnsCorrectValues()
    {
        $this->assertEquals(
            1337.0,
            Perfdata::fromString('1;;;;1337')->getMaximumValue(),
            'Perfdata::getMaximumValue does not return correct values'
        );
        $this->assertEquals(
            1337.5,
            Perfdata::fromString('1;;;;1337.5')->getMaximumValue(),
            'Perfdata::getMaximumValue does not return correct values'
        );
    }

    public function testWhetherMissingValuesAreReturnedAsNull()
    {
        $perfdata = Perfdata::fromString('1;;3;5');
        $this->assertNull(
            $perfdata->getWarningTreshold(),
            'Perfdata objects do not return null for missing warning tresholds'
        );
        $this->assertNull(
            $perfdata->getMaximumValue(),
            'Perfdata objects do not return null for missing maximum values'
        );
    }

    /**
     * @depends testWhetherGetValueReturnsValidValues
     */
    public function testWhetherValuesAreIdentifiedAsNumber()
    {
        $this->assertTrue(
            Perfdata::fromString('666')->isNumber(),
            'Perfdata objects do not identify ordinary digits as number'
        );
    }

    /**
     * @depends testWhetherGetValueReturnsValidValues
     */
    public function testWhetherValuesAreIdentifiedAsSeconds()
    {
        $this->assertTrue(
            Perfdata::fromString('666s')->isSeconds(),
            'Perfdata objects do not identify seconds as seconds'
        );
        $this->assertTrue(
            Perfdata::fromString('666us')->isSeconds(),
            'Perfdata objects do not identify microseconds as seconds'
        );
        $this->assertTrue(
            Perfdata::fromString('666ms')->isSeconds(),
            'Perfdata objects do not identify milliseconds as seconds'
        );
    }

    /**
     * @depends testWhetherGetValueReturnsValidValues
     */
    public function testWhetherValuesAreIdentifiedAsPercentage()
    {
        $this->assertTrue(
            Perfdata::fromString('66%')->isPercentage(),
            'Perfdata objects do not identify percentages as percentages'
        );
    }

    /**
     * @depends testWhetherValuesAreIdentifiedAsPercentage
     */
    public function testWhetherMinAndMaxAreNotRequiredIfUnitIsInPercent()
    {
        $perfdata = Perfdata::fromString('1%');
        $this->assertEquals(
            0.0,
            $perfdata->getMinimumValue(),
            'Perfdata objects do not set minimum value to 0 if UOM is %'
        );
        $this->assertEquals(
            100.0,
            $perfdata->getMaximumValue(),
            'Perfdata objects do not set maximum value to 100 if UOM is %'
        );
    }

    /**
     * @depends testWhetherGetValueReturnsValidValues
     */
    public function testWhetherValuesAreIdentifiedAsBytes()
    {
        $this->assertTrue(
            Perfdata::fromString('66666B')->isBytes(),
            'Perfdata objects do not identify bytes as bytes'
        );
        $this->assertTrue(
            Perfdata::fromString('6666KB')->isBytes(),
            'Perfdata objects do not identify kilobytes as bytes'
        );
        $this->assertTrue(
            Perfdata::fromString('666MB')->isBytes(),
            'Perfdata objects do not identify megabytes as bytes'
        );
        $this->assertTrue(
            Perfdata::fromString('66GB')->isBytes(),
            'Perfdata objects do not identify gigabytes as bytes'
        );
        $this->assertTrue(
            Perfdata::fromString('6TB')->isBytes(),
            'Perfdata objects do not identify terabytes as bytes'
        );
    }

    /**
     * @depends testWhetherGetValueReturnsValidValues
     */
    public function testWhetherValuesAreIdentifiedAsCounter()
    {
        $this->assertTrue(
            Perfdata::fromString('123c')->isCounter(),
            'Perfdata objects do not identify counters as counters'
        );
    }

    /**
     * @depends testWhetherValuesAreIdentifiedAsSeconds
     */
    public function testWhetherMicroSecondsAreCorrectlyConvertedToSeconds()
    {
        $this->assertEquals(
            666 / pow(10, 6),
            Perfdata::fromString('666us')->getValue(),
            'Perfdata objects do not correctly convert microseconds to seconds'
        );
    }

    /**
     * @depends testWhetherValuesAreIdentifiedAsSeconds
     */
    public function testWhetherMilliSecondsAreCorrectlyConvertedToSeconds()
    {
        $this->assertEquals(
            666 / pow(10, 3),
            Perfdata::fromString('666ms')->getValue(),
            'Perfdata objects do not correctly convert microseconds to seconds'
        );
    }

    /**
     * @depends testWhetherValuesAreIdentifiedAsPercentage
     */
    public function testWhetherPercentagesAreHandledCorrectly()
    {
        $this->assertEquals(
            66.0,
            Perfdata::fromString('66%')->getPercentage(),
            'Perfdata objects do not correctly handle native percentages'
        );
        $this->assertEquals(
            50.0,
            Perfdata::fromString('0;;;-250;250')->getPercentage(),
            'Perfdata objects do not correctly convert suitable values to percentages'
        );
        $this->assertNull(
            Perfdata::fromString('50')->getPercentage(),
            'Perfdata objects do return a percentage though their unit is not % and no maximum is given'
        );
        $this->assertNull(
            Perfdata::fromString('25;;;50;100')->getPercentage(),
            'Perfdata objects do return a percentage though their value is lower than it\'s allowed minimum'
        );
    }

    /**
     * @depends testWhetherValuesAreIdentifiedAsBytes
     */
    public function testWhetherKiloBytesAreCorrectlyConvertedToBytes()
    {
        $this->assertEquals(
            6666.0 * pow(2, 10),
            Perfdata::fromString('6666KB')->getValue(),
            'Perfdata objects do not corretly convert kilobytes to bytes'
        );
    }

    /**
     * @depends testWhetherValuesAreIdentifiedAsBytes
     */
    public function testWhetherMegaBytesAreCorrectlyConvertedToBytes()
    {
        $this->assertEquals(
            666.0 * pow(2, 20),
            Perfdata::fromString('666MB')->getValue(),
            'Perfdata objects do not corretly convert megabytes to bytes'
        );
    }

    /**
     * @depends testWhetherValuesAreIdentifiedAsBytes
     */
    public function testWhetherGigaBytesAreCorrectlyConvertedToBytes()
    {
        $this->assertEquals(
            66.0 * pow(2, 30),
            Perfdata::fromString('66GB')->getValue(),
            'Perfdata objects do not corretly convert gigabytes to bytes'
        );
    }

    /**
     * @depends testWhetherValuesAreIdentifiedAsBytes
     */
    public function testWhetherTeraBytesAreCorrectlyConvertedToBytes()
    {
        $this->assertEquals(
            6.0 * pow(2, 40),
            Perfdata::fromString('6TB')->getValue(),
            'Perfdata objects do not corretly convert terabytes to bytes'
        );
    }
}
