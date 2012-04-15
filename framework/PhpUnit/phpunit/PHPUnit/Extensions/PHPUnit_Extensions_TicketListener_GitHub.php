<?php
require_once('TicketListener.php');

/**
 * A ticket listener that interacts with the GitHub issue API.
 */
class PHPUnit_Extensions_TicketListener_GitHub extends 
    PHPUnit_Extensions_TicketListener
{
    const STATUS_CLOSE = 'closed';
    const STATUS_REOPEN = 'reopened';
    
    private $_username = null;
    private $_apiToken = null;
    private $_repository = null;
    private $_apiPath = null;
    private $_printTicketStateChanges = false;
    
    /**
     * @param string $username   The username associated with the GitHub account.
     * @param string $apiToken   The API token associated with the GitHub account.
     * @param string $repository The repository of the system under test (SUT) on GitHub.
     * @param string $printTicketChanges Boolean flag to print the ticket state 
     * changes in the test result.
     * @throws RuntimeException
     */
    public function __construct($username, $apiToken, $repository, 
        $printTicketStateChanges = false)
    {
        if ($this->_isCurlAvailable() === false) {
            throw new RuntimeException('The dependent curl extension is not available');
        }
        if ($this->_isJsonAvailable() === false) {
            throw new RuntimeException('The dependent json extension is not available');
        }
        $this->_username = $username;
        $this->_apiToken = $apiToken;
        $this->_repository = $repository;
        $this->_apiPath = 'http://github.com/api/v2/json/issues';
        $this->_printTicketStateChanges = $printTicketStateChanges;
    }
    
    /**
     * @param  integer $ticketId 
     * @return string
     * @throws PHPUnit_Framework_Exception
     */
    public function getTicketInfo($ticketId = null) 
    {
        if (!ctype_digit($ticketId)) {
            return $ticketInfo = array('status' => 'invalid_ticket_id');
        }                
        $ticketInfo = array();
        
        $apiEndpoint = "{$this->_apiPath}/show/{$this->_username}/"
            . "{$this->_repository}/{$ticketId}";
            
        $issueProperties = $this->_callGitHubIssueApiWithEndpoint($apiEndpoint, true);

        if ($issueProperties['state'] === 'open') {
            return $ticketInfo = array('status' => 'new');
        } elseif ($issueProperties['state'] === 'closed') {
            return $ticketInfo = array('status' => 'closed');
        } elseif ($issueProperties['state'] === 'unknown_ticket') {
            return $ticketInfo = array('status' => $issueProperties['state']);
        }
    }

    /**
     * @param string $ticketId   The ticket number of the ticket under test (TUT).
     * @param string $statusToBe The status of the TUT after running the associated test.
     * @param string $message    The additional message for the TUT.
     * @param string $resolution The resolution for the TUT.
     * @throws PHPUnit_Framework_Exception
     */
    protected function updateTicket($ticketId, $statusToBe, $message, $resolution)
    {
        $apiEndpoint = null;
        $acceptedResponseIssueStates = array('open', 'closed');
        
        if ($statusToBe === self::STATUS_CLOSE) {
            $apiEndpoint = "{$this->_apiPath}/close/{$this->_username}/"
                . "{$this->_repository}/{$ticketId}";
        } elseif ($statusToBe === self::STATUS_REOPEN) {
            $apiEndpoint = "{$this->_apiPath}/reopen/{$this->_username}/"
                . "{$this->_repository}/{$ticketId}";
        }
        if (!is_null($apiEndpoint)) {
            $issueProperties = $this->_callGitHubIssueApiWithEndpoint($apiEndpoint);
            if (!in_array($issueProperties['state'], $acceptedResponseIssueStates)) {
                throw new PHPUnit_Framework_Exception(
                    'Recieved an unaccepted issue state from the GitHub Api');
            }
            if ($this->_printTicketStateChanges) {
                printf("\nUpdating GitHub issue #%d, status: %s\n", $ticketId, 
                    $statusToBe);
            }
        }
    }

    /**
     * @return boolean 
     */
    private function _isCurlAvailable()
    {
        return extension_loaded('curl');
    }

    /**
     * @return boolean 
     */
    private function _isJsonAvailable()
    {
        return extension_loaded('json');
    }

    /**
     * @param string  $apiEndpoint API endpoint to call against the GitHub issue API.
     * @param boolean $isShowMethodCall Show method of the GitHub issue API is called? 
     * @return array
     * @throws PHPUnit_Framework_Exception
     */
    private function _callGitHubIssueApiWithEndpoint($apiEndpoint, 
        $isShowMethodCall = false) 
    {
            $curlHandle = curl_init();

            curl_setopt($curlHandle, CURLOPT_URL, $apiEndpoint);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
            curl_setopt($curlHandle, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandle, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($curlHandle, CURLOPT_USERAGENT, __CLASS__);  
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS,
                "login={$this->_username}&token={$this->_apiToken}");

            $response = curl_exec($curlHandle);
            
            // Unknown tickets throw a 403 error
            if (!$response && (isset($isGetTicketInfoCall) && $isGetTicketInfoCall) ) {
                return array('state' => 'unknown_ticket');
            }

            if (!$response) {
                $curlErrorMessage = curl_error($curlHandle);
                $exceptionMessage = "A failure occured while talking to the "
                    . "GitHub issue Api. {$curlErrorMessage}.";
                throw new PHPUnit_Framework_Exception($exceptionMessage);
            }
            $issue = (array) json_decode($response);
            $issueProperties = (array) $issue['issue'];
            curl_close($curlHandle);
            return $issueProperties;
    }
}
