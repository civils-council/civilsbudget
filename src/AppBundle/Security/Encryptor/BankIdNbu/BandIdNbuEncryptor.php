<?php

namespace AppBundle\Security\Encryptor\BankIdNbu;


class BandIdNbuEncryptor
{
    const
        EM_RESULT_OK = 0,
        EM_RESULT_ERROR = 1,
        EM_RESULT_ERROR_WRONG_PARAMS = 2,
        EM_RESULT_ERROR_INITIALIZED = 3,

        EU_ERROR_UNKNOWN = 0xFFFF,
        EU_ERROR_NOT_INITIALIZED = 0x0001,
        EU_ERROR_BAD_PARAMETER = 0x0002,
        EU_ERROR_LIBRARY_LOAD = 0x0003,

        EM_ENCODING_CP1251 = 1251,
        EM_ENCODING_UTF8 = 65001
    ;

    const EUSPE_ENCODING = self::EM_ENCODING_UTF8;

    private $privateKeyFilePath;
    private $privateKeyPassword;

    public function __construct(string $privateKeyFilePath, string $privateKeyPassword)
    {
        $this->privateKeyFilePath = $privateKeyFilePath;
        $this->privateKeyPassword = $privateKeyPassword;
    }

    /**
     * @param array $encodedResponse
     * @return array
     *
     * @throws \Exception
     */
    public function decode(array $encodedResponse): array
    {
        $error = null;
        $envelopInfo = null;
        $signInfo = null;

        $result = $this->decodeData(
            $encodedResponse['customerCrypto'],
            $encodedResponse['cert'],
            $error,
            $envelopInfo,
            $signInfo
        );

        if (!$result) {
            throw new \Exception('Виникла помилка при розшифруванні відповіді BankID НБУ: ' . $error->description);
        }

        return json_decode($signInfo->data, true);
    }

    private function decodeData(
        $customerCrypto,
        $senderCert,
        &$error,
        &$envelopInfo,
        &$signInfo)
    {
        $errorCode = self::EU_ERROR_UNKNOWN;
        $context = null;
        $pkContext = null;

        $error = null;
        $envelopInfo = new EUEnvelopInfo();
        $signInfo = new EUSignInfo();

        euspe_setcharset(self::EUSPE_ENCODING);

        $result = euspe_init(
            $errorCode
        );

        if ($result != self::EM_RESULT_OK)
        {
            $error = $this->createError(
                'Виникла помилка при ініціалізації криптографічної бібліотеки',
                $result,
                $errorCode
            );

            return false;
        }

        $result = euspe_ctxcreate(
            $context,
            $errorCode
        );
        if ($result != self::EM_RESULT_OK)
        {
            $error = $this->createError(
                'Виникла помилка при створенні контексту',
                $result,
                $errorCode
            );

            return false;
        }

        $result = euspe_ctxreadprivatekeyfile(
            $context,
            $this->privateKeyFilePath,
            $this->privateKeyPassword,
            $pkContext,
            $errorCode
        );

        if ($result != self::EM_RESULT_OK)
        {
            euspe_ctxfree($context);

            $error = $this->createError(
                'Виникла помилка при зчитуванні ос. ключа з файлу',
                $result,
                $errorCode
            );

            return false;
        }


        $result = euspe_ctxdevelopdata(
            $pkContext,
            base64_decode($customerCrypto),
            base64_decode($senderCert),
            $envelopInfo->data,
            $envelopInfo->signTime,
            $envelopInfo->useTSP,
            $envelopInfo->senderInfo->issuer,
            $envelopInfo->senderInfo->issuerCN,
            $envelopInfo->senderInfo->serial,
            $envelopInfo->senderInfo->subject,
            $envelopInfo->senderInfo->subjCN,
            $envelopInfo->senderInfo->subjOrg,
            $envelopInfo->senderInfo->subjOrgUnit,
            $envelopInfo->senderInfo->subjTitle,
            $envelopInfo->senderInfo->subjState,
            $envelopInfo->senderInfo->subjLocality,
            $envelopInfo->senderInfo->subjFullName,
            $envelopInfo->senderInfo->subjAddress,
            $envelopInfo->senderInfo->subjPhone,
            $envelopInfo->senderInfo->subjEMail,
            $envelopInfo->senderInfo->subjDNS,
            $envelopInfo->senderInfo->subjEDRPOUCode,
            $envelopInfo->senderInfo->subjDRFOCode,
            $errorCode);
        if ($result != self::EM_RESULT_OK)
        {
            euspe_ctxfreeprivatekey($pkContext);
            euspe_ctxfree($context);

            $error = $this->createError(
                'Виникла помилка при розшифруванні даних',
                $result,
                $errorCode
            );

            return false;
        }

        $result = euspe_signverify(
            $envelopInfo->data,
            $signInfo->signTime,
            $signInfo->useTSP,
            $signInfo->signerInfo->issuer,
            $signInfo->signerInfo->issuerCN,
            $signInfo->signerInfo->serial,
            $signInfo->signerInfo->subject,
            $signInfo->signerInfo->subjCN,
            $signInfo->signerInfo->subjOrg,
            $signInfo->signerInfo->subjOrgUnit,
            $signInfo->signerInfo->subjTitle,
            $signInfo->signerInfo->subjState,
            $signInfo->signerInfo->subjLocality,
            $signInfo->signerInfo->subjFullName,
            $signInfo->signerInfo->subjAddress,
            $signInfo->signerInfo->subjPhone,
            $signInfo->signerInfo->subjEMail,
            $signInfo->signerInfo->subjDNS,
            $signInfo->signerInfo->subjEDRPOUCode,
            $signInfo->signerInfo->subjDRFOCode,
            $signInfo->data,
            $errorCode);
        if ($result != self::EM_RESULT_OK)
        {
            euspe_ctxfreeprivatekey($pkContext);
            euspe_ctxfree($context);

            $error = $this->createError(
                'Виникла помилка при перевірці даних',
                $result,
                $errorCode
            );

            return false;
        }

        euspe_ctxfreeprivatekey($pkContext);
        euspe_ctxfree($context);

        $error = $this->createError(
            '',
            $result,
            $errorCode
        );

        return true;
    }


    function createError($message, $result, $errorCode)
    {
        if ($result == self::EM_RESULT_ERROR_WRONG_PARAMS)
            $errorCode = self::EU_ERROR_BAD_PARAMETER;
        else if ($result == self::EM_RESULT_ERROR_INITIALIZED)
            $errorCode = self::EU_ERROR_NOT_INITIALIZED;

        $description = '';
        euspe_geterrdescr(
            $errorCode,
            $description
        );

        $error = new EUError();
        $error->code = $errorCode;
        if ($message != null && $message != '')
            $error->description = $message.'. '.'Опис помилки: '.$description;
        else
            $error->description = $description;

        return $error;
    }
}