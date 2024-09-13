<?php

namespace App\Utils;

class CertificadoUtil
{
    public static function getCertificateValidity($certificateFile, $password)
    {
        // Verifica a validade do certificado antes de fazer o upload
        try {

            // Lê o conteúdo do arquivo do certificado sem armazená-lo
            $certificateContent = file_get_contents($certificateFile->getRealPath());

            $certs = [];

            // Usamos a função openssl_pkcs12_read para ler o conteúdo
            if (openssl_pkcs12_read($certificateContent, $certs, $password)) {

                // Extraímos o certificado e o analisamos
                $x509cert = $certs['cert']; // Certificado em formato x509

                // Parseamos o certificado para obter a data de validade
                $certInfo = openssl_x509_parse($x509cert);

                if ($certInfo) {
                    $validToTimestamp = $certInfo['validTo_time_t'];
                    $validToDate = date('Y-m-d H:i:s', $validToTimestamp);
                    $type = $certInfo['subject']['OU'][3];
                    $commonName = $certInfo['subject']['CN'];

                    // Verifica se o certificado está expirado
                    if (strtotime($validToDate) < time()) {
                        return response()->json([
                            'error' => 'O certificado está expirado. Data de validade: ' . date('d/m/Y H:i:s' ,strtotime($validToDate))
                        ], 400);
                    }

                    return response()->json([
                        'message' => 'Seu certificado foi salvo com sucesso!',
                        'data_validade' => $validToDate,
                        'tipo' => $type,
                        'nome_cert' => $commonName
                    ]);
                } else {
                    throw new \Exception('Falha ao ler o certificado.');
                }
            } else {
                throw new \Exception('Não foi possível ler o certificado. Verifique se a senha está correta.');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
