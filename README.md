# Authproc filter for SSP to get any attribute from rest api
* Author: Gyula Szabó <gyufi@niif.hu>, NIIF Institute, Hungary

This module provides an authprocfilter.

## Install module
You can install the module with composer:

    composer require niif/simplesamlphp-module-attributefromrestapi

### Authproc Filters
The NameID of the request will be in the attribute as defined above. 

```
   authproc.aa = array(
       ...
       '60' => array(
            'class' => 'attributefromrestapi:attributeFromRestApi',
            'nameId_attribute_name' =>  'subject_nameid', // look at the aa authsource config
            'api_url' =>          'https://www.anyrestapi.com/getData',
       ),
```
