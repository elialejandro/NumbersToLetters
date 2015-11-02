[![Build Status](https://travis-ci.org/elialejandro/NumerosALetras.svg?branch=master)](https://travis-ci.org/elialejandro/NumerosALetras)
# NumerosALetras
Es una librería que ayuda a convertir números a letras en formato moneda

### Ejemplo
1905.87 => Un mil novecientos cinco con 87/100 .-

## Uso

Convertir a letras con un formato de moneda especifico

```php
use Rurounize\NumerosALetras;

$numLetras = NumerosALetras::getInstance();
$numLetras->setMascaraSalidaDecimal("00/100 M.N.")
          ->setSeparadorDecimalSalida("pesos")
          ->setApocoparUnoParteEntera(true)
          ->setLetraCapital(true)
$letras = $numLetras->convertiALetras(1045.87);
// Un mil cuarenta y cinco con 87/100 M.N.
```
