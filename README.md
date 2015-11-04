# Authproc filter for SSP to get any attribute from rest api
* Author: Gyula Szabó <gyufi@niif.hu>, NIIF Institute, Hungary

This module provides an authprocfilter, that get attributes from rest api in json format.

## Install module
You can install the module with composer:

    composer require niif/simplesamlphp-module-attributefromrestapi

### Authproc Filters
The NameID of the request will be in the attribute as defined above. For example eduPersonPrincipalName. If this nameId is not in the users's attributes there will be shown an exception page, and the authentication process will be stopped.

_config/config.php_

```
   authproc.sp = array(
       ...
       '60' => array(
            'class' => 'attributefromrestapi:AttributeFromRestApi',
            'nameId_attribute_name' =>  'subject_nameid', // look at the aa authsource config
            'api_url' =>          'https://www.anyrestapi.com/getData',
       ),
```
