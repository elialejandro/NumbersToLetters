<?php
namespace Rurounize;

/**
 * Esta clase ayuda a convertir números a letras
 * 
 * Clase portada de una libreria en C# se puede encontrar en:
 * @link https://varionet.wordpress.com/2007/11/29/convertir-numeros-a-letras/
 * @author ISC. Eli Alejandro Moreno López <iscelialejandro@gmail.com>
 */
class NumerosALetras
{
    // Miembros estáticos
    const UNI = 0;
    const DIECI = 1;
    const DECENA = 2;
    const CENTENA = 3;
    
    /**
     * 
     * @var NumerosALetras
     */
    private static $instance;
    
    private static $matriz = array(
        array(null," uno", " dos", " tres", " cuatro", " cinco", " seis", " siete", " ocho", " nueve"),
        array(" diez"," once"," doce"," trece"," catorce"," quince"," dieciséis"," diecisiete"," dieciocho"," diecinueve"),
        array(null,null,null," treinta"," cuarenta"," cincuenta"," sesenta"," setenta"," ochenta"," noventa"),
        array(null,null,null,null,null," quinientos",null," setecientos",null," novecientos"),
    );

    // Caracter SUB
    private $SUB = null;

    // Cambiar acá si se quiere otro comportamiento en los métodos de clase
    const SeparadorDecimalSalidaDefault = "con";
    const MascaraSalidaDecimalDefault = "00'/100.-'";
    const DecimalesDefault = 2;
    const LetraCapitalDefault = false;
    const ConvertirDecimalesDefault = false;
    const ApocoparUnoParteEnteraDefault = false;
    const ApocoparUnoParteDecimalDefault = false;

    #region Propiedades

    private $decimales = self::DecimalesDefault;
    private $separadorDecimalSalida = self::SeparadorDecimalSalidaDefault;
    private $posiciones = self::DecimalesDefault;
    private $mascaraSalidaDecimal = self::MascaraSalidaDecimalDefault;
    private $mascaraSalidaDecimalInterna = self::MascaraSalidaDecimalDefault;
    private $esMascaraNumerica = true;
    private $letraCapital = self::LetraCapitalDefault;
    private $convertirDecimales = self::ConvertirDecimalesDefault;
    private $apocoparUnoParteEntera = self::ApocoparUnoParteEnteraDefault;
    private $apocoparUnoParteDecimal = self::ApocoparUnoParteDecimalDefault;

    private function __construct()
    {
        $this->SUB = chr(26);
        $this->setMascaraSalidaDecimal(self::MascaraSalidaDecimalDefault);
        $this->setSeparadorDecimalSalida(self::SeparadorDecimalSalidaDefault);
        $this->setLetraCapital(self::LetraCapitalDefault);
        $this->setConvertirDecimales($this->convertirDecimales);
    }
    
    public static function getInstance()
    {
        if(self::$instance == null) {
            self::$instance = new NumerosALetras();
        }
        
        return self::$instance;
    }
    
    /** 
     * Indica la cantidad de decimales que se pasarán a entero para la conversión
     * Esta propiedad cambia al cambiar MascaraDecimal por un valor que empieze con '0'
     * 
     * @param string $value
     * @return self
     */
    public function setDecimales($value)
    {
        if ($value > 10) {
            throw new \Exception($value + " excede el número máximo de decimales admitidos, solo se admiten hasta 10.");
        }
        $this->decimales = $value;
        
        return $this;
    }
    
    /**
     * Retorna la cantidad de decimales que se pasarán a entero para la conversión
     */
    public function getDecimales()
    {
        return $this->decimales;
    }

    /**
     * Indica la cadena a intercalar entre la parte entera y la decimal del número
     * 
     * @param string $value
     * @return self 
     */
    public function setSeparadorDecimalSalida($value)
    {
        $this->separadorDecimalSalida = $value;
        //Si el separador decimal es compuesto, infiero que estoy cuantificando algo,
        //por lo que apocopo el "uno" convirtiéndolo en "un"
        if (strpos(trim($value), " ") > 0) {
            $this->apocoparUnoParteEntera = true;
        }
        else {
            $this->apocoparUnoParteEntera = false;
        }
        
        return $this;
    }
    
    /**
     * Retorna la cadena a intercalar entre la parte entera y la decimal del número
     *
     * @return string
     */
    public function getSeparadorDecimalSalida()
    {
        return $this->separadorDecimalSalida;
    }

    /**
     * Indica el formato que se le dara a la parte decimal del número
     * 
     * @param string $value
     * @return self 
     */
    public function setMascaraSalidaDecimal($value)
    {
        //Determina la cantidad de cifras a redondear a partir de la cantidad de '0' o '#' 
        //que haya al principio de la cadena, y también si es una máscara numérica
        $i = 0;
        while ($i < strlen($value) 
                && ($value[$i] == '0')
                || $value[$i] == '#') {
            $i++;
        }
        $this->posiciones = $i;
        if ($i > 0)
        {
            $this->decimales = $i;
            $this->esMascaraNumerica = true;
        } else { 
            $this->esMascaraNumerica = false;
        }
        
        $this->mascaraSalidaDecimal = $value;
        
        if ($this->esMascaraNumerica) {
            $this->mascaraSalidaDecimalInterna = "%s";
            $temp = str_replace("''", $this->SUB, substr($value, $this->posiciones));
            $temp = str_replace("'", "", $temp);
            $temp = str_replace($this->SUB, "'", $temp);
            $this->mascaraSalidaDecimalInterna .= $temp;
        } else {
            $temp = str_replace("''", $this->SUB, substr($value, $this->posiciones));
            $temp = str_replace("'", "", $temp);
            $temp = str_replace($this->SUB, "'", $temp);
            $this->mascaraSalidaDecimalInterna = $temp;
        }
        
        return $this;
    }
    
    /**
     * Retorna el formato que se le dara a la parte decimal del número
     * 
     * @return string
     */
    public function getMascaraSalidaDecimal()
    {
        if (!empty($this->mascaraSalidaDecimal)) {
            return $this->mascaraSalidaDecimal;
        }
        else { 
            return "";
        }
    }

    /**
     * Indica si la primera letra del resultado debe estár en mayúscula
     * 
     * @param bool $value
     * @return self
     */
    public function setLetraCapital($value)
    {
        $this->letraCapital = $value;
        
        return $this;
    }
    
    /**
     * Retorna si la primera letra del resultado debe estár en mayúscula
     * 
     * @return bool
     */
    public function getLetraCapital()
    {
        return $this->letraCapital;
    }

    /**
     * Indica si se deben convertir los decimales a su expresión nominal
     * 
     * @param bool $value
     * @return self
     */
    public function setConvertirDecimales($value)
    {
        $this->convertirDecimales = $value;
        $this->apocoparUnoParteDecimal = $value;
        if ($value)
        {// Si la máscara es la default, la borro
            if ($this->getMascaraSalidaDecimal() == self::MascaraSalidaDecimalDefault)
                $this->setMascaraSalidaDecimal("");
        }
        else if (empty($this->mascaraSalidaDecimal)) {
            //Si no hay máscara dejo la default
            $this->setMascaraSalidaDecimal(self::MascaraSalidaDecimalDefault);
        }
        
        return $this;
    }
    
    /**
     * Retorna si se deben convertir los decimales a su expresión nominal
     * 
     * @return bool
     */
    public function getConvertirDecimales()
    {
        return $this->convertirDecimales;
    }

    /**
     * Indica si de debe cambiar "uno" por "un" en las unidades.
     * 
     * @param bool $value
     * @return self
     */
    public function setApocoparUnoParteEntera($value)
    {
        $this->apocoparUnoParteEntera = $value;
        
        return $this;
    }
    
    /**
     * Retorna si de debe cambiar "uno" por "un" en las unidades.
     * 
     * @return bool
     */
    public function getApocoparUnoParteEntera()
    {
        return $this->apocoparUnoParteEntera;
    }

    /**
     * Determina si se debe apococopar el "uno" en la parte decimal
     * El valor de esta propiedad cambia al setear ConvertirDecimales
     * 
     * @param bool $value
     * @return self
     */
    public function setApocoparUnoParteDecimal($value)
    {
        $this->apocoparUnoParteDecimal = $value;
        return $this;
    }
    
    /**
     * Retorna si se debe apococopar el "uno" en la parte decimal
     * 
     * @return bool
     */
    public function getApocoparUnoParteDecimal()
    {
        return $this->apocoparUnoParteDecimal;
    }

    /**
     * Obtiene la representación en letras del número
     *  
     * @param int|double|string $numero
     * @return string 
     */
    public function convertirALetras($numero)
    { 
        return self::convertir($numero, $this->decimales, $this->separadorDecimalSalida, $this->mascaraSalidaDecimalInterna, $this->esMascaraNumerica, $this->letraCapital, $this->convertirDecimales, $this->apocoparUnoParteEntera, $this->apocoparUnoParteDecimal); 
    }

    private static function convertir($numero, $decimales, $separadorDecimalSalida, $mascaraSalidaDecimal, $esMascaraNumerica, $letraCapital, $convertirDecimales, $apocoparUnoParteEntera, $apocoparUnoParteDecimal)
    {
        $terna = 0; 
        $centenaTerna = 0;
        $decenaTerna = 0; 
        $unidadTerna = 0;
        $iTerna = 0;
        $cadTerna = "";
        
        $resultado = "";
        $num = abs($numero);

        if ($num >= 1000000000000 || $num < 0) {
            throw new \Exception("El número '" + $numero + "' excedió los límites del conversor: [0;1,000,000,000,000)");
        }
        if ($num == 0)
            $resultado = " cero";
        else
        {
            $iTerna = 0;
            while ($num > 0)
            {
                $iTerna++;
                $cadTerna = "";
                $terna = (int) ($num % 1000);

                $centenaTerna = (int)($terna / 100);
                $decenaTerna = $terna % 100;
                $unidadTerna = $terna % 10;

                if (($decenaTerna > 0) && ($decenaTerna < 10)) {
                    $cadTerna = self::$matriz[self::UNI][$unidadTerna] . $cadTerna;
                } else if (($decenaTerna >= 10) && ($decenaTerna < 20)) {
                    $cadTerna = $cadTerna . self::$matriz[self::DIECI][$unidadTerna];
                } else if ($decenaTerna == 20) {
                    $cadTerna = $cadTerna . " veinte";
                } else if (($decenaTerna > 20) && ($decenaTerna < 30))
                    $cadTerna = " veinti" . substr(self::$matriz[self::UNI][$unidadTerna], 1);
                else if (($decenaTerna >= 30) && ($decenaTerna < 100)) {
                    if ($unidadTerna != 0) {
                        $cadTerna = self::$matriz[self::DECENA][(int)($decenaTerna / 10)] . " y" . self::$matriz[self::UNI][$unidadTerna] . $cadTerna;
                    } else {
                        $cadTerna .= self::$matriz[self::DECENA][(int)($decenaTerna / 10)];
                    }
                }
                
                switch ($centenaTerna)
                {
                    case 1:
                        if ($decenaTerna > 0) {
                            $cadTerna = " ciento" . $cadTerna;
                        } else {
                            $cadTerna = " cien" . $cadTerna;
                        }
                        break;
                    case 5:
                    case 7:
                    case 9:
                        $cadTerna = self::$matriz[self::CENTENA][(int)($terna / 100)] . $cadTerna;
                        break;
                    default:
                        if ((int)($terna / 100) > 1) {
                            $cadTerna = self::$matriz[self::UNI][(int)($terna / 100)] . "cientos" . $cadTerna;
                        }
                        break;
                }
                
                // Reemplazo el 'uno' por 'un' si no es en las únidades o si se solicító apocopar
                if (($iTerna > 1 || $apocoparUnoParteEntera) && $decenaTerna == 21) {
                    $cadTerna = str_replace("veintiuno", "veintiún", $cadTerna);
                } else if (($iTerna > 1 || $apocoparUnoParteEntera) && $unidadTerna == 1 && $decenaTerna != 11) {
                    $cadTerna = substr($cadTerna, 0, strlen($cadTerna) - 1);
                    //Acentúo 'veintidós', 'veintitrés' y 'veintiséis'
                } else if ($decenaTerna == 22) {
                    $cadTerna = str_replace("veintidos", "veintidós", $cadTerna);
                } else if ($decenaTerna == 23) {
                    $cadTerna = str_replace("veintitres", "veintitrés", $cadTerna);
                } else if ($decenaTerna == 26) {
                    $cadTerna = str_replace("veintiseis", "veintiséis", $cadTerna);
                }

                //Completo miles y millones
                switch ($iTerna)
                {
                    case 3:
                        if ($numero < 2000000) {
                            $cadTerna .= " millón";
                        } else {
                            $cadTerna .= " millones";
                        }
                        break;
                    case 2:
                    case 4:
                        if ($terna > 0) { 
                            $cadTerna .= " mil";
                        }
                        break;
                }
                $resultado = $cadTerna . $resultado;
                $num = (int)($num / 1000);
            } //while
        }
		
        //Se agregan los decimales si corresponde
        if ($decimales > 0)
        {
            $resultado .= " " . $separadorDecimalSalida . " ";
            $EnteroDecimal = (int) round(($numero - (int)$numero) * pow(10, $decimales), 0);
            if ($convertirDecimales)
            {
                $esMascaraDecimalDefault = $mascaraSalidaDecimal == self::MascaraSalidaDecimalDefault;
                $resultado .= (self::convertir($EnteroDecimal, 0, null, null, $esMascaraNumerica, false, false, ($apocoparUnoParteDecimal && !$esMascaraNumerica/*&& !esMascaraDecimalDefault*/), false) 
                           . " " . ($esMascaraNumerica ? "" : $mascaraSalidaDecimal));
            }
            else if ($esMascaraNumerica) {
                $EnteroDecimal = ($EnteroDecimal == 0) ? $EnteroDecimal = "00": $EnteroDecimal;
                $resultado .= sprintf($mascaraSalidaDecimal, $EnteroDecimal);
            } else {
                $resultado .= $EnteroDecimal . " " . $mascaraSalidaDecimal;
            }
        }
        
        $resultado = trim($resultado);
        //Se pone la primer letra en mayúscula si corresponde y se retorna el resultado
        if ($letraCapital) {
            return ucfirst($resultado);
        } else {
            return $resultado;
        }
    }
}
