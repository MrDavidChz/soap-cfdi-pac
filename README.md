# Clase para trabajar con el WSDL de FACTURA INTELIGENTE y generar un CFDI ya timbrado

## Instalación

```
composer require mrdavidchz/soap-pac-cfdi
```
## Uso
- Especificamos las credenciales para logearnos en el WS de FACTURA INTELIGENTE
```
$username   = 'CFDI010233D33';
$password   = 'contRa$3na';
$xml        = file_get_contents('XML_03052018.xml')
$referencia = 'prueba';
$test       = true;
/**
 * $username    Proporcionado por el PAC
 * $password    Proporcionado por el PAC
 * $xml         Generado Previamente con toda la Esctructura de un CFDI 3.3
 * $referencia  Referencia para efectos de control
 * true         Para Endpoint de Pruebas Cambiar a false
 * @type {PAC}
 */
$pac = new PAC($username, $password, $xml, $referencia , $test);

- Ejemplo para validar si las credenciales y XML fueron validas.
``
if ($pac->response()) {
   //Metodo para guardar el XML
   $pac->save('./timbrado_exitosooo.xml');
   //Metodo para imprimir la información del timbrado
   print_r( $pac->getInfoTimbre() );
} else {
  //Metodo para imprimir los errores Recibidos.
  print_r( $pac->errorMessage() );

}
```
