<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use SoapClient;
use SoapFault;

class TokenService
{
    protected $taPath;
    protected $wsaaWsdl;
    protected $wsaaUrl;

    public function __construct()
    {
        $this->taPath = storage_path('app/afip/TA.xml');
        $this->wsaaWsdl = storage_path('app/afip/wsaa.wsdl');
        $this->wsaaUrl = 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms';
    }

    public function getTokenAndSign()
    {
        if (!file_exists($this->taPath) || $this->taExpirado()) {
            $this->generarNuevoTA();
        }

        $ta = simplexml_load_file($this->taPath);
        if ($ta === false) {
            throw new \Exception("No se pudo leer el archivo TA.xml.");
        }

        return [
            'token' => (string)$ta->credentials->token,
            'sign' => (string)$ta->credentials->sign,
        ];
    }

    protected function taExpirado()
    {
        if (!file_exists($this->taPath)) {
            return true;
        }

        $ta = simplexml_load_file($this->taPath);
        $expirationTime = (string) $ta->header->expirationTime;
        return strtotime($expirationTime) < time();
    }

    public function generarNuevoTA($service = 'wsfe')
    {
        $this->verificarArchivosNecesarios();
        $this->info("El TA ha expirado. Generando uno nuevo...");

        $this->createTRA($service);
        $CMS = $this->signTRA();
        $TA = $this->callWSAA($CMS);

        if (file_put_contents($this->taPath, $TA)) {
            $this->info("Nuevo TA generado correctamente.");
        } else {
            throw new \Exception("Error al guardar el nuevo TA.");
        }
    }

    protected function verificarArchivosNecesarios()
    {
        if (!file_exists($this->wsaaWsdl)) {
            throw new \Exception("El archivo wsaa.wsdl no existe.");
        }
        if (!file_exists(storage_path('app/afip/ghf.crt'))) {
            throw new \Exception("El archivo ghf.crt no existe.");
        }
        if (!file_exists(storage_path('app/afip/ghf.key'))) {
            throw new \Exception("El archivo ghf.key no existe.");
        }
    }

    protected function createTRA($service)
    {
        $TRA = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0">' .
            '</loginTicketRequest>'
        );
        $TRA->addChild('header');
        $TRA->header->addChild('uniqueId', date('U'));
        $TRA->header->addChild('generationTime', date('c', date('U') - 60));
        $TRA->header->addChild('expirationTime', date('c', date('U') + 3600));
        $TRA->addChild('service', $service);
        $TRA->asXML(storage_path('app/afip/TRA.xml'));
    }

    protected function signTRA()
    {
        $STATUS = openssl_pkcs7_sign(
            storage_path('app/afip/TRA.xml'),
            storage_path('app/afip/TRA.tmp'),
            "file://" . storage_path('app/afip/ghf.crt'),
            array("file://" . storage_path('app/afip/ghf.key'), 'xxxxx'),
            array(),
            !PKCS7_DETACHED
        );
        if (!$STATUS) {
            throw new \Exception("ERROR generando la firma PKCS#7.");
        }
        $inf = fopen(storage_path('app/afip/TRA.tmp'), "r");
        $i = 0;
        $CMS = "";
        while (!feof($inf)) {
            $buffer = fgets($inf);
            if ($i++ >= 4) {
                $CMS .= $buffer;
            }
        }
        fclose($inf);
        unlink(storage_path('app/afip/TRA.tmp'));
        return $CMS;
    }

    protected function callWSAA($CMS)
    {
        $client = new SoapClient($this->wsaaWsdl, [
            'soap_version' => SOAP_1_2,
            'location' => $this->wsaaUrl,
            'trace' => 1,
            'exceptions' => 1,
        ]);

        $results = $client->loginCms(['in0' => $CMS]);
        if (is_soap_fault($results)) {
            throw new \Exception("SOAP Fault: " . $results->faultcode . "\n" . $results->faultstring);
        }
        return $results->loginCmsReturn;
    }

    protected function info($message)
    {
        Log::info("[TokenService] " . $message);
    }
}
