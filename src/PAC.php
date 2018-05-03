<?php

/*
 * This file is part of the CFDI-PAC-FACTURA INTELIGENTE MX.
 *
 * (c) MrDavidChz
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MrDavidChz\SoapPacCfdi;

use Exception;
use DOMDocument;
use SoapClient;
class PAC
{


    /**
     * Client username.
     *
     * @var string
     */
    protected $username;

    /**
     * Client password.
     *
     * @var string
     */
    protected $password;

    /**
     * CFDI PRE-XML.
     *
     * @var string
     */
    protected $xml;

    /**
     * 
     * @var string
     */
    protected $reference;

    /**
     * Endpoint Factura Inteligente
     *
     * @var string
     */
    const WSDL_ENDPOINT_TEST = 'https://www.appfacturainteligente.com/WSTimbrado33Test/WSCFDI33.svc?WSDL'; 
    const WSDL_ENDPOINT_PRODUCTION ='https://www.appfacturainteligente.com/WSTimbrado33/WSCFDI33.svc?WSDL';



    protected $client;

    /**
     * Node Principal SOAP Result
     * @var [type]
     */
    protected $CFDIResult;
    /**
     * EndPoint Testing
     * @var [type]
     */
    protected $test;

    /**
     * Soap Cliente Options
     * @var [type]
     */
    protected $options = [
        'soap_version' => SOAP_1_1
    ];

    /**
     * Create a new pac instance.
     *
     * @param string $username
     * @param string $password
     * @param string $xml
     * @param string $reference
     * @param bool $test
     * @param bool $CFDIResult
     */
    public function __construct($username,$password,$xml,$reference,$test, $CFDIResult = 'TimbrarCFDIResult' )
    {
        $this->username   = $username;
        $this->password   = $password;
        $this->xml        = $xml;
        $this->reference  = $reference;
        $this->test       = $test;
        $this->CFDIResult = $CFDIResult;

        $this->client     = $this->sendXML();

    }


    /**
     * [sendXML sending request to the WSDL endpoint]
     * @return [type] [instance]
     */
    protected function sendXML(){
        $endpoint = $this->wsdl_edpoint();
        $client   = new SoapClient($this->wsdl_edpoint(), $this->options);

            return $client->TimbrarCFDI([
                'usuario'    => $this->username,
                'password'   => $this->password,
                'cadenaXML'  => $this->xml,
                'referencia' => $this->reference
            ]);


    }

    /**
     * [wsdl_edpoint description]
     * @return [type] [description]
     */
    protected function wsdl_edpoint(){
        if ($this->test) {
            return static::WSDL_ENDPOINT_TEST;
        } else {
            return static::WSDL_ENDPOINT_PRODUCTION;
        }
    }
    /**
     * [response get response of WSDL]
     * @param  string $property [node SOAP]
     * @return [type]           [boolean]
     */
    public function response()
    {
        $request               = $this->client;
        $OperacionExitosa      = $request->{$this->CFDIResult}->OperacionExitosa;
        $MensajeErrorDetallado = $request->{$this->CFDIResult}->MensajeErrorDetallado;

        if ($OperacionExitosa && is_null($MensajeErrorDetallado)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [getInfoTimbre get info. Timbre]
     * @param  string $property [node SOAP]
     * @return [type]           [array]
     */
    public function getInfoTimbre($property='Timbre')
    {
        $request = $this->client->{$this->CFDIResult};
        
        $data    = ((array) $request->{$property});
        return $data;
    }

    /**
     * [getXML get XML]
     * @param  string $property [node SOAP]
     * @return [type]           [STRING xml]
     */
    public function getXML($property='XMLResultado'){
        $request    = $this->client->{$this->CFDIResult};
        return $request->{$property};
    }

    /**
     * [save xml to file]
     * @param  [type] $filename [FILENAME TO SAVE]
     */
    public function save($filename)
    {
        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = true;
        $xml->formatOutput = true;
        $xml->loadXML($this->getXML());

        return $xml->save($filename);
    }

    /**
     * [errorMessage Get mesage error soap]
     * @return [type] [array]
     */
    public function errorMessage(){
        $request = $this->client->{$this->CFDIResult};
        $data = [
           'CodigoRespuesta'       =>  $request->CodigoRespuesta,
           'MensajeError'          =>  $request->MensajeError,
           'MensajeErrorDetallado' =>  $request->MensajeErrorDetallado
        ];
        return $data;
    }

}
