<?php
/**
 * SAML 2.0 remote IdP metadata for SimpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote
 */

if (!empty($_ENV['PANTHEON_ENVIRONMENT']) && ($_ENV['PANTHEON_ENVIRONMENT'] === 'test' || $_ENV['PANTHEON_ENVIRONMENT'] === 'dev')) {
  $metadata['https://testlogin.du.edu/idp'] = array (
    'entityid' => 'https://testlogin.du.edu/idp',
    'description' =>
    array (
      'en' => 'University of Denver',
    ),
    'OrganizationName' =>
    array (
      'en' => 'University of Denver',
    ),
    'name' =>
    array (
      'en' => 'University of Denver',
    ),
    'OrganizationDisplayName' =>
    array (
      'en' => 'University of Denver',
    ),
    'url' =>
    array (
      'en' => 'https://www.du.edu/',
    ),
    'OrganizationURL' =>
    array (
      'en' => 'https://www.du.edu/',
    ),
    'contacts' =>
    array (
      0 =>
      array (
        'contactType' => 'support',
        'givenName' => 'DU SSO Support',
        'emailAddress' =>
        array (
          0 => 'support@du.edu',
        ),
      ),
      1 =>
      array (
        'contactType' => 'technical',
        'givenName' => 'DU SSO Administrators',
        'emailAddress' =>
        array (
          0 => 'IT.Systems@du.edu',
        ),
      ),
    ),
    'metadata-set' => 'saml20-idp-remote',
    'SingleSignOnService' =>
    array (
      0 =>
      array (
        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        'Location' => 'https://testlogin.du.edu/sso/go.ashx',
      ),
      1 =>
      array (
        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        'Location' => 'https://testlogin.du.edu/sso/go.ashx',
      ),
    ),
    'SingleLogoutService' =>
    array (
      0 =>
      array (
        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        'Location' => 'https://testlogin.du.edu/_layouts/pg/signout.aspx',
      ),
      1 =>
      array (
        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        'Location' => 'https://testlogin.du.edu/_layouts/pg/signout.aspx',
      ),
    ),
    'ArtifactResolutionService' =>
    array (
    ),
    'NameIDFormats' =>
    array (
    ),
    'keys' =>
    array (
      0 =>
      array (
        'encryption' => false,
        'signing' => true,
        'type' => 'X509Certificate',
        'X509Certificate' => 'MIIDjDCCAnQCCQCaICRjUr0z0DANBgkqhkiG9w0BAQsFADCBhzELMAkGA1UEChMCRFUxCzAJBgNVBAsTAklUMSAwHgYJKoZIhvcNAQkBFhFyeWFuLnBvd2VyQGR1LmVkdTEPMA0GA1UEBxMGRGVudmVyMREwDwYDVQQIEwhDb2xvcmFkbzELMAkGA1UEBhMCVVMxGDAWBgNVBAMTD3BvcnRhbGd1YXJkLXNzbzAeFw0yMTA5MTUxNjA3MDFaFw0zMTA5MTMxNjA3MDFaMIGHMQswCQYDVQQKEwJEVTELMAkGA1UECxMCSVQxIDAeBgkqhkiG9w0BCQEWEXJ5YW4ucG93ZXJAZHUuZWR1MQ8wDQYDVQQHEwZEZW52ZXIxETAPBgNVBAgTCENvbG9yYWRvMQswCQYDVQQGEwJVUzEYMBYGA1UEAxMPcG9ydGFsZ3VhcmQtc3NvMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnO3x340JOSLYJLQKGPtyKiBnpwXh53k7i60qodfOadGnyeDWdG57dKin0XHI7xcek14Kpq8PrgghEDoTieuppJFgQPLPe01EnMeagsx/saZ6OXM2WDAOGNerVpqe+QODuxzu9joQ8Iqb2RPfb263JqdApN2rptVfpg03NiwdT/9cpNWfPqz9ZrR6A5aKLrKO81gOHCfYobNAn3hrHCZ0spDD4AXXMDXSppVFBwruU7ZChMWQpcJgSiUsk5N47tBW/FJq2G9un9hfnKrTK5vuTATW8Q3YpHmRlaA+76DmhNsoU8+nNuNSmvmisY+DHIXxKXNVEc8w69CJYKEjjxTfGwIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQBgd+j2aF+nn1itj0awFRjRuU9AjpyUYuzb4Cg+EhqQKBFJI9Oo6xs7aoZJXYv0OiAzf4aoFaDW979MKvdKkjZjjI4uBPrM/+KiYF2yu/lDvoqL2YKHwEbF0ZyRHb4JTh3cmuC0KRbpZ7hzO+rlqX0SisVSvRzLopjONb+KL/BUDizEuO2zyLYEPVGtIelh8HFro7bJVtYlhusRV47PjzWu0wZvr/rHO4Qq0Jt0Sg3h9/Ewvh7gxTxFQ+Lq0cGC5oVnYs/RBpRVoDP/gXBklO6zAle6RIr4xpSKa65m+/xpzKcgkzKWnyDZMglXTkOchcGDKTP4By68dUr/0jkDZuVr',
      ),
    ),
    'scope' =>
    array (
      0 => 'du.edu',
    ),
    'RegistrationInfo' =>
    array (
      'registrationAuthority' => 'https://incommon.org',
    ),
    'EntityAttributes' =>
    array (
      'http://macedir.org/entity-category' =>
      array (
        0 => 'http://refeds.org/category/hide-from-discovery',
        1 => 'http://id.incommon.org/category/registered-by-incommon',
      ),
    ),
    'hide.from.discovery' => true,
    'UIInfo' =>
    array (
      'DisplayName' =>
      array (
        'en' => 'University of Denver',
      ),
      'Description' =>
      array (
      ),
      'InformationURL' =>
      array (
      ),
      'PrivacyStatementURL' =>
      array (
        'en' => 'https://www.du.edu/site-utilities/privacy-policy',
      ),
    ),
  );
} elseif (!empty($_ENV['PANTHEON_ENVIRONMENT']) && $_ENV['PANTHEON_ENVIRONMENT'] === 'live') {
  $metadata['https://login.du.edu/idp'] = array (
    'entityid' => 'https://login.du.edu/idp',
    'description' =>
    array (
      'en' => 'University of Denver',
    ),
    'OrganizationName' =>
    array (
      'en' => 'University of Denver',
    ),
    'name' =>
    array (
      'en' => 'University of Denver',
    ),
    'OrganizationDisplayName' =>
    array (
      'en' => 'University of Denver',
    ),
    'url' =>
    array (
      'en' => 'https://www.du.edu/',
    ),
    'OrganizationURL' =>
    array (
      'en' => 'https://www.du.edu/',
    ),
    'contacts' =>
    array (
      0 =>
      array (
        'contactType' => 'support',
        'givenName' => 'DU SSO Support',
        'emailAddress' =>
        array (
          0 => 'support@du.edu',
        ),
      ),
      1 =>
      array (
        'contactType' => 'technical',
        'givenName' => 'DU SSO Administrators',
        'emailAddress' =>
        array (
          0 => 'IT.Systems@du.edu',
        ),
      ),
    ),
    'metadata-set' => 'saml20-idp-remote',
    'SingleSignOnService' =>
    array (
      0 =>
      array (
        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        'Location' => 'https://login.du.edu/sso/go.ashx',
      ),
      1 =>
      array (
        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        'Location' => 'https://login.du.edu/sso/go.ashx',
      ),
    ),
    'SingleLogoutService' =>
    array (
      0 =>
      array (
        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        'Location' => 'https://login.du.edu/_layouts/pg/signout.aspx',
      ),
      1 =>
      array (
        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        'Location' => 'https://login.du.edu/_layouts/pg/signout.aspx',
      ),
    ),
    'ArtifactResolutionService' =>
    array (
    ),
    'NameIDFormats' =>
    array (
    ),
    'keys' =>
    array (
      0 =>
      array (
        'encryption' => false,
        'signing' => true,
        'type' => 'X509Certificate',
        'X509Certificate' => 'MIIDjDCCAnQCCQCaICRjUr0z0DANBgkqhkiG9w0BAQsFADCBhzELMAkGA1UEChMCRFUxCzAJBgNVBAsTAklUMSAwHgYJKoZIhvcNAQkBFhFyeWFuLnBvd2VyQGR1LmVkdTEPMA0GA1UEBxMGRGVudmVyMREwDwYDVQQIEwhDb2xvcmFkbzELMAkGA1UEBhMCVVMxGDAWBgNVBAMTD3BvcnRhbGd1YXJkLXNzbzAeFw0yMTA5MTUxNjA3MDFaFw0zMTA5MTMxNjA3MDFaMIGHMQswCQYDVQQKEwJEVTELMAkGA1UECxMCSVQxIDAeBgkqhkiG9w0BCQEWEXJ5YW4ucG93ZXJAZHUuZWR1MQ8wDQYDVQQHEwZEZW52ZXIxETAPBgNVBAgTCENvbG9yYWRvMQswCQYDVQQGEwJVUzEYMBYGA1UEAxMPcG9ydGFsZ3VhcmQtc3NvMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnO3x340JOSLYJLQKGPtyKiBnpwXh53k7i60qodfOadGnyeDWdG57dKin0XHI7xcek14Kpq8PrgghEDoTieuppJFgQPLPe01EnMeagsx/saZ6OXM2WDAOGNerVpqe+QODuxzu9joQ8Iqb2RPfb263JqdApN2rptVfpg03NiwdT/9cpNWfPqz9ZrR6A5aKLrKO81gOHCfYobNAn3hrHCZ0spDD4AXXMDXSppVFBwruU7ZChMWQpcJgSiUsk5N47tBW/FJq2G9un9hfnKrTK5vuTATW8Q3YpHmRlaA+76DmhNsoU8+nNuNSmvmisY+DHIXxKXNVEc8w69CJYKEjjxTfGwIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQBgd+j2aF+nn1itj0awFRjRuU9AjpyUYuzb4Cg+EhqQKBFJI9Oo6xs7aoZJXYv0OiAzf4aoFaDW979MKvdKkjZjjI4uBPrM/+KiYF2yu/lDvoqL2YKHwEbF0ZyRHb4JTh3cmuC0KRbpZ7hzO+rlqX0SisVSvRzLopjONb+KL/BUDizEuO2zyLYEPVGtIelh8HFro7bJVtYlhusRV47PjzWu0wZvr/rHO4Qq0Jt0Sg3h9/Ewvh7gxTxFQ+Lq0cGC5oVnYs/RBpRVoDP/gXBklO6zAle6RIr4xpSKa65m+/xpzKcgkzKWnyDZMglXTkOchcGDKTP4By68dUr/0jkDZuVr',
      ),
    ),
    'scope' =>
    array (
      0 => 'du.edu',
    ),
    'RegistrationInfo' =>
    array (
      'registrationAuthority' => 'https://incommon.org',
    ),
    'EntityAttributes' =>
    array (
      'http://macedir.org/entity-category' =>
      array (
        0 => 'http://refeds.org/category/hide-from-discovery',
        1 => 'http://id.incommon.org/category/registered-by-incommon',
      ),
    ),
    'hide.from.discovery' => true,
    'UIInfo' =>
    array (
      'DisplayName' =>
      array (
        'en' => 'University of Denver',
      ),
      'Description' =>
      array (
      ),
      'InformationURL' =>
      array (
      ),
      'PrivacyStatementURL' =>
      array (
        'en' => 'https://www.du.edu/site-utilities/privacy-policy',
      ),
    ),
  );
}
