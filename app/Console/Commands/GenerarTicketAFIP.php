<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SimpleXMLElement;
use SoapClient;
use SoapFault;

class GenerarTicketAFIP extends Command
{
    protected $signature = 'afip:generar-ticket {service}';
    protected $description = 'Genera un Ticket de Acceso (TA) para el servicio de AFIP.';

    public function handle()
    {
        $SERVICE = $this->argument('service');

        //Constantes
        define("WSDL", storage_path('app/afip/wsaa.wsdl')); // Ruta al WSDL
        define("CERT", storage_path('app/afip/ghf.crt'));   // Ruta al certificado
        define("PRIVATEKEY", storage_path('app/afip/ghf.key')); // Ruta a la clave privada
        define("PASSPHRASE", "xxxxx"); // Frase de contraseña
        define("URL", "https://wsaahomo.afip.gov.ar/ws/services/LoginCms"); // URL de WSAA

        $this->CreateTRA($SERVICE);
        $CMS = $this->SignTRA();
        $TA = $this->CallWSAA($CMS);

        if (file_put_contents(storage_path('app/afip/TA.xml'), $TA)) {
            $this->info('Ticket de Acceso (TA) generado correctamente.');
        } else {
            $this->error('Error al guardar el Ticket de Acceso (TA).');
        }
    }

    // Función para crear el TRA
    protected function CreateTRA($SERVICE)
    {
        $TRA = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0">' .
            '</loginTicketRequest>'
        );
        $TRA->addChild('header');
        $TRA->header->addChild('uniqueId', date('U'));
        $TRA->header->addChild('generationTime', date('c', date('U') - 60));
        $TRA->header->addChild('expirationTime', date('c', date('U') + 60));
        $TRA->addChild('service', $SERVICE);
        $TRA->asXML(storage_path('app/afip/TRA.xml'));
    }

    // Función para firmar el TRA
    protected function SignTRA()
    {
        $STATUS = openssl_pkcs7_sign(
            storage_path('app/afip/TRA.xml'),
            storage_path('app/afip/TRA.tmp'),
            "file://" . CERT,
            array("file://" . PRIVATEKEY, PASSPHRASE),
            array(),
            !PKCS7_DETACHED
        );
        if (!$STATUS) {
            $this->error('ERROR generating PKCS#7 signature');
            return;
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
        if (file_exists(storage_path('app/afip/TRA.tmp'))) {
            unlink(storage_path('app/afip/TRA.tmp'));
        }
        return $CMS;
    }

    // Función para llamar al WSAA
    protected function CallWSAA($CMS)
    {
        try {
            $client = new SoapClient(WSDL, array(
                'soap_version' => SOAP_1_2,
                'location' => URL,
                'trace' => 1,
                'exceptions' => 1
            ));
            $results = $client->loginCms(array('in0' => $CMS));
            file_put_contents(storage_path('app/afip/request-loginCms.xml'), $client->__getLastRequest());
            file_put_contents(storage_path('app/afip/response-loginCms.xml'), $client->__getLastResponse());
            return $results->loginCmsReturn;
        } catch (SoapFault $e) {
            $this->error("SOAP Fault: " . $e->getMessage());
            $this->error("Request: " . $client->__getLastRequest());
            $this->error("Response: " . $client->__getLastResponse());
            return;
        }
    }
}
