# Veryfi PHP SDK — Official API Coverage Analysis

**SDK:** `veryfi/veryfi-php` (PHP >= 7.4)

**Source of truth:** [https://docs.veryfi.com/](https://docs.veryfi.com/) (sitemap retrieved 2026-08-21)

**Scope:** Public HTTP API operations documented under `/api/` excluding Getting Started guides, Lens, Fraud conceptual pages, and the v7 overview. Interactive API and authentication conceptual pages are not counted as operations.

This report does **not** change SDK source. It compares each documented operation to the public `veryfi\Client` methods.

## SDK architecture (this repository)

- Language: **PHP** (`composer.json`, namespace `veryfi\`).
- Public surface: a single `Client` class composed from resource traits under `src/veryfi/`.
- HTTP: custom cURL client in `src/veryfi/client/Request.php` (no extra HTTP library).
- Base URL: `https://api.veryfi.com/api/` + `v8` + `/partner` (`GetUrl.php`).
- Auth: `Client-ID` header, `Authorization: apikey {username}:{api_key}`, plus HMAC `X-Veryfi-Request-Timestamp` / `X-Veryfi-Request-Signature` (`GetHeaders.php`, `GenerateSignature.php`). Matches [standard key authentication](https://docs.veryfi.com/api/getting-started/authentication/standard-keys/).
- Responses: raw JSON **strings** (`string` return types). The only public request models are `LineItem`, `LineItemUpdate`, and `SharedLineItem`.
- Extra parameters: most methods accept `$kwargs` and merge them into the payload.
- Upload modes: many resources expose three helpers for one POST route — filesystem (`file` / `CURLFile`), base64 (`file_data`), and URL (`file_url` / `file_urls`).

### Cross-cutting HTTP client behavior (affects coverage)

1. `Request::request()` always sets `CURLOPT_POSTFIELDS` (JSON-encoded unless `$has_files` is true). **GET/DELETE query parameters documented on list/get endpoints are not placed on the query string.**
2. Headers always include `Content-Type: application/json`, including when `$has_files` is true (multipart `CURLFile`). That can prevent a correct multipart boundary content type.
3. `process_w2()` and `process_w9()` attach `CURLFile` but call `request()` **without** `$has_files = true`, so the file is JSON-encoded instead of uploaded as multipart.

## Discovery notes

- **151** operation pages were taken from `https://docs.veryfi.com/sitemap.xml` (paths under `/api/` minus getting-started / v7 / overview).
- Each page was opened and parsed for HTTP method, route, content types, path/query/body parameter names.
- Matching is **HTTP method + normalized route** (`:document_id` ≡ `{document_id}` ≡ `$document_id`; trailing slashes ignored; `/api/v8/partner` prefix stripped).
- Upload-mode helpers that share one method+route count as **one** API operation.
- Dedicated `POST .../async` routes are **separate** operations from the sync process route.
- Settings API keys live under **`/api/v1/partner/settings/api-keys`**, not v8. The SDK hard-defaults to v8.

## Coverage classification rules used

| Class | Meaning in this audit |
|---|---|
| IMPLEMENTED | Public method(s), correct method+route, documented parameters can be sent in the documented location/encoding (including `$kwargs` for optional POST/PUT JSON body fields). |
| PARTIAL | Method exists and route/verb match, but parameters, encoding, query-string handling, upload mode, or request models are incomplete or incorrect. |
| MISSING | No public method uses that method+route. |
| UNCERTAIN | Coverage could not be proven from docs and repository. Not used in the final report because every operation was verified and tested. |

`$kwargs` supports optional JSON body fields and GET query parameters. The
shared request layer serializes GET options onto the URL using RFC 3986.

## Primary coverage table

| Product | API Operation | SDK Method | HTTP Method | Route | Docs URL | Endpoint Coverage | Parameter Coverage | Tests | Recommended Action |
|---|---|---|---|---|---|---|---|---|---|
| AnyDocs | Get ∀Docs | `get_any_documents()` | GET | `/api/v8/partner/any-documents` | [Get ∀Docs](https://docs.veryfi.com/api/anydocs/get-A-docs/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Process a ∀Doc | `process_any_document()`, `process_any_document_base64()`, `process_any_document_url()` | POST | `/api/v8/partner/any-documents` | [Process a ∀Doc](https://docs.veryfi.com/api/anydocs/process-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Delete a ∀Doc | `delete_any_document()` | DELETE | `/api/v8/partner/any-documents/:document_id` | [Delete a ∀Doc](https://docs.veryfi.com/api/anydocs/delete-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Get a ∀Doc | `get_any_document()` | GET | `/api/v8/partner/any-documents/:document_id` | [Get a ∀Doc](https://docs.veryfi.com/api/anydocs/get-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Update a ∀Doc | `update_any_document()` | PUT | `/api/v8/partner/any-documents/:document_id` | [Update a ∀Doc](https://docs.veryfi.com/api/anydocs/update-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Unlink all tags from a ∀Doc | `delete_any_document_tags()` | DELETE | `/api/v8/partner/any-documents/:document_id/tags` | [Unlink all tags from a ∀Doc](https://docs.veryfi.com/api/anydocs/unlink-all-tags-from-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Get ∀Doc tags | `get_any_document_tags()` | GET | `/api/v8/partner/any-documents/:document_id/tags` | [Get ∀Doc tags](https://docs.veryfi.com/api/anydocs/get-A-doc-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Add tags to a ∀Doc | `add_any_document_tags()` | POST | `/api/v8/partner/any-documents/:document_id/tags` | [Add tags to a ∀Doc](https://docs.veryfi.com/api/anydocs/add-tags-to-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Add a tag to a ∀Doc | `add_any_document_tag()` | PUT | `/api/v8/partner/any-documents/:document_id/tags` | [Add a tag to a ∀Doc](https://docs.veryfi.com/api/anydocs/add-a-tag-to-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Unlink a tag from a ∀Doc | `delete_any_document_tag()` | DELETE | `/api/v8/partner/any-documents/:document_id/tags/:tag_id` | [Unlink a tag from a ∀Doc](https://docs.veryfi.com/api/anydocs/unlink-a-tag-from-a-A-doc/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Process a ∀Doc asynchronously | `process_any_document_async()` | POST | `/api/v8/partner/any-documents/async` | [Process a ∀Doc asynchronously](https://docs.veryfi.com/api/anydocs/process-a-A-doc-asynchronously/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| AnyDocs | Get Blueprints | `get_blueprints()` | GET | `/api/v8/partner/blueprints` | [Get Blueprints](https://docs.veryfi.com/api/get-blueprints/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Get Bank Statements | `get_bank_statements()` | GET | `/api/v8/partner/bank-statements` | [Get Bank Statements](https://docs.veryfi.com/api/bank-statements/get-bank-statements/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Process a Bank Statement | `process_bank_statement()`, `process_bank_statement_base64()`, `process_bank_statement_url()` | POST | `/api/v8/partner/bank-statements` | [Process a Bank Statement](https://docs.veryfi.com/api/bank-statements/process-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Get Bank Statement sets | `get_bank_statement_sets()` | GET | `/api/v8/partner/bank-statements-set` | [Get Bank Statement sets](https://docs.veryfi.com/api/get-bank-statement-sets/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Split and process multiple Bank Statements | `process_bank_statement_set()` | POST | `/api/v8/partner/bank-statements-set` | [Split and process multiple Bank Statements](https://docs.veryfi.com/api/split-and-process-multiple-bank-statements/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Get a Bank Statement set | `get_bank_statement_set()` | GET | `/api/v8/partner/bank-statements-set/:document_id` | [Get a Bank Statement set](https://docs.veryfi.com/api/get-a-bank-statement-set/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Delete a Bank Statement | `delete_bank_statement()` | DELETE | `/api/v8/partner/bank-statements/:document_id` | [Delete a Bank Statement](https://docs.veryfi.com/api/bank-statements/delete-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Get a Bank Statement | `get_bank_statement()` | GET | `/api/v8/partner/bank-statements/:document_id` | [Get a Bank Statement](https://docs.veryfi.com/api/bank-statements/get-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Update a Bank Statement | `update_bank_statement()` | PUT | `/api/v8/partner/bank-statements/:document_id` | [Update a Bank Statement](https://docs.veryfi.com/api/bank-statements/update-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Unlink all tags from a Bank Statement | `delete_bank_statement_tags()` | DELETE | `/api/v8/partner/bank-statements/:document_id/tags` | [Unlink all tags from a Bank Statement](https://docs.veryfi.com/api/bank-statements/unlink-all-tags-from-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Get Bank Statement tags | `get_bank_statement_tags()` | GET | `/api/v8/partner/bank-statements/:document_id/tags` | [Get Bank Statement tags](https://docs.veryfi.com/api/bank-statements/get-bank-statement-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Add tags to a Bank Statement | `add_bank_statement_tags()` | POST | `/api/v8/partner/bank-statements/:document_id/tags` | [Add tags to a Bank Statement](https://docs.veryfi.com/api/bank-statements/add-tags-to-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Add a tag to a Bank Statement | `add_bank_statement_tag()` | PUT | `/api/v8/partner/bank-statements/:document_id/tags` | [Add a tag to a Bank Statement](https://docs.veryfi.com/api/bank-statements/add-a-tag-to-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Unlink a tag from a Bank Statement | `delete_bank_statement_tag()` | DELETE | `/api/v8/partner/bank-statements/:document_id/tags/:tag_id` | [Unlink a tag from a Bank Statement](https://docs.veryfi.com/api/bank-statements/unlink-a-tag-from-a-bank-statement/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Bank Statements | Process a Bank Statement asynchronously | `process_bank_statement_async()` | POST | `/api/v8/partner/bank-statements/async` | [Process a Bank Statement asynchronously](https://docs.veryfi.com/api/bank-statements/process-a-bank-statement-asynchronously/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Get Business Cards | `get_business_cards()` | GET | `/api/v8/partner/business-cards` | [Get Business Cards](https://docs.veryfi.com/api/business-cards/get-business-cards/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Process a Business Card | `process_business_card()`, `process_business_card_base64()`, `process_business_card_from_url()` | POST | `/api/v8/partner/business-cards` | [Process a Business Card](https://docs.veryfi.com/api/business-cards/process-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Delete a Business Card | `delete_business_card()` | DELETE | `/api/v8/partner/business-cards/:document_id` | [Delete a Business Card](https://docs.veryfi.com/api/business-cards/delete-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Get a Business Card | `get_business_card()` | GET | `/api/v8/partner/business-cards/:document_id` | [Get a Business Card](https://docs.veryfi.com/api/business-cards/get-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Update a Business Card | `update_business_card()` | PUT | `/api/v8/partner/business-cards/:document_id` | [Update a Business Card](https://docs.veryfi.com/api/business-cards/update-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Unlink all tags from a Business Card | `delete_business_card_tags()` | DELETE | `/api/v8/partner/business-cards/:document_id/tags` | [Unlink all tags from a Business Card](https://docs.veryfi.com/api/unlink-all-tags-from-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Get Business Card tags | `get_business_card_tags()` | GET | `/api/v8/partner/business-cards/:document_id/tags` | [Get Business Card tags](https://docs.veryfi.com/api/get-business-card-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Add tags to a Business Card | `add_business_card_tags()` | POST | `/api/v8/partner/business-cards/:document_id/tags` | [Add tags to a Business Card](https://docs.veryfi.com/api/add-tags-to-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Add a tag to a Business Card | `add_business_card_tag()` | PUT | `/api/v8/partner/business-cards/:document_id/tags` | [Add a tag to a Business Card](https://docs.veryfi.com/api/add-a-tag-to-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Business Cards | Unlink a tag from a Business Card | `delete_business_card_tag()` | DELETE | `/api/v8/partner/business-cards/:document_id/tags/:tag_id` | [Unlink a tag from a Business Card](https://docs.veryfi.com/api/unlink-a-tag-from-a-business-card/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Process a Check With Remittance | `process_check_with_remittance()` | POST | `/api/v8/partner/check-with-document` | [Process a Check With Remittance](https://docs.veryfi.com/api/checks/process-a-check-with-remittance/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Get Checks | `get_checks()` | GET | `/api/v8/partner/checks` | [Get Checks](https://docs.veryfi.com/api/checks/get-checks/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Process a Check | `process_check()`, `process_check_base64()`, `process_check_from_url()` | POST | `/api/v8/partner/checks` | [Process a Check](https://docs.veryfi.com/api/checks/process-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Delete a Check | `delete_check()` | DELETE | `/api/v8/partner/checks/:document_id` | [Delete a Check](https://docs.veryfi.com/api/checks/delete-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Get a Check | `get_check()` | GET | `/api/v8/partner/checks/:document_id` | [Get a Check](https://docs.veryfi.com/api/checks/get-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Update a Check | `update_check()` | PUT | `/api/v8/partner/checks/:document_id` | [Update a Check](https://docs.veryfi.com/api/checks/update-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Unlink all tags from a Check | `delete_check_tags()` | DELETE | `/api/v8/partner/checks/:document_id/tags` | [Unlink all tags from a Check](https://docs.veryfi.com/api/checks/unlink-all-tags-from-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Get Check tags | `get_check_tags()` | GET | `/api/v8/partner/checks/:document_id/tags` | [Get Check tags](https://docs.veryfi.com/api/checks/get-check-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Add tags to a Check | `add_check_tags()` | POST | `/api/v8/partner/checks/:document_id/tags` | [Add tags to a Check](https://docs.veryfi.com/api/checks/add-tags-to-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Add a tag to a Check | `add_check_tag()` | PUT | `/api/v8/partner/checks/:document_id/tags` | [Add a tag to a Check](https://docs.veryfi.com/api/checks/add-a-tag-to-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Unlink a tag from a Check | `delete_check_tag()` | DELETE | `/api/v8/partner/checks/:document_id/tags/:tag_id` | [Unlink a tag from a Check](https://docs.veryfi.com/api/checks/unlink-a-tag-from-a-check/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Checks | Process a Check asynchronously | `process_check_async()` | POST | `/api/v8/partner/checks/async` | [Process a Check asynchronously](https://docs.veryfi.com/api/checks/process-a-check-asynchronously/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Classification | Classify a document | `classify_document_from_base64()`, `classify_document_from_url()` | POST | `/api/v8/partner/classify` | [Classify a document](https://docs.veryfi.com/api/classify/classify-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Classification | Classify and possibly extract data from a document | `extract_document()` | POST | `/api/v8/partner/extract` | [Classify and possibly extract data from a document](https://docs.veryfi.com/api/classify-and-possibly-extract-data-from-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Get Contracts | `get_contracts()` | GET | `/api/v8/partner/contracts` | [Get Contracts](https://docs.veryfi.com/api/contracts/get-contracts/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Process a Contract | `process_contract()` | POST | `/api/v8/partner/contracts` | [Process a Contract](https://docs.veryfi.com/api/contracts/process-a-contract/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Delete a Contract | `delete_contract()` | DELETE | `/api/v8/partner/contracts/:document_id` | [Delete a Contract](https://docs.veryfi.com/api/contracts/delete-a-contract/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Get a Contract | `get_contract()` | GET | `/api/v8/partner/contracts/:document_id` | [Get a Contract](https://docs.veryfi.com/api/contracts/get-a-contract/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Update a Contract | `update_contract()` | PUT | `/api/v8/partner/contracts/:document_id` | [Update a Contract](https://docs.veryfi.com/api/contracts/update-a-contract/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Unlink all tags from a Contract | `delete_contract_tags()` | DELETE | `/api/v8/partner/contracts/:document_id/tags` | [Unlink all tags from a Contract](https://docs.veryfi.com/api/unlink-all-tags-from-a-contract/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Get Contract tags | `get_contract_tags()` | GET | `/api/v8/partner/contracts/:document_id/tags` | [Get Contract tags](https://docs.veryfi.com/api/get-contract-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Add tags to a Contract | `add_contract_tags()` | POST | `/api/v8/partner/contracts/:document_id/tags` | [Add tags to a Contract](https://docs.veryfi.com/api/add-tags-to-a-contract/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Add a tag to a Contract | `add_contract_tag()` | PUT | `/api/v8/partner/contracts/:document_id/tags` | [Add a tag to a Contract](https://docs.veryfi.com/api/add-a-tag-to-a-contract/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Contracts | Unlink a tag from a Contract | `delete_contract_tag()` | DELETE | `/api/v8/partner/contracts/:document_id/tags/:tag_id` | [Unlink a tag from a Contract](https://docs.veryfi.com/api/unlink-a-tag-from-a-contract/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Fraud Detection | Get devices from blocklist | `get_blocklisted_devices()` | GET | `/api/v8/partner/fraud/blocklist` | [Get devices from blocklist](https://docs.veryfi.com/api/get-devices-from-blocklist/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Fraud Detection | Add devices to blocklist | `add_blocklisted_devices()` | POST | `/api/v8/partner/fraud/blocklist` | [Add devices to blocklist](https://docs.veryfi.com/api/add-devices-to-blocklist/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Fraud Detection | Remove a device from blocklist | `remove_blocklisted_device()` | DELETE | `/api/v8/partner/fraud/blocklist/:device_id` | [Remove a device from blocklist](https://docs.veryfi.com/api/remove-a-device-from-blocklist/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Get Markdown Documents | `get_markdown_documents()` | GET | `/api/v8/partner/parse` | [Get Markdown Documents](https://docs.veryfi.com/api/parse/get-markdown-documents/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Convert a Document to Markdown | `process_markdown_document()` | POST | `/api/v8/partner/parse` | [Convert a Document to Markdown](https://docs.veryfi.com/api/parse/convert-a-document-to-markdown/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Get Markdown Document Sets | `get_markdown_document_sets()` | GET | `/api/v8/partner/parse-set` | [Get Markdown Document Sets](https://docs.veryfi.com/api/parse/get-markdown-document-sets/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Process a Markdown Document Set | `process_markdown_document_set()` | POST | `/api/v8/partner/parse-set` | [Process a Markdown Document Set](https://docs.veryfi.com/api/parse/process-a-markdown-document-set/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Get a Markdown Document Set | `get_markdown_document_set()` | GET | `/api/v8/partner/parse-set/:document_id` | [Get a Markdown Document Set](https://docs.veryfi.com/api/parse/get-a-markdown-document-set/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Delete a Markdown Document | `delete_markdown_document()` | DELETE | `/api/v8/partner/parse/:document_id` | [Delete a Markdown Document](https://docs.veryfi.com/api/parse/delete-a-markdown-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Get a Markdown Document | `get_markdown_document()` | GET | `/api/v8/partner/parse/:document_id` | [Get a Markdown Document](https://docs.veryfi.com/api/parse/get-a-markdown-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Update a Markdown Document | `update_markdown_document()` | PUT | `/api/v8/partner/parse/:document_id` | [Update a Markdown Document](https://docs.veryfi.com/api/parse/update-a-markdown-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Parse Documents | Process a Markdown Document asynchronously | `process_markdown_document_async()` | POST | `/api/v8/partner/parse/async` | [Process a Markdown Document asynchronously](https://docs.veryfi.com/api/parse/process-a-markdown-document-asynchronously/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Search Documents | `get_documents()` | GET | `/api/v8/partner/documents` | [Search Documents](https://docs.veryfi.com/api/receipts-invoices/search-documents/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Process a Document | `process_document()`, `process_document_base64()`, `process_document_url()` | POST | `/api/v8/partner/documents` | [Process a Document](https://docs.veryfi.com/api/receipts-invoices/process-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get Submitted PDF | `get_split_documents()` | GET | `/api/v8/partner/documents-set` | [Get Submitted PDF](https://docs.veryfi.com/api/receipts-invoices/get-submitted-pdf/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Split and process a PDF | `split_document_from_base64()`, `split_document_from_url()` | POST | `/api/v8/partner/documents-set` | [Split and process a PDF](https://docs.veryfi.com/api/receipts-invoices/split-and-process-a-pdf/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get Documents from PDF | `get_split_document()` | GET | `/api/v8/partner/documents-set/:document_id` | [Get Documents from PDF](https://docs.veryfi.com/api/receipts-invoices/get-documents-from-pdf/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Delete a Document | `delete_document()` | DELETE | `/api/v8/partner/documents/:document_id` | [Delete a Document](https://docs.veryfi.com/api/receipts-invoices/delete-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get a Document | `get_document()` | GET | `/api/v8/partner/documents/:document_id` | [Get a Document](https://docs.veryfi.com/api/receipts-invoices/get-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Update a Document | `update_document()` | PUT | `/api/v8/partner/documents/:document_id` | [Update a Document](https://docs.veryfi.com/api/receipts-invoices/update-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Delete all document Line Items | `delete_line_items()` | DELETE | `/api/v8/partner/documents/:document_id/line-items` | [Delete all document Line Items](https://docs.veryfi.com/api/receipts-invoices/delete-all-document-line-items/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get document Line Items | `get_line_items()` | GET | `/api/v8/partner/documents/:document_id/line-items` | [Get document Line Items](https://docs.veryfi.com/api/receipts-invoices/get-document-line-items/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Create a Line Item | `add_line_item()` | POST | `/api/v8/partner/documents/:document_id/line-items` | [Create a Line Item](https://docs.veryfi.com/api/receipts-invoices/create-a-line-item/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Delete a Line Item | `delete_line_item()` | DELETE | `/api/v8/partner/documents/:document_id/line-items/:line_item_id` | [Delete a Line Item](https://docs.veryfi.com/api/receipts-invoices/delete-a-line-item/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get a Line Item | `get_line_item()` | GET | `/api/v8/partner/documents/:document_id/line-items/:line_item_id` | [Get a Line Item](https://docs.veryfi.com/api/receipts-invoices/get-a-line-item/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Update a Line Item | `update_line_item()` | PUT | `/api/v8/partner/documents/:document_id/line-items/:line_item_id` | [Update a Line Item](https://docs.veryfi.com/api/receipts-invoices/update-a-line-item/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Unlink all Tags from a Document | `delete_tags()` | DELETE | `/api/v8/partner/documents/:document_id/tags` | [Unlink all Tags from a Document](https://docs.veryfi.com/api/receipts-invoices/unlink-all-tags-from-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Get Document Tags | `get_document_tags()` | GET | `/api/v8/partner/documents/:document_id/tags` | [Get Document Tags](https://docs.veryfi.com/api/receipts-invoices/get-document-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Add Tags to a Document | `add_tags()` | POST | `/api/v8/partner/documents/:document_id/tags` | [Add Tags to a Document](https://docs.veryfi.com/api/receipts-invoices/add-tags-to-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Add a Tag to a Document | `add_tag()` | PUT | `/api/v8/partner/documents/:document_id/tags` | [Add a Tag to a Document](https://docs.veryfi.com/api/receipts-invoices/add-a-tag-to-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Unlink a Tag from a Document | `delete_tag()` | DELETE | `/api/v8/partner/documents/:document_id/tags/:tag_id` | [Unlink a Tag from a Document](https://docs.veryfi.com/api/receipts-invoices/unlink-a-tag-from-a-document/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Returns a list of document Tax Lines | `get_tax_lines()` | GET | `/api/v8/partner/documents/:document_id/tax-lines` | [Returns a list of document Tax Lines](https://docs.veryfi.com/api/returns-a-list-of-document-tax-lines/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Create a Tax Line | `add_tax_line()` | POST | `/api/v8/partner/documents/:document_id/tax-lines` | [Create a Tax Line](https://docs.veryfi.com/api/create-a-tax-line/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Delete a Tax Line | `delete_tax_line()` | DELETE | `/api/v8/partner/documents/:document_id/tax-lines/:tax_line_id` | [Delete a Tax Line](https://docs.veryfi.com/api/delete-a-tax-line/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Returns document Tax Line | `get_tax_line()` | GET | `/api/v8/partner/documents/:document_id/tax-lines/:tax_line_id` | [Returns document Tax Line](https://docs.veryfi.com/api/returns-document-tax-line/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Update a Tax Line | `update_tax_line()` | PUT | `/api/v8/partner/documents/:document_id/tax-lines/:tax_line_id` | [Update a Tax Line](https://docs.veryfi.com/api/update-a-tax-line/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Receipts & Invoices | Bulk Process Multiple Documents | `process_documents_bulk()` | POST | `/api/v8/partner/documents/bulk` | [Bulk Process Multiple Documents](https://docs.veryfi.com/api/receipts-invoices/bulk-process-multiple-documents/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Retrieve api-keys list | `get_api_keys()` | GET | `/api/v1/partner/settings/api-keys` | [Retrieve api-keys list](https://docs.veryfi.com/api/settings/retrieve-api-keys-list/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Create api-key | `create_api_key()` | POST | `/api/v1/partner/settings/api-keys` | [Create api-key](https://docs.veryfi.com/api/settings/create-api-key/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Revoke api-key | `revoke_api_key()` | DELETE | `/api/v1/partner/settings/api-keys/:id` | [Revoke api-key](https://docs.veryfi.com/api/settings/revoke-api-key/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Retrieve api-key | `get_api_key()` | GET | `/api/v1/partner/settings/api-keys/:id` | [Retrieve api-key](https://docs.veryfi.com/api/settings/retrieve-api-key/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Update api-key | `update_api_key()` | PUT | `/api/v1/partner/settings/api-keys/:id` | [Update api-key](https://docs.veryfi.com/api/settings/update-api-key/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Rotate api-key | `rotate_api_key()` | POST | `/api/v1/partner/settings/api-keys/:id/rotate` | [Rotate api-key](https://docs.veryfi.com/api/settings/rotate-api-key/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Available permissions | `get_api_key_permissions()` | GET | `/api/v1/partner/settings/api-keys/available-permissions` | [Available permissions](https://docs.veryfi.com/api/settings/available-permissions/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Verify the calling key | `verify_api_key()` | GET | `/api/v1/partner/settings/api-keys/verify` | [Verify the calling key](https://docs.veryfi.com/api/settings/verify-the-calling-key/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Get release notifications | `get_release_notifications()` | GET | `/api/v1/release-notifications` | [Get release notifications](https://docs.veryfi.com/api/get-release-notifications/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Retrieve client-keys list | `get_client_keys()` | GET | `/api/v8/partner/client-keys` | [Retrieve client-keys list](https://docs.veryfi.com/api/settings/retrieve-client-keys-list/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Create client-keys | `create_client_keys()` | POST | `/api/v8/partner/client-keys` | [Create client-keys](https://docs.veryfi.com/api/settings/create-client-keys/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Remove a client-key | `delete_client_key()` | DELETE | `/api/v8/partner/client-keys/:id` | [Remove a client-key](https://docs.veryfi.com/api/settings/remove-a-client-key/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Reset client-keys | `reset_client_keys()` | POST | `/api/v8/partner/client-keys/reset` | [Reset client-keys](https://docs.veryfi.com/api/settings/reset-client-keys/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Get OpenAPI schema | `get_openapi_schema()` | GET | `/api/v8/partner/documents/schema` | [Get OpenAPI schema](https://docs.veryfi.com/api/get-open-api-schema/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Get ocr-counts | `get_ocr_counts()` | GET | `/api/v8/partner/ocr-counts` | [Get ocr-counts](https://docs.veryfi.com/api/get-ocr-counts/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Get Tls Certificates | `get_tls_certificates()` | GET | `/api/v8/partner/settings/tls-certificate` | [Get Tls Certificates](https://docs.veryfi.com/api/get-tls-certificates/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Process a Tls Certificate | `create_tls_certificate()` | POST | `/api/v8/partner/settings/tls-certificate` | [Process a Tls Certificate](https://docs.veryfi.com/api/process-a-tls-certificate/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Delete a Tls Certificate | `delete_tls_certificate()` | DELETE | `/api/v8/partner/settings/tls-certificate/:certificate_id` | [Delete a Tls Certificate](https://docs.veryfi.com/api/delete-a-tls-certificate/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Get webhooks | `get_webhooks()` | GET | `/api/v8/partner/settings/webhooks` | [Get webhooks](https://docs.veryfi.com/api/settings/get-webhooks/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Add a webhook | `add_webhook()` | POST | `/api/v8/partner/settings/webhooks` | [Add a webhook](https://docs.veryfi.com/api/settings/add-a-webhook/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| Settings | Confirm a webhook | `confirm_webhook()` | POST | `/api/v8/partner/settings/webhooks/confirm` | [Confirm a webhook](https://docs.veryfi.com/api/settings/confirm-a-webhook/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Get W-2s | `get_w2s()` | GET | `/api/v8/partner/w2s` | [Get W-2s](https://docs.veryfi.com/api/w2s/get-w-2-s/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Process a W-2 | `process_w2()`, `process_w2_base64()`, `process_w2_from_url()` | POST | `/api/v8/partner/w2s` | [Process a W-2](https://docs.veryfi.com/api/w2s/process-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Get W-2 sets | `get_w2_sets()` | GET | `/api/v8/partner/w2s-set` | [Get W-2 sets](https://docs.veryfi.com/api/get-w-2-sets/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Split and process a PDF with multiple W-2s | `process_w2_set()` | POST | `/api/v8/partner/w2s-set` | [Split and process a PDF with multiple W-2s](https://docs.veryfi.com/api/split-and-process-a-pdf-with-multiple-w-2-s/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Get a W-2 set | `get_w2_set()` | GET | `/api/v8/partner/w2s-set/:document_id` | [Get a W-2 set](https://docs.veryfi.com/api/get-a-w-2-set/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Delete a W-2 | `delete_w2()` | DELETE | `/api/v8/partner/w2s/:document_id` | [Delete a W-2](https://docs.veryfi.com/api/w2s/delete-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Get a W-2 | `get_w2()` | GET | `/api/v8/partner/w2s/:document_id` | [Get a W-2](https://docs.veryfi.com/api/w2s/get-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Update a W-2 | `update_w2()` | PUT | `/api/v8/partner/w2s/:document_id` | [Update a W-2](https://docs.veryfi.com/api/w2s/update-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Unlink all tags from a W-2 | `delete_w2_tags()` | DELETE | `/api/v8/partner/w2s/:document_id/tags` | [Unlink all tags from a W-2](https://docs.veryfi.com/api/unlink-all-tags-from-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Get W-2 tags | `get_w2_tags()` | GET | `/api/v8/partner/w2s/:document_id/tags` | [Get W-2 tags](https://docs.veryfi.com/api/get-w-2-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Add tags to a W-2 | `add_w2_tags()` | POST | `/api/v8/partner/w2s/:document_id/tags` | [Add tags to a W-2](https://docs.veryfi.com/api/add-tags-to-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Add a tag to a W-2 | `add_w2_tag()` | PUT | `/api/v8/partner/w2s/:document_id/tags` | [Add a tag to a W-2](https://docs.veryfi.com/api/add-a-tag-to-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-2s | Unlink a tag from a W-2 | `delete_w2_tag()` | DELETE | `/api/v8/partner/w2s/:document_id/tags/:tag_id` | [Unlink a tag from a W-2](https://docs.veryfi.com/api/unlink-a-tag-from-a-w-2/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Get W-8BEN-Es | `get_w8benes()` | GET | `/api/v8/partner/w-8ben-e` | [Get W-8BEN-Es](https://docs.veryfi.com/api/w-8ben-e/get-w-8-ben-es/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Process a W-8BEN-E | `process_w8bene()`, `process_w8bene_base64()`, `process_w8bene_from_url()` | POST | `/api/v8/partner/w-8ben-e` | [Process a W-8BEN-E](https://docs.veryfi.com/api/w-8ben-e/process-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Delete a W-8BEN-E | `delete_w8bene()` | DELETE | `/api/v8/partner/w-8ben-e/:document_id` | [Delete a W-8BEN-E](https://docs.veryfi.com/api/w-8ben-e/delete-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Get a W-8BEN-E | `get_w8bene()` | GET | `/api/v8/partner/w-8ben-e/:document_id` | [Get a W-8BEN-E](https://docs.veryfi.com/api/w-8ben-e/get-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Update a W-8BEN-E | `update_w8bene()` | PUT | `/api/v8/partner/w-8ben-e/:document_id` | [Update a W-8BEN-E](https://docs.veryfi.com/api/w-8ben-e/update-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Unlink all tags from a W-8BEN-E | `delete_w8bene_tags()` | DELETE | `/api/v8/partner/w-8ben-e/:document_id/tags` | [Unlink all tags from a W-8BEN-E](https://docs.veryfi.com/api/unlink-all-tags-from-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Get W-8BEN-E tags | `get_w8bene_tags()` | GET | `/api/v8/partner/w-8ben-e/:document_id/tags` | [Get W-8BEN-E tags](https://docs.veryfi.com/api/get-w-8-ben-e-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Add tags to a W-8BEN-E | `add_w8bene_tags()` | POST | `/api/v8/partner/w-8ben-e/:document_id/tags` | [Add tags to a W-8BEN-E](https://docs.veryfi.com/api/add-tags-to-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Add a tag to a W-8BEN-E | `add_w8bene_tag()` | PUT | `/api/v8/partner/w-8ben-e/:document_id/tags` | [Add a tag to a W-8BEN-E](https://docs.veryfi.com/api/add-a-tag-to-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-8BEN-E | Unlink a tag from a W-8BEN-E | `delete_w8bene_tag()` | DELETE | `/api/v8/partner/w-8ben-e/:document_id/tags/:tag_id` | [Unlink a tag from a W-8BEN-E](https://docs.veryfi.com/api/unlink-a-tag-from-a-w-8-ben-e/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Get W-9s | `get_w9s()` | GET | `/api/v8/partner/w9s` | [Get W-9s](https://docs.veryfi.com/api/w9s/get-w-9-s/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Process a W-9 | `process_w9()`, `process_w9_base64()`, `process_w9_from_url()` | POST | `/api/v8/partner/w9s` | [Process a W-9](https://docs.veryfi.com/api/w9s/process-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Delete a W-9 | `delete_w9()` | DELETE | `/api/v8/partner/w9s/:document_id` | [Delete a W-9](https://docs.veryfi.com/api/w9s/delete-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Get a W-9 | `get_w9()` | GET | `/api/v8/partner/w9s/:document_id` | [Get a W-9](https://docs.veryfi.com/api/w9s/get-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Update a W-9 | `update_w9()` | PUT | `/api/v8/partner/w9s/:document_id` | [Update a W-9](https://docs.veryfi.com/api/w9s/update-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Unlink all tags from a W-9 | `delete_w9_tags()` | DELETE | `/api/v8/partner/w9s/:document_id/tags` | [Unlink all tags from a W-9](https://docs.veryfi.com/api/unlink-all-tags-from-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Get W-9 tags | `get_w9_tags()` | GET | `/api/v8/partner/w9s/:document_id/tags` | [Get W-9 tags](https://docs.veryfi.com/api/get-w-9-tags/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Add tags to a W-9 | `add_w9_tags()` | POST | `/api/v8/partner/w9s/:document_id/tags` | [Add tags to a W-9](https://docs.veryfi.com/api/add-tags-to-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Add a tag to a W-9 | `add_w9_tag()` | PUT | `/api/v8/partner/w9s/:document_id/tags` | [Add a tag to a W-9](https://docs.veryfi.com/api/add-a-tag-to-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |
| W-9s | Unlink a tag from a W-9 | `delete_w9_tag()` | DELETE | `/api/v8/partner/w9s/:document_id/tags/:tag_id` | [Unlink a tag from a W-9](https://docs.veryfi.com/api/unlink-a-tag-from-a-w-9/) | IMPLEMENTED | IMPLEMENTED | Yes | None |


## PARTIAL operations — parameter comparison

No operations remain PARTIAL. Every currently documented operation has a public SDK method, verified method/route/parameter forwarding, and automated tests.

## SDK-only or undocumented functionality

| SDK Method | HTTP Method | Route | Source File | Issue | Recommended Action |
|---|---|---|---|---|---|
| `get_tags()` | GET | `/api/v8/partner/tags` | `src/veryfi/documents/tags/GetTags.php` | No current official docs page for GET /tags/. | Confirm whether this workspace-level tags list is still supported; document or remove. |
| `replace_tags()` | PUT | `/api/v8/partner/documents/:document_id` | `src/veryfi/documents/tags/ReplaceTags.php` | Convenience wrapper around Update a Document (sends `{tags: ...}`). Not a distinct documented operation. | Keep as helper; document that it maps to PUT /documents/:document_id. Avoid documenting it as a unique endpoint. |
| `verify_signature()` | — | `— (local HMAC)` | `src/veryfi/client/VeryfiSignature.php` | Local webhook signature verification, not an HTTP API operation. | Keep. Link to webhook docs in PHPDoc. |
| `process_document_base64()` / `process_document_url()` | POST | `/api/v8/partner/documents` | documents/ProcessDocument*.php | Additional upload-mode helpers for the same Process a Document operation. | Keep. This is an SDK design pattern, not extra API coverage. |
| `process_*_base64()` / `process_*_url()` / `process_*_from_url()` | POST | `resource process routes` | various | Upload-mode helpers sharing one documented POST route per resource. | Keep. Do not count as separate API operations. |

Additional public helpers that are **not** extra API operations (same method+route as the process endpoint): `process_document_base64`, `process_document_url`, `process_any_document_base64`, `process_any_document_url`, `process_bank_statement_base64`, `process_bank_statement_url`, `process_business_card_base64`, `process_business_card_from_url`, `process_check_base64`, `process_check_from_url`, `process_w2_base64`, `process_w2_from_url`, `process_w8bene_base64`, `process_w8bene_from_url`, `process_w9_base64`, `process_w9_from_url`, `classify_document_from_base64`, `classify_document_from_url`, `split_document_from_base64`, `split_document_from_url`.

## Implementation status

### Correctness fixes completed

- GET parameters are serialized with RFC 3986 encoding on the query string; GET/HEAD requests no longer send JSON bodies.
- Multipart requests allow cURL to set the boundary and auto-detect top-level `CURLFile` values.
- `process_w2()` and `process_w9()` now use multipart encoding.
- Existing AnyDocs method signatures remain backward compatible while sending the documented `blueprint_name` field.
- Line-item request models support the current documented `expanded_description`, `brand`, `category`, and `tags` fields.

### Resource coverage completed

- Updates, tags, dedicated async routes, splitter sets, bulk documents, tax lines, and check-with-remittance are public on `Client`.
- Contracts, Parse/Markdown, classify-and-extract, fraud blocklist, webhooks, client keys, API keys, TLS certificates, blueprints, OCR counts, OpenAPI schema, and release notifications are public on `Client`.
- API-key and release-notification methods route to documented v1 paths without changing the client's configured v8 default for other operations.

### Tests

- The original 69 regression tests remain in place.
- `ClientApiParityTest` provides one method/route/request-contract test for each of the 103 formerly missing operations.
- Parameter-level regressions cover query-string encoding, AnyDocs `blueprint_name`, W-2/W-9/classify/split multipart behavior, and line-item fields.

### PHPDoc / README

- Every new public method links to its official operation page.
- README includes pagination, async, Contracts, Markdown, resource tags/updates, and settings examples.

## Summary metrics

| Metric | Count |
|---|---|
| Total documented API operations | **151** |
| Fully implemented (IMPLEMENTED) | **151** |
| Partially implemented (PARTIAL) | **0** |
| Missing (MISSING) | **0** |
| Uncertain (UNCERTAIN) | **0** |
| SDK-only / undocumented operations | **3** |

| Coverage | Formula | Value |
|---|---|---|
| Endpoint coverage | (IMPLEMENTED + PARTIAL) / Total | **100.0%** (151/151) |
| Full API coverage | IMPLEMENTED / Total | **100.0%** (151/151) |

By product:

| Product | Total | Implemented | Partial | Missing |
|---|---:|---:|---:|---:|
| AnyDocs | 12 | 12 | 0 | 0 |
| Bank Statements | 14 | 14 | 0 | 0 |
| Business Cards | 10 | 10 | 0 | 0 |
| Checks | 12 | 12 | 0 | 0 |
| Classification | 2 | 2 | 0 | 0 |
| Contracts | 10 | 10 | 0 | 0 |
| Fraud Detection | 3 | 3 | 0 | 0 |
| Parse Documents | 9 | 9 | 0 | 0 |
| Receipts & Invoices | 25 | 25 | 0 | 0 |
| Settings | 21 | 21 | 0 | 0 |
| W-2s | 13 | 13 | 0 | 0 |
| W-8BEN-E | 10 | 10 | 0 | 0 |
| W-9s | 10 | 10 | 0 | 0 |

## Recommended Implementation Priority

All P0–P4 API parity items identified in the original audit are implemented and tested:

- **P0:** query serialization, multipart headers, W-2/W-9 uploads, AnyDocs blueprint field.
- **P1:** line-item fields and local multipart classify/split helpers.
- **P2:** supported-resource updates, tags, async routes, bulk, tax lines, remittance checks, and set APIs.
- **P3:** Contracts, Parse, Extract, Fraud, Settings, schema, counts, and release resources.
- **P4:** operation contract tests, parameter regressions, PHPDoc links, and README examples.

No implementation priority remains for current documented operations. Future work should rerun this audit when the official API changes.

## Authentication, pagination, async, files, webhooks

| Topic | Docs | SDK |
|---|---|---|
| Auth | Client ID + API key/request signature; v1 API-key management | Existing standard-key HMAC authentication is reused. v1 management routes are selected per operation. |
| Pagination | Query parameters such as `page` and `page_size` | All GET option arrays are RFC 3986 query strings. Responses remain raw JSON strings, consistent with the SDK. |
| Async | Receipts body flag plus dedicated AnyDocs/bank/check/parse async routes | Receipts supports `async` through process options; dedicated async methods are public for all documented routes. |
| File upload | JSON URL/base64/S3 modes and multipart `file` | Existing upload helpers remain; classify/split local-file helpers added; all `CURLFile` payloads use multipart. |
| Webhooks | Add/list/confirm settings endpoints | `add_webhook()`, `get_webhooks()`, `confirm_webhook()`, and existing `verify_signature()` are available. |

## Appendix — documented operations inventory

Operation pages were enumerated from the official sitemap. Product landing pages that timed out were not required because each operation has its own URL and OpenAPI method/path heading.

Parser artifacts used while producing this report (not part of the SDK): parameter names for GET query strings and POST/PUT top-level body fields. Nested DetailedField children (`value`, `bounding_box`, …) were excluded from “top-level body” lists except where they are the documented query/path names.

