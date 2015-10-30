<?php

/**
 * AttributeFromRestApi authproc filter.
 *
 * This class is the authproc filter to get attributes from rest api
 *
 * Example configuration in the config/config.php
 *
 *    authproc.aa = array(
 *       ...
 *       '60' => array(
 *            'class' => 'attributefromrestapi:AttributeFromRestApi',
 *            'nameId_attribute_name' =>  'subject_nameid', // look at the aa authsource config
 *            'api_url' =>          'https://www.example.com/resource',
 *       ),
 *
 * @author Gyula Szabó <gyufi@niif.hu>
 */
class sspmod_attributefromrestapi_Auth_Process_AttributeFromRestApi extends SimpleSAML_Auth_ProcessingFilter
{
    private $config;
    private $as_config;

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);
        $params = array('api_url', 'nameId_attribute_name');
        foreach ($params as $param) {
            if (!array_key_exists($param, $config)) {
                throw new SimpleSAML_Error_Exception('Missing required attribute: '.$param);
            }
            $this->as_config[$param] = $config[$param];
        }
    }

    public function process(&$state)
    {
        assert('is_array($state)');
        if (! array_key_exists($this->as_config['nameId_attribute_name'], $state['Attributes'])) {
            throw new SimpleSAML_Error_Exception(
                "Configuration error: There is no attribute named: "
                .$this->as_config['nameId_attribute_name']
                ." in state['Attributes'] array."
            );
        }
        $nameId = $state['Attributes'][$this->as_config['nameId_attribute_name']][0];
        $this->config = SimpleSAML_Configuration::getInstance();
        $new_attributes = $this->getAttributes($nameId);
        $state['Attributes'][key($new_attributes)][0] = $new_attributes[key($new_attributes)];
    }

    public function getAttributes($nameId, $attributes = array())
    {

        // Set up config
        $config = $this->config;
        $retarray = array();

        // Make the call

        // Setup cURL
        $url = $this->as_config['api_url'].'/'.$nameId;
        $ch = curl_init($url);
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            )
        );

        // Send the request
        $response = curl_exec($ch);
        $http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for error; not even redirects are allowed here
        if ($http_response == 507) {
            throw new SimpleSAML_Error_Exception("Out of resources: " . $response);
        } elseif ($response === false || !($http_response >= 200 && $http_response < 300)) {
            SimpleSAML_Logger::error('[afra] API query failed: HTTP response code: '.$http_response.', curl error: "'.curl_error($ch)).'"';
            SimpleSAML_Logger::debug('[afra] API query failed: curl info: '.var_export(curl_getinfo($ch), 1));
            SimpleSAML_Logger::debug('[afra] API query failed: HTTP response: '.var_export($response, 1));
            throw new SimpleSAML_Error_Exception("Error at REST API response: ". $response . $http_response);
        } else {
            $data = json_decode($response, true);
            SimpleSAML_Logger::info('[afra] got reply from API');
            SimpleSAML_Logger::debug('[afra] API query url: '.var_export($url, true));
            SimpleSAML_Logger::debug('[afra] API query result: '.var_export($data, true));
        }
        $attributes = $data['data'];
        return $attributes;
    }
}
