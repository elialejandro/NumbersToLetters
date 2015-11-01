<?php

use Rurounize\NumerosALetras;

/**
 * NumerosALetras test case.
 */
class NumerosALetrasTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var NumerosALetras
     */
    private $numerosALetras;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->numerosALetras = NumerosALetras::getInstance();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->numerosALetras = null;
        parent::tearDown();
    }

    /**
     * Tests NumerosALetras::getInstance()
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf("Rurounize\\NumerosALetras", $this->numerosALetras);
        $this->assertInstanceOf("Rurounize\\NumerosALetras", NumerosALetras::getInstance());
    }

    /**
     * Tests Decimales
     */
    public function testDecimales()
    {
        $this->numerosALetras->setDecimales(5);
        $decimales = $this->numerosALetras->getDecimales();
        $this->assertEquals(5, $decimales);
    }

    /**
     * Tests SeparadorDecimalSalida
     */
    public function testSeparadorDecimalSalida()
    {
        $this->numerosALetras->setSeparadorDecimalSalida("con");
        $separador = $this->numerosALetras->getSeparadorDecimalSalida();
        $this->assertEquals("con", $separador);
    }

    /**
     * Tests MascaraSalidaDecimal
     */
    public function testSetMascaraSalidaDecimal()
    {
        $this->numerosALetras->setMascaraSalidaDecimal("00/100 M.N.");
        $mascara = $this->numerosALetras->getMascaraSalidaDecimal();
        $this->assertEquals("00/100 M.N.", $mascara);
    }

    /**
     * Tests LetraCapital
     */
    public function testLetraCapital()
    {
        $letraCapital = $this->numerosALetras->getLetraCapital();
        $this->assertFalse($letraCapital);
        
        $this->numerosALetras->setLetraCapital(true);
        
        $letraCapital = $this->numerosALetras->getLetraCapital();
        $this->assertNotFalse($letraCapital);
    }

    /**
     * Tests ApocoparUnoParteDecimal()
     */
    public function testApocoparUnoParteDecimal()
    {
        $apocopar = $this->numerosALetras->getApocoparUnoParteDecimal();
        $this->assertFalse($apocopar);
    
        $this->numerosALetras->setApocoparUnoParteDecimal(true);
    
        $apocopar = $this->numerosALetras->getApocoparUnoParteDecimal();
        $this->assertNotFalse($apocopar);
    }
    
    /**
     * Tests ConvertirDecimales()
     */
    public function testConvertirDecimales()
    {
        $convertirDecimales = $this->numerosALetras->getConvertirDecimales();
        $this->assertFalse($convertirDecimales);
        
        $this->numerosALetras->setConvertirDecimales(true);
        
        $convertirDecimales = $this->numerosALetras->getConvertirDecimales();
        $this->assertNotFalse($convertirDecimales);
    }

    /**
     * Tests ApocoparUnoParteEntera()
     */
    public function testApocoparUnoParteEntera()
    {
        $apocopar = $this->numerosALetras->getApocoparUnoParteEntera();
        $this->assertFalse($apocopar);
        $this->numerosALetras->setApocoparUnoParteEntera(true);
        
        $apocopar = $this->numerosALetras->getApocoparUnoParteEntera();
        $this->assertNotFalse($apocopar);
    }

    /**
     * Tests convertirALetras()
     */
    public function testConvertirALetras()
    {
        $letras = $this->numerosALetras->convertirALetras(1);
        $this->assertEquals("Un con cero", $letras);
        
        $letras = $this->numerosALetras->convertirALetras(1045.87);
        $this->assertEquals("Un mil cuarenta y cinco con ochenta y siete", $letras);
        
        $this->numerosALetras->setConvertirDecimales(false);
        $letras = $this->numerosALetras->convertirALetras(1);
        $this->assertEquals("Un con 00/100 M.N.", $letras);
        
        $letras = $this->numerosALetras->convertirALetras(1045.87);
        $this->assertEquals("Un mil cuarenta y cinco con 87/100 M.N.", $letras);
        
        $this->numerosALetras->setConvertirDecimales(true)
                             ->setApocoparUnoParteEntera(false);
        $letras = $this->numerosALetras->convertirALetras(1);
        $this->assertEquals("Uno con cero", $letras);
    }
}

