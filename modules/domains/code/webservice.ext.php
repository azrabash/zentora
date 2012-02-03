<?php

/**
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class webservice extends ws_xmws {

    /**
     * Get the full list of currently active domains on the server.
     * @global type $zdbh
     * @return type 
     */
    function GetAllDomains() {
        global $zdbh;
        $response_xml = "\n";
        $alldomains = module_controller::ListDomains();
        foreach ($alldomains as $domain) {
            if ($domain['vh_custom_tx'] == "") {
                $customconf = "NULL";
            } else {
                $customconf = $domain['vh_custom_tx'];
            }

            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('domain', array(
                        'id' => $domain['vh_id_pk'],
                        'uid' => $domain['vh_acc_fk'],
                        'domain' => $domain['vh_name_vc'],
                        'homedirectory' => $domain['vh_directory_vc'],
                        'domaintype' => $domain['vh_type_in'],
                        'active' => $domain['vh_active_in'],
                        'suhosin' => $domain['vh_suhosin_in'],
                        'openbasedir' => $domain['vh_obasedir_in'],
                        'customconfig' => $customconf,
                        'datecreated' => $domain['vh_created_ts'],
                    ));
        }
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

    /**
     * Gets a list of all the domains that a user has configured on their hosting account (the user id needs to be sent in the <content> tag).
     * @global type $zdbh
     * @return type 
     */
    function GetDomainsForUser() {
        global $zdbh;
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "\n";
        $alldomains = module_controller::ListDomains($request_data['content']);
        foreach ($alldomains as $domain) {
            if ($domain['vh_custom_tx'] == "") {
                $customconf = "NULL";
            } else {
                $customconf = $domain['vh_custom_tx'];
            }

            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('domain', array(
                        'id' => $domain['vh_id_pk'],
                        'uid' => $domain['vh_acc_fk'],
                        'domain' => $domain['vh_name_vc'],
                        'homedirectory' => $domain['vh_directory_vc'],
                        'domaintype' => $domain['vh_type_in'],
                        'active' => $domain['vh_active_in'],
                        'suhosin' => $domain['vh_suhosin_in'],
                        'openbasedir' => $domain['vh_obasedir_in'],
                        'customconfig' => $customconf,
                        'datecreated' => $domain['vh_created_ts'],
                    ));
        }

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);

        return $dataobject->getDataObject();
    }

    /**
     * Enables an authenticated user to create a domain on their hosting account.
     * @return type 
     */
    function CreateDomain() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');

        if (module_controller::ExecuteAddDomain($contenttags['uid'], $contenttags['domain'], $contenttags['destination'], $contenttags['autohome'])) {
            $dataobject->addItemValue('content', ws_xmws::NewXMLTag('domain', $contenttags['domain']) . ws_xmws::NewXMLTag('created', 'true'));
        } else {
            $dataobject->addItemValue('content', ws_xmws::NewXMLTag('domain', $contenttags['domain']) . ws_xmws::NewXMLTag('created', 'false'));
        }
        return $dataobject->getDataObject();
    }

    /**
     * Delete a specified domain using the content <domainid> tag to pass the domain DB ID through.
     * @return type 
     */
    function DeleteDomain() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');

        if (module_controller::ExecuteDeleteDomain($contenttags['domainid'])) {
            $dataobject->addItemValue('content', ws_xmws::NewXMLTag('domainid', $contenttags['domainid']) . ws_xmws::NewXMLTag('deleted', 'true'));
        } else {
            $dataobject->addItemValue('content', ws_xmws::NewXMLTag('domainid', $contenttags['domainid']) . ws_xmws::NewXMLTag('deleted', 'false'));
        }
        return $dataobject->getDataObject();
    }

}

?>
