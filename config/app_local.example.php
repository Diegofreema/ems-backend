<?php

use function Cake\Core\env;

/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],

    /*
     * API / JWT configuration.
     * - secret: signing key for JWTs. MUST be long (>= 32 chars) & random.
     *   Supply a real value here or via the JWT_SECRET env var — the shipped
     *   '__JWT_SECRET__' placeholder is REJECTED by App\Api\Jwt, so a copy of
     *   this file that is not filled in refuses to sign tokens rather than
     *   running on a publicly-known key. Generate one with: openssl rand -hex 32
     * - accessTtl / refreshTtl: token lifetimes in seconds.
     * - issuer: identifies tokens issued by this app.
     */
    'Jwt' => [
        'secret' => env('JWT_SECRET', '__JWT_SECRET__'),
        'accessTtl' => 900,           // 15 minutes — short-lived, held only in browser memory
        'refreshTtl' => 60 * 60 * 24 * 14, // 14 days — rotating, httpOnly-cookie only
        'issuer' => 'ltalms-api',
    ],

    /*
     * EMS surface tuning (§ security candidates).
     * - frontendBaseUrl: the one public React URL. Invitation links and the
     *   default CORS allow list both derive from EMS_FRONTEND_URL.
     * - corsOrigins: browser origins allowed to make CREDENTIALED requests (the
     *   refresh cookie rides on these). NEVER '*' — credentials + wildcard is
     *   both rejected by browsers and a CSRF footgun. Add your production SPA
     *   origin here; the Vite dev server is http://localhost:5173.
     * - cookieSecure: send the refresh cookie only over HTTPS. Safe to leave on
     *   in local dev — browsers exempt http://localhost from the Secure rule.
     * - trustProxy: read the client IP from X-Forwarded-For (only enable behind
     *   a trusted reverse proxy; a spoofable XFF would bypass every throttle).
     */
    'Ems' => [
        'frontendBaseUrl' => rtrim((string)env('EMS_FRONTEND_URL', 'http://localhost:5173'), '/'),
        'corsOrigins' => array_values(array_filter(array_map(
            static function (string $origin): string {
                return rtrim(trim($origin), '/');
            },
            explode(',', (string)env(
                'EMS_CORS_ORIGINS',
                env('EMS_FRONTEND_URL', 'http://localhost:5173')
            ))
        ))),
        'cookieSecure' => filter_var(env('EMS_COOKIE_SECURE', true), FILTER_VALIDATE_BOOLEAN),
        'trustProxy' => filter_var(env('EMS_TRUST_PROXY', false), FILTER_VALIDATE_BOOLEAN),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => [
            'host' => 'localhost',
            /*
             * CakePHP will use the default DB port based on the driver selected
             * MySQL on MAMP uses port 8889, MAMP users will want to uncomment
             * the following line and set the port accordingly
             */
            //'port' => 'non_standard_port_number',

            'username' => 'my_app',
            'password' => 'secret',

            'database' => 'my_app',
            /**
             * If not using the default 'public' schema with the PostgreSQL driver
             * set it here.
             */
            //'schema' => 'myapp',

            /**
             * You can use a DSN string to set the entire configuration
             */
            'url' => env('DATABASE_URL', null),
        ],

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'host' => 'localhost',
            //'port' => 'non_standard_port_number',
            'username' => 'my_app',
            'password' => 'secret',
            'database' => 'test_myapp',
            //'schema' => 'myapp',
        ],
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],
];
