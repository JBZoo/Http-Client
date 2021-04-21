<?php

/**
 * JBZoo Toolbox - Http-Client
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Http-Client
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Http-Client
 */

declare(strict_types=1);

namespace JBZoo\HttpClient;

/**
 * Class HttpCodes
 * @package JBZoo\HttpClient
 */
class HttpCodes
{
    /**
     * @var string[]
     */
    private static $phrases = [
        self::CONTINUE            => 'Continue',
        self::SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::PROCESSING          => 'Processing',
        self::EARLY_HINTS         => 'Early Hints',

        self::OK                     => 'OK',
        self::CREATED                => 'Created',
        self::ACCEPTED               => 'Accepted',
        self::NON_AUTHORITATIVE_INFO => 'Non-Authoritative Information',
        self::NO_CONTENT             => 'No Content',
        self::RESET_CONTENT          => 'Reset Content',
        self::PARTIAL_CONTENT        => 'Partial Content',
        self::MULTI_STATUS           => 'Multi-status',
        self::ALREADY_REPORTED       => 'Already Reported',
        self::IM_USED                => 'IM Used',

        self::MULTIPLE_CHOICES   => 'Multiple Choices',
        self::MOVED_PERMANENTLY  => 'Moved Permanently',
        self::FOUND              => 'Found',
        self::SEE_OTHER          => 'See Other',
        self::NOT_MODIFIED       => 'Not Modified',
        self::USE_PROXY          => 'Use Proxy',
        self::SWITCH_PROXY       => 'Switch Proxy',
        self::TEMPORARY_REDIRECT => 'Temporary Redirect',

        self::BAD_REQUEST                     => 'Bad Request',
        self::UNAUTHORIZED                    => 'Unauthorized',
        self::PAYMENT_REQUIRED                => 'Payment Required',
        self::FORBIDDEN                       => 'Forbidden',
        self::NOT_FOUND                       => 'Not Found',
        self::METHOD_NOT_ALLOWED              => 'Method Not Allowed',
        self::NOT_ACCEPTABLE                  => 'Not Acceptable',
        self::PROXY_AUTHENTICATION_REQUIRED   => 'Proxy Authentication Required',
        self::REQUEST_TIMEOUT                 => 'Request Time-out',
        self::CONFLICT                        => 'Conflict',
        self::GONE                            => 'Gone',
        self::LENGTH_REQUIRED                 => 'Length Required',
        self::PRECONDITION_FAILED             => 'Precondition Failed',
        self::REQUEST_ENTITY_TOO_LARGE        => 'Request Entity Too Large',
        self::REQUEST_URI_TOO_LONG            => 'Request-URI Too Large',
        self::UNSUPPORTED_MEDIA_TYPE          => 'Unsupported Media Type',
        self::REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested range not satisfiable',
        self::EXPECTATION_FAILED              => 'Expectation Failed',
        self::IM_A_TEAPOT                     => "I'm a teapot",
        self::UNPROCESSABLE_ENTITY            => 'Unprocessable Entity',
        self::LOCKED                          => 'Locked',
        self::FAILED_DEPENDENCY               => 'Failed Dependency',
        self::UNORDERED_COLLECTION            => 'Unordered Collection',
        self::UPGRADE_REQUIRED                => 'Upgrade Required',
        self::PRECONDITION_REQUIRED           => 'Precondition Required',
        self::TOO_MANY_REQUESTS               => 'Too Many Requests',
        self::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
        self::UNAVAILABLE_FOR_LEGAL_REASONS   => 'Unavailable For Legal Reasons',

        self::INTERNAL_SERVER_ERROR           => 'Internal Server Error',
        self::NOT_IMPLEMENTED                 => 'Not Implemented',
        self::BAD_GATEWAY                     => 'Bad Gateway',
        self::SERVICE_UNAVAILABLE             => 'Service Unavailable',
        self::GATEWAY_TIMEOUT                 => 'Gateway Time-out',
        self::VERSION_NOT_SUPPORTED           => 'HTTP Version not supported',
        self::VARIANT_ALSO_NEGOTIATES         => 'Variant Also Negotiates',
        self::INSUFFICIENT_STORAGE            => 'Insufficient Storage',
        self::LOOP_DETECTED                   => 'Loop Detected',
        self::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
        self::UNKNOWN_ERROR                   => 'Unknown Error',
        self::WEB_SERVER_IS_DOWN              => 'Web Server Is Down',
        self::CONNECTION_TIMED_OUT            => 'Connection Timed Out',
        self::ORIGIN_IS_UNREACHABLE           => 'Origin Is Unreachable',
        self::TIMEOUT_OCCURRED                => 'A Timeout Occurred',
        self::SSL_HANDSHAKE_FAILED            => 'SSL Handshake Failed',
        self::INVALID_SSL_CERTIFICATE         => 'Invalid SSL Certificate',
    ];

    // 1xx informational response – the request was received, continuing process
    public const CONTINUE            = 100;
    public const SWITCHING_PROTOCOLS = 101;
    public const PROCESSING          = 102;
    public const EARLY_HINTS         = 103;

    // 2xx successful – the request was successfully received, understood, and accepted
    public const OK                     = 200;
    public const CREATED                = 201;
    public const ACCEPTED               = 202;
    public const NON_AUTHORITATIVE_INFO = 203;
    public const NO_CONTENT             = 204;
    public const RESET_CONTENT          = 205;
    public const PARTIAL_CONTENT        = 206;
    public const MULTI_STATUS           = 207;
    public const ALREADY_REPORTED       = 208;
    public const IM_USED                = 226;

    // 3xx redirection – further action needs to be taken in order to complete the request
    public const MULTIPLE_CHOICES   = 300;
    public const MOVED_PERMANENTLY  = 301;
    public const FOUND              = 302;
    public const SEE_OTHER          = 303;
    public const NOT_MODIFIED       = 304;
    public const USE_PROXY          = 305;
    public const SWITCH_PROXY       = 306;
    public const TEMPORARY_REDIRECT = 307;

    // 4xx client error – the request contains bad syntax or cannot be fulfilled
    public const BAD_REQUEST                     = 400;
    public const UNAUTHORIZED                    = 401;
    public const PAYMENT_REQUIRED                = 402;
    public const FORBIDDEN                       = 403;
    public const NOT_FOUND                       = 404;
    public const METHOD_NOT_ALLOWED              = 405;
    public const NOT_ACCEPTABLE                  = 406;
    public const PROXY_AUTHENTICATION_REQUIRED   = 407;
    public const REQUEST_TIMEOUT                 = 408;
    public const CONFLICT                        = 409;
    public const GONE                            = 410;
    public const LENGTH_REQUIRED                 = 411;
    public const PRECONDITION_FAILED             = 412;
    public const REQUEST_ENTITY_TOO_LARGE        = 413;
    public const REQUEST_URI_TOO_LONG            = 414;
    public const UNSUPPORTED_MEDIA_TYPE          = 415;
    public const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const EXPECTATION_FAILED              = 417;
    public const IM_A_TEAPOT                     = 418;
    public const UNPROCESSABLE_ENTITY            = 422;
    public const LOCKED                          = 423;
    public const FAILED_DEPENDENCY               = 424;
    public const UNORDERED_COLLECTION            = 425;
    public const UPGRADE_REQUIRED                = 426;
    public const PRECONDITION_REQUIRED           = 428;
    public const TOO_MANY_REQUESTS               = 429;
    public const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const UNAVAILABLE_FOR_LEGAL_REASONS   = 451;
    public const CLIENT_CLOSED_REQUEST           = 499;

    // 5xx server error – the server failed to fulfil an apparently valid request
    public const INTERNAL_SERVER_ERROR           = 500;
    public const NOT_IMPLEMENTED                 = 501;
    public const BAD_GATEWAY                     = 502;
    public const SERVICE_UNAVAILABLE             = 503;
    public const GATEWAY_TIMEOUT                 = 504;
    public const VERSION_NOT_SUPPORTED           = 505;
    public const VARIANT_ALSO_NEGOTIATES         = 506;
    public const INSUFFICIENT_STORAGE            = 507;
    public const LOOP_DETECTED                   = 508;
    public const NETWORK_AUTHENTICATION_REQUIRED = 511;
    public const UNKNOWN_ERROR                   = 520;
    public const WEB_SERVER_IS_DOWN              = 521;
    public const CONNECTION_TIMED_OUT            = 522;
    public const ORIGIN_IS_UNREACHABLE           = 523;
    public const TIMEOUT_OCCURRED                = 524;
    public const SSL_HANDSHAKE_FAILED            = 525;
    public const INVALID_SSL_CERTIFICATE         = 526;

    /**
     * @param int $code
     * @return bool
     */
    public static function isSuccessful(int $code): bool
    {
        return $code >= self::OK && $code < self::MULTIPLE_CHOICES;
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function isRedirect(int $code): bool
    {
        return $code >= self::MULTIPLE_CHOICES && $code < self::BAD_REQUEST;
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function isForbidden(int $code): bool
    {
        return $code === self::FORBIDDEN;
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function isNotFound(int $code): bool
    {
        return $code === self::NOT_FOUND;
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function isUnauthorized(int $code): bool
    {
        return $code === self::UNAUTHORIZED;
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function hasAccess(int $code): bool
    {
        return !self::isForbidden($code) && !self::isUnauthorized($code);
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function isError(int $code): bool
    {
        return $code >= self::BAD_REQUEST;
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function isFatalError(int $code): bool
    {
        return $code >= self::INTERNAL_SERVER_ERROR;
    }

    /**
     * @param int $code
     * @return string|null
     */
    public static function getDescriptionByCode(int $code): ?string
    {
        return self::$phrases[$code] ?? null;
    }
}
