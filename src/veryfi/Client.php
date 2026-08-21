<?php

declare(strict_types=1);
namespace veryfi;

use Exception;
use veryfi\anydocs\DeleteAnyDocument;
use veryfi\anydocs\GetAnyDocument;
use veryfi\anydocs\GetAnyDocuments;
use veryfi\anydocs\ProcessAnyDocument;
use veryfi\anydocs\ProcessAnyDocumentBase64;
use veryfi\anydocs\ProcessAnyDocumentUrl;
use veryfi\bankstatements\DeleteBankStatement;
use veryfi\bankstatements\GetBankStatement;
use veryfi\bankstatements\GetBankStatements;
use veryfi\bankstatements\ProcessBankStatement;
use veryfi\bankstatements\ProcessBankStatementUrl;
use veryfi\bankstatements\ProcessBankStatementBase64;
use veryfi\businesscards\DeleteBusinessCard;
use veryfi\businesscards\GetBusinessCard;
use veryfi\businesscards\GetBusinessCards;
use veryfi\businesscards\ProcessBusinessCard;
use veryfi\businesscards\ProcessBusinessCardBase64;
use veryfi\businesscards\ProcessBusinessCardUrl;
use veryfi\checks\DeleteCheck;
use veryfi\checks\GetCheck;
use veryfi\checks\GetChecks;
use veryfi\checks\ProcessCheck;
use veryfi\checks\ProcessCheckBase64;
use veryfi\checks\ProcessCheckUrl;
use veryfi\client\Exec;
use veryfi\client\GetHeaders;
use veryfi\client\GetUrl;
use veryfi\client\GenerateSignature;
use veryfi\client\Request;
use veryfi\client\VeryfiSignature;
use veryfi\documents\DeleteDocument;
use veryfi\documents\GetDocument;
use veryfi\documents\GetDocuments;
use veryfi\documents\ProcessDocument;
use veryfi\documents\ProcessDocumentBase64;
use veryfi\documents\ProcessDocumentUrl;
use veryfi\documents\tags\AddTag;
use veryfi\documents\tags\AddTags;
use veryfi\documents\tags\DeleteTag;
use veryfi\documents\tags\DeleteTags;
use veryfi\documents\tags\GetDocumentTags;
use veryfi\documents\tags\GetTags;
use veryfi\documents\tags\ReplaceTags;
use veryfi\documents\UpdateDocument;
use veryfi\w2s\DeleteW2;
use veryfi\w2s\GetW2;
use veryfi\w2s\GetW2s;
use veryfi\w2s\ProcessW2;
use veryfi\w2s\ProcessW2Base64;
use veryfi\w2s\ProcessW2Url;
use veryfi\w8bene\DeleteW8BENE;
use veryfi\w8bene\GetW8BENE;
use veryfi\w8bene\GetW8BENEs;
use veryfi\w8bene\ProcessW8BENE;
use veryfi\w8bene\ProcessW8BENEBase64;
use veryfi\w8bene\ProcessW8BENEUrl;
use veryfi\w9s\DeleteW9;
use veryfi\w9s\GetW9;
use veryfi\w9s\GetW9s;
use veryfi\w9s\ProcessW9;
use veryfi\w9s\ProcessW9Base64;
use veryfi\w9s\ProcessW9Url;
use veryfi\documents\lineitems\AddLineItem;
use veryfi\documents\lineitems\DeleteLineItem;
use veryfi\documents\lineitems\DeleteLineItems;
use veryfi\documents\lineitems\GetLineItem;
use veryfi\documents\lineitems\GetLineItems;
use veryfi\documents\lineitems\UpdateLineItem;
use veryfi\split\GetSplitDocument;
use veryfi\split\GetSplitDocuments;
use veryfi\split\ProcessSplitDocumentBase64;
use veryfi\split\ProcessSplitDocumentUrl;
use veryfi\classify\ProcessClassifyDocumentBase64;
use veryfi\classify\ProcessClassifyDocumentUrl;
use veryfi\classify\ExtractDocument;
use veryfi\contracts\ContractOperations;
use veryfi\fraud\FraudOperations;
use veryfi\parse\ParseOperations;
use veryfi\resources\SupportedResourceOperations;
use veryfi\settings\SettingsOperations;



/**
 * Veryfi-sdk for php
 *
 * @author Sebastian Carmona Tobon
 * @author Alejandro Uribe Sánchez
 * @license MIT
 */
class Client
{
    /**
     * Base url of Veryfi by default 'https://api.veryfi.com/api/'.
     * @var string
     */
    public string $base_url;
    /**
     * Api version to use Veryfi by default 'v8'
     * @var string
     */
    public string $api_version;
    /**
     * Api timeout to call Veryfi API by default 120.
     * @var int
     */
    public int $api_timeout;
    /**
     * Client id provided by Veryfi.
     *
     * @var string
     */
    public string $client_id;
    /**
     * Client secret provided by Veryfi.
     * @var string
     */
    public string $client_secret;
    /**
     * Username provided by Veryfi.
     * @var string
     */
    public string $username;
    /**
     * Api key provided by Veryfi.
     *
     * @var string
     */
    public string $api_key;
    /**
     * Associative array of headers.
     * @var array
     */
    private array $headers;
    /**
     * Base URL to Veryfi API.
     * @var string
     */
    private string $extend_url;

    /**
     * Constructor of Veryfi client
     *
     * @param string $client_id Client id provided by Veryfi.
     * @param string $client_secret Client secret provided by Veryfi.
     * @param string $username Username provided by Veryfi.
     * @param string $api_key Api key provided by Veryfi.
     * @param string $base_url Base url of Veryfi by default 'https://api.veryfi.com/api/',
     * @param string $api_version Api version to use Veryfi, currently v8
     * @param int $api_timeout Api timeout for call Veryfi api, by default 120
     */
    public function __construct(
        string $client_id,
        string $client_secret,
        string $username,
        string $api_key,
        string $base_url = 'https://api.veryfi.com/api/',
        string $api_version = 'v8',
        int $api_timeout = 120
    ) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->username = $username;
        $this->api_key = $api_key;
        $this->base_url = $base_url;
        $this->api_version = $api_version;
        $this->api_timeout = $api_timeout;
        $this->headers = $this->get_headers();
        $this->extend_url = $this->get_url();
    }

    use DeleteAnyDocument, GetAnyDocument, GetAnyDocuments, ProcessAnyDocument, ProcessAnyDocumentBase64, ProcessAnyDocumentUrl;
    use DeleteBankStatement, GetBankStatement, GetBankStatements, ProcessBankStatement, ProcessBankStatementUrl, ProcessBankStatementBase64;
    use DeleteBusinessCard, GetBusinessCard, GetBusinessCards, ProcessBusinessCard, ProcessBusinessCardBase64, ProcessBusinessCardUrl;
    use DeleteCheck, GetCheck, GetChecks, ProcessCheck, ProcessCheckBase64, ProcessCheckUrl;
    use Exec, GetHeaders, GetUrl, GenerateSignature, Request, VeryfiSignature;
    use DeleteDocument, GetDocument, GetDocuments, ProcessDocument, ProcessDocumentBase64, ProcessDocumentUrl, UpdateDocument;
    use AddTag, AddTags, DeleteTags, ReplaceTags, GetTags, GetDocumentTags, DeleteTag;
    use DeleteW2, GetW2, GetW2s, ProcessW2, ProcessW2Base64, ProcessW2Url;
    use DeleteW8BENE, GetW8BENE, GetW8BENEs, ProcessW8BENE, ProcessW8BENEBase64, ProcessW8BENEUrl;
    use DeleteW9, GetW9, GetW9s, ProcessW9, ProcessW9Base64, ProcessW9Url;
    use AddLineItem, DeleteLineItem, DeleteLineItems, GetLineItem, GetLineItems, UpdateLineItem;
    use GetSplitDocument, GetSplitDocuments, ProcessSplitDocumentBase64, ProcessSplitDocumentUrl;
    use ProcessClassifyDocumentBase64, ProcessClassifyDocumentUrl, ExtractDocument;
    use SupportedResourceOperations, ContractOperations, ParseOperations, FraudOperations, SettingsOperations;

}
